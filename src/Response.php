<?php

namespace Be;

/**
 * Response 静态快速访问类
 *
 * Class Response
 *
 * @package Be
 */
class Response
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

    protected static $data = []; // 暂存数据
    
    /**
     * 请求状态
     *
     * @param int $code 状态码
     * @param string $message 状态信息
     */
    public static function status(int $code = 302, string $message = '')
    {
        if (!$message) {
            if (isset(self::STATUS[$code])) {
                $message = self::STATUS[$code];
            }
        }

        header('HTTP/1.1 ' . $code . ' ' . $message);
    }

    /**
     * 请求重定向
     *
     * @param string $url 跳转网址
     * @param int $code 状态码
     */
    public static function redirect(string $url, int $code = 302)
    {
        header('location:' . $url, true, $code);
    }

    /**
     * 输出头信息
     *
     * @param string $key
     * @param string $val
     */
    public static function header(string $key, string $val)
    {
        header($key . ':' . $val);
    }

    /**
     * 输出内容
     *
     * @param string $string
     */
    public static function write(string $string)
    {
        echo $string;
    }

    /**
     * 以 JSON 输出暂存数据
     */
    public static function json($data = null, int $flags = JSON_UNESCAPED_UNICODE, int $depth = 512)
    {
        header('Content-type: application/json');
        if ($data === null) {
            echo json_encode(self::$data, $flags, $depth);
        } else {
            echo json_encode(array_merge(self::$data, $data), $flags, $depth);
        }
    }

    /**
     * 输出内容并结束响应
     *
     * @param string $string 输出内空
     */
    public static function end(string $string = '')
    {
        if ($string) {
            echo $string;
        }
    }

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
    public static function cookie(string $key, string $value = '', int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httpOnly = false)
    {
        setcookie($key, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    /**
     * 显示模板
     *
     * @param string|null $templateName 模板名
     * @param string|null $themeName 主题名
     */
    public static function display(string $templateName = null, string $themeName = null)
    {
        if ($templateName === null) {
            if (Request::isAdmin()) {
                $templateName = 'App.' . Request::getAppName() . '.Admin.' . Request::getControllerName() . '.' . Request::getActionName();
            } else {
                $templateName = 'App.' . Request::getRoute();
            }
        }

        if ($themeName === null) {
            $themeName = Request::getThemeName();
        }

        $templateInstance = Request::isAdmin() ? Be::getAdminTemplate($templateName, $themeName) : Be::getTemplate($templateName, $themeName);
        foreach (self::$data as $key => $val) {
            $templateInstance->$key = $val;
        }

        if (!isset(self::$data['pageConfig'])) {
            $route = Request::getRoute();
            if ($route !== null) {
                $templateInstance->pageConfig = Be::getService('App.System.Theme')->getPageConfig(Request::isAdmin() ? 'AdminTheme' : 'Theme', $themeName, $route);
            }
        }

        $templateInstance->display();
    }

    
    /**
     * 设置暂存数据
     * @param string $name 名称
     * @param mixed $value 值 (可以是数组或对象)
     */
    public static function set(string $name, $value)
    {
        self::$data[$name] = $value;
    }

    /**
     * 获取暂存数据
     *
     * @param string $name 名称
     * @return mixed
     */
    public static function get(string $name, $default = null)
    {
        if (isset(self::$data[$name])) return self::$data[$name];
        return $default;
    }

    /**
     * 成功
     *
     * @param string $message 消息
     * @param array $redirect 跳转
     */
    public static function success(string $message, array $redirect = null)
    {
        if (Request::isAjax()) {
            self::set('success', true);
            self::set('message', $message);
            if ($redirect !== null && isset($redirect['url'])) {
                self::set('redirectUrl', $redirect['url']);
            }

            self::json();
        } else {
            if ($redirect !== null && isset($redirect['url'])) {
                if (isset($redirect['timeout'])) {
                    if ($redirect['timeout'] === 0) {
                        self::$redirect($redirect['url']);
                        return;
                    }
                } else {
                    $redirect['timeout'] = 3;
                }

                self::set('redirect', $redirect);
            }

            self::set('message', $message);

            if (Request::isAdmin()) {
                self::display('App.System.Admin.System.success', 'Blank');
            } else {
                self::display('App.System.System.success', 'Blank');
            }
        }
    }

    /**
     * 失败
     *
     * @param string $message 消息
     * @param array $redirect 跳转
     */
    public static function error(string $message, array $redirect = null)
    {
        if (Request::isAjax()) {
            self::set('success', false);
            self::set('message', $message);
            if ($redirect !== null && isset($redirect['url'])) {
                self::set('redirectUrl', $redirect['url']);
            }

            self::json();
        } else {
            if ($redirect !== null && isset($redirect['url'])) {
                if (isset($redirect['timeout'])) {
                    if ($redirect['timeout'] === 0) {
                        self::$redirect($redirect['url']);
                        return;
                    }
                } else {
                    $redirect['timeout'] = 3;
                }
                self::set('redirect', $redirect);
            }

            self::set('message', $message);

            if (Request::isAdmin()) {
                self::display('App.System.Admin.System.error', 'Blank');
            } else {
                self::display('App.System.System.error', 'Blank');
            }
        }
    }

    /**
     * 系统异常
     *
     * @param \Throwable $e 错误码
     */
    public static function exception(\Throwable $e)
    {
        if (Request::isAjax()) {
            self::set('success', false);
            self::set('message', $e->getMessage());
            // self::set('trace', $e->getTrace());
            self::set('code', $e->getCode());
            self::json();
        } else {
            self::set('e', $e);

            if (Request::isAdmin()) {
                self::display('App.System.Admin.System.exception', 'Blank');
            } else {
                self::display('App.System.System.exception', 'Blank');
            }
        }
    }

    /**
     * 记录历史节点
     *
     * @param string $historyKey 历史节点键名
     */
    public static function createHistory(string $historyKey = null)
    {
        if ($historyKey === null) {
            $historyKey = Request::getAppName() . '.' . Request::getControllerName();
        }

        $session = Be::getSession();
        $session->set('be-historyUrl-' . $historyKey, Request::getUrl());
        $session->set('be-historyPostData-' . $historyKey, serialize(Request::post()));
    }

    /**
     * 成功
     *
     * @param string $message 消息
     * @param string $historyKey 历史节点键名
     * @param array $redirect 跳转参数，其中 url 参数无效
     */
    public static function successAndBack(string $message, string $historyKey = null, array $redirect = [])
    {
        if ($historyKey === null) {
            $historyKey = Request::getAppName() . '.' . Request::getControllerName();
        }

        self::set('message', $message);
        self::set('historyKey', $historyKey);

        $session = Be::getSession();
        $historyUrl = null;
        if ($session->has('be-historyUrl-' . $historyKey)) {
            $historyUrl = $session->get('be-historyUrl-' . $historyKey);
        }
        if (!$historyUrl) $historyUrl = Request::getReferer();
        if (!$historyUrl) $historyUrl = './';

        self::set('historyUrl', $historyUrl);

        $historyPostData = null;
        if ($session->has('be-historyPostData-' . $historyKey)) {
            $historyPostData = $session->get('be-historyPostData-' . $historyKey);
            if ($historyPostData) $historyPostData = unserialize($historyPostData);
        }

        self::set('historyPostData', $historyPostData);

        if (!isset($redirect['timeout'])) {
            $redirect['timeout'] = 3;
        }

        self::set('redirect', $redirect);

        if (Request::isAdmin()) {
            self::display('App.System.Admin.System.successAndBack', 'Blank');
        } else {
            self::display('App.System.System.successAndBack', 'Blank');
        }
    }

    /**
     * 失败
     *
     * @param string $message 消息
     * @param string $historyKey 历史节点键名
     * @param array $redirect 跳转参数，其中 url 参数无效
     */
    public static function errorAndBack(string $message, string $historyKey = null, array $redirect = [])
    {
        if ($historyKey === null) {
            $historyKey = Request::getAppName() . '.' . Request::getControllerName();
        }

        self::set('message', $message);
        self::set('historyKey', $historyKey);

        $session = Be::getSession();
        $historyUrl = null;
        if ($session->has('be-historyUrl-' . $historyKey)) {
            $historyUrl = $session->get('be-historyUrl-' . $historyKey);
        }
        if (!$historyUrl) $historyUrl = Request::getReferer();
        if (!$historyUrl) $historyUrl = './';

        self::set('historyUrl', $historyUrl);

        $historyPostData = null;
        if ($session->has('be-historyPostData-' . $historyKey)) {
            $historyPostData = $session->get('be-historyPostData-' . $historyKey);
            if ($historyPostData) $historyPostData = unserialize($historyPostData);
        }

        self::set('historyPostData', $historyPostData);

        if (!isset($redirect['timeout'])) {
            $redirect['timeout'] = 3;
        }

        self::set('redirect', $redirect);

        if (Request::isAdmin()) {
            self::display('App.System.Admin.System.errorAndBack', 'Blank');
        } else {
            self::display('App.System.System.errorAndBack', 'Blank');
        }
    }

    /**
     * 获取模板内容
     *
     * @param string $templateName 模板名
     * @param string $themeName 主题名
     * @return  string
     */
    public static function fetch(string $templateName, string $themeName = null)
    {
        if ($themeName === null) {
            $themeName = Request::getThemeName();
        }

        ob_start();
        ob_clean();
        $templateInstance = Request::isAdmin() ? Be::getAdminTemplate($templateName, $themeName) : Be::getTemplate($templateName, $themeName);
        foreach (self::$data as $key => $val) {
            $templateInstance->$key = $val;
        }

        if (!isset(self::$data['pageConfig'])) {
            $route = Request::getRoute();
            if ($route !== null) {
                $templateInstance->pageConfig = Be::getService('App.System.Theme')->getPageConfig(Request::isAdmin() ? 'AdminTheme' : 'Theme', $themeName, $route);
            }
        }

        $templateInstance->display();
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * 获取页面配置
     *
     * @param string $themeName 主题名
     * @param string $route 路由名
     * @return object
     */
    public static function getPageConfig(string $themeName = null, string $route = null): object
    {
        if ($themeName === null) {
            $themeName = Request::getThemeName();
        }

        if ($route === null) {
            $route = Request::getRoute();
        }

        return Be::getService('App.System.Theme')->getPageConfig(Request::isAdmin() ? 'AdminTheme' : 'Theme', $themeName, $route);
    }


}
