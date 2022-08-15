<?php

namespace Be\Response;

use Be\Be;


/**
 * Class Driver
 * @package Be\Response
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
     * 输出内容并结束响应
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
     * @param array $redirect 跳转
     */
    public function success(string $message, array $redirect = null)
    {
        $request = Be::getRequest();
        if ($request->isAjax()) {
            $this->set('success', true);
            $this->set('message', $message);
            if ($redirect !== null && isset($redirect['url'])) {
                $this->set('redirectUrl', $redirect['url']);
            }

            $this->json();
        } else {
            if ($redirect !== null && isset($redirect['url'])) {
                if (isset($redirect['timeout'])) {
                    if ($redirect['timeout'] === 0) {
                        $this->redirect($redirect['url']);
                        return;
                    }
                } else {
                    $redirect['timeout'] = 3;
                }

                $this->set('redirect', $redirect);
            }

            $this->set('message', $message);

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
     * @param array $redirect 跳转
     */
    public function error(string $message, array $redirect = null)
    {
        $request = Be::getRequest();
        if ($request->isAjax()) {
            $this->set('success', false);
            $this->set('message', $message);
            if ($redirect !== null && isset($redirect['url'])) {
                $this->set('redirectUrl', $redirect['url']);
            }

            $this->json();
        } else {
            if ($redirect !== null && isset($redirect['url'])) {
                if (isset($redirect['timeout'])) {
                    if ($redirect['timeout'] === 0) {
                        $this->redirect($redirect['url']);
                        return;
                    }
                } else {
                    $redirect['timeout'] = 3;
                }
                $this->set('redirect', $redirect);
            }

            $this->set('message', $message);

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
        $session->set('be-historyUrl-' . $historyKey, $request->getUrl());
        $session->set('be-historyPostData-' . $historyKey, serialize($request->post()));
    }

    /**
     * 成功
     *
     * @param string $message 消息
     * @param string $historyKey 历史节点键名
     * @param array $redirect 跳转参数，其中 url 参数无效
     */
    public function successAndBack(string $message, string $historyKey = null, array $redirect = [])
    {
        $request = Be::getRequest();
        if ($historyKey === null) {
            $historyKey = $request->getAppName() . '.' . $request->getControllerName();
        }

        $this->set('message', $message);
        $this->set('historyKey', $historyKey);

        $session = Be::getSession();
        $historyUrl = null;
        if ($session->has('be-historyUrl-' . $historyKey)) {
            $historyUrl = $session->get('be-historyUrl-' . $historyKey);
        }
        if (!$historyUrl) $historyUrl = $request->getReferer();
        if (!$historyUrl) $historyUrl = './';

        $this->set('historyUrl', $historyUrl);

        $historyPostData = null;
        if ($session->has('be-historyPostData-' . $historyKey)) {
            $historyPostData = $session->get('be-historyPostData-' . $historyKey);
            if ($historyPostData) $historyPostData = unserialize($historyPostData);
        }

        $this->set('historyPostData', $historyPostData);

        if (!isset($redirect['timeout'])) {
            $redirect['timeout'] = 3;
        }

        $this->set('redirect', $redirect);

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
     * @param array $redirect 跳转参数，其中 url 参数无效
     */
    public function errorAndBack(string $message, string $historyKey = null, array $redirect = [])
    {
        $request = Be::getRequest();
        if ($historyKey === null) {
            $historyKey = $request->getAppName() . '.' . $request->getControllerName();
        }

        $this->set('message', $message);
        $this->set('historyKey', $historyKey);

        $session = Be::getSession();
        $historyUrl = null;
        if ($session->has('be-historyUrl-' . $historyKey)) {
            $historyUrl = $session->get('be-historyUrl-' . $historyKey);
        }
        if (!$historyUrl) $historyUrl = $request->getReferer();
        if (!$historyUrl) $historyUrl = './';

        $this->set('historyUrl', $historyUrl);

        $historyPostData = null;
        if ($session->has('be-historyPostData-' . $historyKey)) {
            $historyPostData = $session->get('be-historyPostData-' . $historyKey);
            if ($historyPostData) $historyPostData = unserialize($historyPostData);
        }

        $this->set('historyPostData', $historyPostData);

        if (!isset($redirect['timeout'])) {
            $redirect['timeout'] = 3;
        }

        $this->set('redirect', $redirect);

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
     * @param string $templateName 模板名
     * @param string $themeName 主题名
     * @return  string
     */
    public function fetch(string $templateName, string $themeName = null)
    {
        $request = Be::getRequest();

        if ($themeName === null) {
            $themeName = $request->getThemeName();
        }

        ob_start();
        ob_clean();
        $templateInstance = $request->isAdmin() ? Be::getAdminTemplate($templateName, $themeName) : Be::getTemplate($templateName, $themeName);
        foreach ($this->data as $key => $val) {
            $templateInstance->$key = $val;
        }

        if (!isset($this->data['_page'])) {
            $templateInstance->_page = Be::getService('App.System.Theme')->getPage($request->isAdmin() ? 'AdminTheme' : 'Theme', $themeName, $request->getRoute());
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
