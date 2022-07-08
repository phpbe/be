<?php

namespace Be\Response\Driver;

use Be\Be;
use Be\Response\Driver;

/**
 * Class Swoole
 * @package Be\Response\Driver
 */
class Swoole extends Driver
{

    /**
     * @var \Swoole\Http\Response
     */
    protected $response = null;

    /**
     * Response constructor.
     * @param \Swoole\Http\Response $response
     */
    public function __construct(\Swoole\Http\Response $response)
    {
        $this->response = $response;
    }

    /**
     * 请求状态
     *
     * @param int $code 状态码
     * @param string $message 状态信息
     */
    public function status(int $code = 302, string $message = '')
    {
        if (!$message) {
            if (isset(self::STATUS[$code])) {
                $message = self::STATUS[$code];
            }
        }

        $this->response->status($code, $message);
    }

    /**
     * 请求重定向
     *
     * @param string $url 跳转网址
     * @param int $code 状态码
     */
    public function redirect(string $url, int $code = 302)
    {
        $this->response->redirect($url, $code);
    }

    /**
     * 输出头信息
     *
     * @param string $key
     * @param string $val
     */
    public function header(string $key, string $val)
    {
        $this->response->header($key, $val);
    }

    /**
     * 输出内容
     *
     * @param string $string
     */
    public function write(string $string)
    {
        $this->response->write($string);
    }

    /**
     * 以 JSON 输出暂存数据
     */
    public function json($data = null)
    {
        $this->response->header('Content-type', 'application/json');
        if ($data === null) {
            $this->response->end(json_encode($this->data));
        } else {
            $this->response->end(json_encode(array_merge($this->data, $data)));
        }
    }

    /**
     * 结束输出
     *
     * @param string $string 输出内空
     */
    public function end(string $string = '')
    {
        $this->response->end($string);
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
    public function cookie(string $key, string $value = '', int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httpOnly = false)
    {
        $this->response->cookie($key, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    /**
     * 显示模板
     *
     * @param string $templateName 模板名
     * @param string $themeName 主题名
     */
    public function display(string $templateName = null, string $themeName = null)
    {
        $request = Be::getRequest();
        if ($templateName === null) {
            if ($request->isAdmin()) {
                $templateName = 'App.' . $request->getAppName() . '.Admin.' . $request->getControllerName() . '.' . $request->getActionName();
            } else {
                $templateName = 'App.' . $request->getRoute();
            }
        }

        $this->response->end($this->fetch($templateName, $themeName));
    }

    /**
     * 获取原生 Response 对像
     *
     * @return \Swoole\Http\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

}
