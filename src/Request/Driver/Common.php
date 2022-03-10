<?php

namespace Be\Request\Driver;

use Be\Request\Driver;

/**
 * Class Common
 * @package Be\Request\Driver
 */
class Common extends Driver
{
    /**
     * 获取 $_GET 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    public function get(string $name = null, $default = null, $format = 'string')
    {
        return $this->_request($_GET, $name, $default, $format);
    }

    /**
     * 获取 $_POST 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    public function post(string $name = null, $default = null, $format = 'string')
    {
        return $this->_request($_POST, $name, $default, $format);
    }

    /**
     * 获取 $_REQUEST 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    public function request(string $name = null, $default = null, $format = 'string')
    {
        return $this->_request($_REQUEST, $name, $default, $format);
    }

    /**
     * 获取 ajax 请求发送的 JSON 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    public function json(string $name = null, $default = null, $format = null)
    {
        if ($this->json === null) {
            if (isset($_SERVER['CONTENT_TYPE']) && strpos(strtolower($_SERVER['CONTENT_TYPE']), 'application/json') !== false) {
                $json = file_get_contents('php://input');
                $json = json_decode($json, true);
                if ($json) {
                    $this->json = $json;
                }
            }
        }

        return $this->_request($this->json, $name, $default, $format);
    }

    /**
     * 获取 $_SERVER 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    public function server(string $name = null, $default = null, $format = 'string')
    {
        return $this->_request($_SERVER, $name, $default, $format);
    }

    /**
     * 获取 $_COOKIE 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    public function cookie(string $name = null, $default = null, $format = 'string')
    {
        return $this->_request($_COOKIE, $name, $default, $format);
    }

    /**
     * 获取上传的文件
     * @param string $name 参数量
     * @return array|null
     */
    public function files(string $name = null)
    {
        if ($name === null) {
            return $_FILES;
        }

        if (!isset($_FILES[$name])) return null;

        return $_FILES[$name];
    }

    /**
     * 是否GET请求
     *
     * @return bool
     */
    public function isGet(): bool
    {
        return 'GET' === $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 是否POST请求
     *
     * @return bool
     */
    public function isPost(): bool
    {
        return 'POST' === $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 是否AJAX请求
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'XMLHTTPREQUEST' === strtoupper($_SERVER['HTTP_X_REQUESTED_WITH'])) ||
            (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/json') !== false);
    }

    /**
     * 是否通过手机访问
     *
     * @return bool
     */
    public function isMobile(): bool
    {
        if (isset($_GET['_isMobile'])) {
            return $_GET['_isMobile'] ? true : false;
        }

        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取当前请求的方法类型
     *
     * @return string 方法类型 GET / POST / ...
     */
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 获取当前请求的通讯协议
     *
     * @return string 通讯协议
     */
    public function getScheme(): string
    {
        if (isset($_SERVER['HTTP_SCHEME']) && ($_SERVER['HTTP_SCHEME'] === 'http' || $_SERVER['HTTP_SCHEME'] === 'https')) {
            return $_SERVER['HTTP_SCHEME'];
        } else {
            return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        }
    }

    /**
     * 获取当前请求的域名，即服务器名 server name
     *
     * @return string 域名
     */
    public function getDomain(): string
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * 获取当前请求的主机名，包含端品号
     *
     * @return string 主机名
     */
    public function getHost(): string
    {
        return isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT']));
    }

    /**
     * 获取当前请求的主机，包含端品号
     *
     * @return int 端口号
     */
    public function getPort(): int
    {
        return $_SERVER['SERVER_PORT'] ?? 80;
    }

    /**
     * 获取请求者的 IP 地址
     *
     * @return string
     */
    public function getIp(bool $detectProxy = true): string
    {
        if ($detectProxy) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $xForwardedFor = $_SERVER['HTTP_X_FORWARDED_FOR'];

                $ip = null;
                $pos = strpos($xForwardedFor, ',');
                if ($pos === false) {
                    $ip = $xForwardedFor;
                } else {
                    $ip = substr($xForwardedFor, 0, $pos);
                }

                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * 获取当前请求的完整网址
     *
     * @return string 网址
     */
    public function getUrl(): string
    {
        if ($this->url === null) {
            $url = null;
            if (isset($_SERVER['HTTP_SCHEME']) && ($_SERVER['HTTP_SCHEME'] === 'http' || $_SERVER['HTTP_SCHEME'] === 'https')) {
                $url = $_SERVER['HTTP_SCHEME'] . '://';
            } else {
                $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            }

            $url .= isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT']));
            $url .= $_SERVER['REQUEST_URI'];
            $this->url = $url;
        }

        return $this->url;
    }

    /**
     * 获取当前请求的根网址
     *
     * @return string 请求的根网址
     */
    public function getRootUrl(): string
    {
        if ($this->rootUrl === null) {
            $rootUrl = null;
            if (isset($_SERVER['HTTP_SCHEME']) && ($_SERVER['HTTP_SCHEME'] === 'http' || $_SERVER['HTTP_SCHEME'] === 'https')) {
                $rootUrl = $_SERVER['HTTP_SCHEME'] . '://';
            } else {
                $rootUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            }

            $rootUrl .= isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT']));

            $scriptName = $_SERVER['SCRIPT_NAME'];
            $indexName = '/index.php';
            $pos = strrpos($scriptName, $indexName);
            if ($pos !== false) {
                $path = substr($scriptName, 0, $pos);
                if ($path) {
                    $rootUrl .= $path;
                }
            }
            $this->rootUrl = $rootUrl;
        }

        return $this->rootUrl;
    }

    /**
     * 获取来源网址
     *
     * @return string 来源网址
     */
    public function getReferer(): string
    {
        return $_SERVER['HTTP_REFERER'] ?? '';
    }

}

