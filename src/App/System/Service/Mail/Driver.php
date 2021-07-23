<?php

namespace Be\App\System\Service\Mail;


abstract class Driver
{
    protected $driver = null;


    // 构造函数
    public function __construct()
    {

    }

    // 析构函数
    public function __destruct()
    {
        $this->driver = null;
    }


    public function from($fromMail, $fromName = '')
    {
        return $this;
    }


    public function replyTo($replyToMail, $replyToName = '')
    {
        return $this;
    }


    // 添加收件人
    public function to($email, $name = '')
    {
        return $this;
    }


    // 添加收件人
    public function cc($email, $name = '')
    {
        return $this;
    }


    // 添加收件人
    public function bcc($email, $name = '')
    {
        return $this;
    }


    public function attachment($path)
    {
        return $this;
    }

    public function subject($subject = '')
    {
        return $this;
    }

    public function body($body = '')
    {
        return $this;
    }

    // 设置不支持 html 的客户端显示的主体内容
    public function altBody($altBody = '')
    {
        return $this;
    }

    // 占位符格式化
    public function format($text, $data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $text = str_replace('{' . $key . '}', $val, $text);
            }
        } else {
            $text = str_replace('{0}', $data, $text);
        }

        return $text;
    }

    public function send()
    {
    }

    public function verify($email)
    {
        return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $email);
    }
}
