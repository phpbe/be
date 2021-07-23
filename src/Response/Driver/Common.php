<?php

namespace Be\Response\Driver;

use Be\Be;
use Be\Response\Driver;

/**
 * Class Driver
 * @package Be\System\Response
 */
class Common extends Driver
{

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

        header('HTTP/1.1 ' . $code . ' ' . $message);
    }

    /**
     * 请求重定向
     *
     * @param string $url 跳转网址
     * @param int $code 状态码
     */
    public function redirect(string $url, int $code = 302)
    {
        header('location:' . $url, true, $code);
    }

    /**
     * 输出头信息
     *
     * @param string $key
     * @param string $val
     */
    public function header(string $key, string $val)
    {
        header($key . ':' . $val);
    }

    /**
     * 输出内容
     *
     * @param string $string
     */
    public function write(string $string)
    {
        echo $string;
    }

    /**
     * 以 JSON 输出暂存数据
     */
    public function json($data = null)
    {
        header('Content-type: application/json');
        if ($data === null) {
            echo json_encode($this->data);
        } else {
            echo json_encode(array_merge($this->data, $data));
        }
    }

    /**
     * 结束输出
     *
     * @param string $string 输出内空
     */
    public function end(string $string = '')
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
    public function cookie(string $key, string $value = '', int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httpOnly = false)
    {
        setcookie($key, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    /**
     * 显示模板
     *
     * @param string $template 模板名
     * @param string $theme 主题名
     */
    public function display(string $template = null, string $theme = null)
    {
        $request = Be::getRequest();
        if ($template === null) {
            $template = 'App.' . $request->getRoute();
        }

        $templateInstance = $request->isAdmin() ? Be::getAdminTemplate($template, $theme) : Be::getTemplate($template, $theme);
        foreach ($this->data as $key => $val) {
            $templateInstance->$key = $val;
        }

        $templateInstance->display();
    }



}
