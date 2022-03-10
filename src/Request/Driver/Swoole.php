<?php

namespace Be\Request\Driver;

use Be\Request\Driver;

/**
 * Class Swoole
 * @package Be\Request\Driver
 */
class Swoole extends Driver
{

    /**
     * @var \Swoole\Http\Request
     */
    private $request = null;


    public function __construct(\Swoole\Http\Request $request)
    {
        $this->request = $request;
    }

    /**
     * 获取 $_GET 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    public function get(string $name = null, $default = null, $format = 'string')
    {
        return $this->_request($this->request->get, $name, $default, $format);
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
        return $this->_request($this->request->post, $name, $default, $format);
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
        return $this->_request($this->request->request, $name, $default, $format);
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
            $json = $this->request->getContent();
            $json = json_decode($json, true);
            if ($json) {
                $this->json = $json;
            } else {
                $this->json = [];
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
        return $this->_request($this->request->server, $name, $default, $format);
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
        return $this->_request($this->request->cookie, $name, $default, $format);
    }

    /**
     * 获取上传的文件
     * @param string|null $name 参数量
     * @return array|null
     */
    public function files(string $name = null)
    {
        if ($name === null) {
            return $this->request->files;
        }

        if (!isset($this->request->files[$name])) return null;

        return $this->request->files[$name];
    }

    /**
     * 是否GET请求
     *
     * @return bool
     */
    public function isGet(): bool
    {
        return 'GET' === $this->request->server['request_method'];
    }

    /**
     * 是否POST请求
     *
     * @return bool
     */
    public function isPost(): bool
    {
        return 'POST' === $this->request->server['request_method'];
    }

    /**
     * 是否AJAX请求
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        return isset($this->request->header['accept']) && strpos(strtolower($this->request->header['accept']), 'application/json') !== false;
    }

    /**
     * 是否通过手机访问
     *
     * @return bool
     */
    public function isMobile(): bool
    {
        if (isset($this->request->get['_isMobile'])) {
            return $this->request->get['_isMobile'] ? true : false;
        }

        if (empty($this->request->header['user-agent'])) {
            return false;
        } elseif (strpos($this->request->header['user-agent'], 'Mobile') !== false
            || strpos($this->request->header['user-agent'], 'Android') !== false
            || strpos($this->request->header['user-agent'], 'Silk/') !== false
            || strpos($this->request->header['user-agent'], 'Kindle') !== false
            || strpos($this->request->header['user-agent'], 'BlackBerry') !== false
            || strpos($this->request->header['user-agent'], 'Opera Mini') !== false
            || strpos($this->request->header['user-agent'], 'Opera Mobi') !== false) {
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
        return $this->request->server['request_method'];
    }

    /**
     * 获取当前请求的通讯协议
     *
     * @return string 通讯协议
     */
    public function getScheme(): string
    {
        if (isset($this->request->header['scheme']) && ($this->request->header['scheme'] === 'http' || $this->request->header['scheme'] === 'https')) {
            return $this->request->header['scheme'];
        } else {
            return 'http';
        }
    }

    /**
     * 获取当前请求的域名，即服务器名 server name
     *
     * @return string 域名
     */
    public function getDomain(): string
    {
        $domain = $this->request->header['host'];
        $pos = strpos($domain, ':');
        if ($pos !== false) {
            $domain = substr($domain, 0, $pos);
        }
        return $domain;
    }

    /**
     * 获取当前请求的主机名，包含端品号
     *
     * @return string 主机名
     */
    public function getHost(): string
    {
        return $this->request->header['host'];
    }

    /**
     * 获取当前请求的主机，包含端品号
     *
     * @return int 端口号
     */
    public function getPort(): int
    {
        $host = $this->request->header['host'];
        $port = null;
        $pos = strpos($host, ':');
        if ($pos !== false) {
            $port = (int)substr($host, $pos + 1);
        } else {
            $port = 80;
        }

        return $port;
    }

    /**
     * 获取请求者的 IP 地址
     *
     * @return string
     */
    public function getIp(bool $detectProxy = true): string
    {
        if ($detectProxy) {
            if (isset($this->request->header['x-forwarded-for'])) {
                $xForwardedFor = $this->request->header['x-forwarded-for'];

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

        return $this->request->server['remote_addr'];
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
            if (isset($this->request->header['scheme']) && ($this->request->header['scheme'] === 'http' || $this->request->header['scheme'] === 'https')) {
                $url = $this->request->header['scheme'] . '://';
            } else {
                $url = 'http://';
            }
            $url .= $this->request->header['host'];
            $url .= $this->request->server['request_uri'];
            if ($this->request->server['query_string']) {
                $url .= '?' . $this->request->server['query_string'];
            }

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
            if (isset($this->request->header['scheme']) && ($this->request->header['scheme'] === 'http' || $this->request->header['scheme'] === 'https')) {
                $rootUrl = $this->request->header['scheme'] . '://';
            } else {
                $rootUrl = 'http://';
            }
            $rootUrl .= $this->request->header['host'];

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
        return $this->request->header['referer'] ?? '';
    }

    /**
     * 获取原生 Request 对像
     *
     * @return \Swoole\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

}

