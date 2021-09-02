<?php

namespace Be\Response;

use Be\Be;


/**
 * Class Driver
 * @package Be\System\Response
 */
abstract class Driver
{

    const STATUS = array(
        100 => "Continue",
        101 => "Switching Protocols",
        200 => "OK",
        201 => "Created",
        202 => "Accepted",
        203 => "Non-Authoritative Information",
        204 => "No Content",
        205 => "Reset Content",
        206 => "Partial Content",
        300 => "Multiple Choices",
        301 => "Moved Permanently",
        302 => "Found",
        303 => "See Other",
        304 => "Not Modified",
        305 => "Use Proxy",
        307 => "Temporary Redirect",
        400 => "Bad Request",
        401 => "Unauthorized",
        402 => "Payment Required",
        403 => "Forbidden",
        404 => "Not Found",
        405 => "Method Not Allowed",
        406 => "Not Acceptable",
        407 => "Proxy Authentication Required",
        408 => "Request Time-out",
        409 => "Conflict",
        410 => "Gone",
        411 => "Length Required",
        412 => "Precondition Failed",
        413 => "Request Entity Too Large",
        414 => "Request-URI Too Large",
        415 => "Unsupported Media Type",
        416 => "Requested range not satisfiable",
        417 => "Expectation Failed",
        500 => "Internal Server Error",
        501 => "Not Implemented",
        502 => "Bad Gateway",
        503 => "Service Unavailable",
        504 => "Gateway Time-out"
    );

    protected $data = []; // 暂存数据

    /**
     * 请求状态
     *
     * @param int $code 状态码
     * @param string $message 状态信息
     */
    abstract function status(int $code = 302, string $message = '');

    /**
     * 请求重定向
     *
     * @param string $url 跳转网址
     * @param int $code 状态码
     */
    abstract function redirect(string $url, int $code = 302);

    /**
     * 设置暂存数据
     * @param string $name 名称
     * @param mixed $value 值 (可以是数组或对象)
     */
    public function set(string $name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * 获取暂存数据
     *
     * @param string $name 名称
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        if (isset($this->data[$name])) return $this->data[$name];
        return $default;
    }

    /**
     * 输出头信息
     *
     * @param string $key
     * @param string $val
     */
    abstract function header(string $key, string $val);

    /**
     * 输出内容
     *
     * @param string $string
     */
    abstract function write(string $string);

    /**
     * 以 JSON 输出暂存数据
     */
    abstract function json($data = null);

    /**
     * 结束输出
     *
     * @param string $string 输出内空
     */
    abstract function end(string $string = '');

    /**
     * 设置 Cookie
     *
     * @param string $key
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     */
    abstract function cookie(string $key, string $value = '', int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httpOnly = false);


    /**
     * 成功
     *
     * @param string $message 消息
     * @param string $redirectUrl 跳转网址
     * @param int $redirectTimeout 跳转超时时长
     */
    public function success(string $message, string $redirectUrl = null, int $redirectTimeout = 3)
    {
        $this->set('success', true);
        $this->set('message', $message);

        if ($redirectUrl !== null) {
            $this->set('redirectUrl', $redirectUrl);
            if ($redirectTimeout > 0) $this->set('redirectTimeout', $redirectTimeout);
        }
    
        $request = Be::getRequest();
        if ($request->isAjax()) {
            $this->json();
        } else {
            if ($request->isAdmin()) {
                $this->display('App.System.Admin.System.success', 'Blank');
            } else {
                $this->display('App.System.System.success', 'Blank');
            }
        }
    }

    /**
     * 失败
     *
     * @param string $message 消息
     * @param string $redirectUrl 跳转网址
     * @param int $redirectTimeout 跳转超时时长
     */
    public function error(string $message, string $redirectUrl = null, int $redirectTimeout = 3)
    {
        $this->set('success', false);
        $this->set('message', $message);

        if ($redirectUrl !== null) {
            $this->set('redirectUrl', $redirectUrl);
            if ($redirectTimeout > 0) $this->set('redirectTimeout', $redirectTimeout);
        }

        $request = Be::getRequest();
        if ($request->isAjax()) {
            $this->json();
        } else {
            if ($request->isAdmin()) {
                $this->display('App.System.Admin.System.error', 'Blank');
            } else {
                $this->display('App.System.System.error', 'Blank');
            }
        }
    }

    /**
     * 系统异常
     *
     * @param \Throwable $e 错误码
     */
    public function exception(\Throwable $e)
    {
        $request = Be::getRequest();
        if ($request->isAjax()) {
            $this->set('success', false);
            $this->set('message', $e->getMessage());
            // $this->set('trace', $e->getTrace());
            $this->set('code', $e->getCode());
            $this->json();
        } else {
            $this->set('e', $e);

            if ($request->isAdmin()) {
                $this->display('App.System.Admin.System.exception', 'Blank');
            } else {
                $this->display('App.System.System.exception', 'Blank');
            }
        }
    }

    /**
     * 记录历史节点
     *
     * @param string $historyKey 历史节点键名
     */
    public function createHistory(string $historyKey = null)
    {
        $request = Be::getRequest();
        if ($historyKey === null) {
            $historyKey = $request->getAppName() . '.' . $request->getControllerName();
        }

        $session = Be::getSession();
        $session->set('_history_url_'.$historyKey, $request->server('REQUEST_URI'));
        $session->set('_history_post_'.$historyKey, serialize($request->post()));
    }

    /**
     * 成功
     *
     * @param string $message 消息
     * @param string $historyKey 历史节点键名
     * @param int $redirectTimeout 跳转超时时长
     */
    public function successAndBack(string $message, string $historyKey = null, int $redirectTimeout = 3)
    {
        $request = Be::getRequest();
        if ($historyKey === null) {
            $historyKey = $request->getAppName() . '.' . $request->getControllerName();
        }

        $this->set('success', true);
        $this->set('message', $message);
        $this->set('historyKey', $historyKey);

        $session = Be::getSession();
        $historyUrl = null;
        if ($session->has('_history_url_'.$historyKey)) {
            $historyUrl = $session->get('_history_url_'.$historyKey);
        }
        if (!$historyUrl) $historyUrl = $request->server('HTTP_REFERER');
        if (!$historyUrl) $historyUrl = './';

        $historyPost = null;
        if ($session->has('_history_post_'.$historyKey)) {
            $historyPost = $session->get('_history_post_'.$historyKey);
            if ($historyPost) $historyPost = unserialize($historyPost);
        }

        $this->set('historyUrl', $historyUrl);
        $this->set('historyPost', $historyPost);
        $this->set('redirectTimeout', $redirectTimeout);

        if ($request->isAdmin()) {
            $this->display('App.System.Admin.System.successAndBack', 'Blank');
        } else {
            $this->display('App.System.System.successAndBack', 'Blank');
        }
    }

    /**
     * 失败
     *
     * @param string $message 消息
     * @param string $historyKey 历史节点键名
     * @param int $redirectTimeout 跳转超时时长
     */
    public function errorAndBack(string $message, string $historyKey = null, int $redirectTimeout = 3)
    {
        $request = Be::getRequest();
        if ($historyKey === null) {
            $historyKey = $request->getAppName() . '.' . $request->getControllerName();
        }

        $this->set('success', false);
        $this->set('message', $message);
        $this->set('historyKey', $historyKey);

        $session = Be::getSession();
        $historyUrl = null;
        if ($session->has('_history_url_'.$historyKey)) {
            $historyUrl = $session->get('_history_url_'.$historyKey);
        }
        if (!$historyUrl) $historyUrl = $request->server('HTTP_REFERER');
        if (!$historyUrl) $historyUrl = './';

        $historyPost = null;
        if ($session->has('_history_post_'.$historyKey)) {
            $historyPost = $session->get('_history_post_'.$historyKey);
            if ($historyPost) $historyPost = unserialize($historyPost);
        }

        $this->set('historyUrl', $historyUrl);
        $this->set('historyPost', $historyPost);
        $this->set('redirectTimeout', $redirectTimeout);

        if ($request->isAdmin()) {
            $this->display('App.System.Admin.System.errorAndBack', 'Blank');
        } else {
            $this->display('App.System.System.errorAndBack', 'Blank');
        }
    }

    /**
     * 显示模板
     *
     * @param string $template 模板名
     * @param string $theme 主题名
     */
    abstract function display(string $template = null, string $theme = null);

    /**
     * 获取模板内容
     *
     * @param string $template 模板名
     * @param string $theme 主题名
     * @return  string
     */
    public function fetch(string $template, string $theme = null) {
        ob_start();
        ob_clean();
        $templateInstance = Be::getRequest()->isAdmin() ? Be::getAdminTemplate($template, $theme) : Be::getTemplate($template, $theme);
        foreach ($this->data as $key => $val) {
            $templateInstance->$key = $val;
        }
        $templateInstance->display();
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }


    /**
     * 获取原生 Response 对像，仅适用 swoole 模式下
     *
     * @return null | \Swoole\Http\Response
     */
    public function getResponse()
    {
        return null;
    }

}
