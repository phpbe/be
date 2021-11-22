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

    public function isGet()
    {
        return 'GET' == $this->request->server['request_method'];
    }

    public function isPost()
    {
        return 'POST' == $this->request->server['request_method'];
    }

    public function isAjax()
    {
        return isset($this->request->header['accept']) && strpos(strtolower($this->request->header['accept']), 'application/json') !== false;
    }

    /**
     * 是否通过手机访问
     *
     * @return bool
     */
    public function isMobile()
    {
        if (isset($this->request->get['_isMobile'])) {
            return $this->request->get['_isMobile'] ? true: false;
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

    public function getMethod()
    {
        return $this->request->server['request_method'];
    }

    /**
     * 获取当前请求的完整网址
     */
    public function getUrl()
    {
        $url = 'http://';
        $url .= $this->request->header['host'];
        $url .= $this->request->server['request_uri'];
        if ($this->request->server['query_string']) {
            $url .= '?' . $this->request->server['query_string'];
        }
        return $url;
    }

    /**
     * 获取请求者的 IP 地址
     *
     * @return string
     */
    public function getIp(bool $detectProxy = true)
    {
        return $this->request->server['remote_addr'];
    }

    /**
     * 获取当前请求的完整网址
     */
    public function getRootUrl()
    {
        return 'http://' . $this->request->header['host'];
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

