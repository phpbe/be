<?php
namespace Be\App\System\Service\Mail;

use Be\Be;
use Be\App\ServiceException;
use PHPMailer\PHPMailer\PHPMailer;

class Smtp extends Driver
{

    private $driver = null;

    // 构造函数
    public function __construct()
    {
        $config = Be::getConfig('App.System.Mail');
        if ($config->driver != 'Smtp') {
            throw new ServiceException('实例化SMTP邮件发送器时报错：邮件配置参数非SMTP！');
        }

        $this->driver = new PHPMailer(true);
        $this->driver->setLanguage('zh_cn');

        if ($config->fromMail) $this->driver->From = $config->fromMail;
        if ($config->fromName) $this->driver->FromName = $config->fromName;

        $this->driver->isHTML(true);
        $this->driver->CharSet = 'utf-8';
        $this->driver->Encoding = 'base64';

        if ($config->smtp == 1) {
            $this->driver->isSMTP();
            $this->driver->Host = $config->smtp['host']; // smtp 主机地址
            $this->driver->Port = $config->smtp['port']; // smtp 主机端口
            $this->driver->SMTPAuth = true;
            $this->driver->Username = $config->smtp['username']; // smtp 用户名
            $this->driver->Password = $config->smtp['password']; // smtp 用户密码
            $this->driver->Timeout = $config->smtp['timeout']; // smtp 超时时间 秒

            if ($config->smtp['secure'] != '0') $this->driver->SMTPSecure = $config->smtp['secure']; // smtp 加密 'ssl' 或 'tls'
        }
    }

    public function from($fromMail, $fromName = '')
    {
        $this->driver->setFrom($fromMail, $fromName);
        return $this;
    }


    public function replyTo($replyToMail, $replyToName = '')
    {
        $this->driver->addReplyTo($replyToMail, $replyToName);
        return $this;
    }


    // 添加收件人
    public function to($email, $name = '')
    {
        if (!$this->driver->addAddress($email, $name)) {
            throw new \Exception($this->driver->ErrorInfo);
        }
        return $this;
    }


    // 添加收件人
    public function cc($email, $name = '')
    {
        if (!$this->driver->addCC($email, $name)) {
            throw new ServiceException($this->driver->ErrorInfo);
        }
        return $this;
    }


    // 添加收件人
    public function bcc($email, $name = '')
    {
        if (!$this->driver->addBCC($email, $name)) {
            throw new ServiceException($this->driver->ErrorInfo);
        }
        return $this;
    }


    public function attachment($path)
    {
        if (!$this->driver->addAttachment($path)) {
            throw new ServiceException($this->driver->ErrorInfo);
        }
        return $this;
    }

    public function subject($subject = '')
    {
        $this->driver->Subject = $subject;
        return $this;
    }

    public function body($body = '')
    {
        $this->driver->Body = $body;
        return $this;
    }

    // 设置不支持 html 的客户端显示的主体内容
    public function altBody($altNody = '')
    {
        $this->driver->AltBody = $altNody;
        return $this;
    }

    public function send()
    {
        if (!$this->driver->send()) {
            throw new ServiceException($this->driver->ErrorInfo);
        }
    }

    public function verify($email)
    {
        return $this->driver->validateAddress($email);
    }
}
