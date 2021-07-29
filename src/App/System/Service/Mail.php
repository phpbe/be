<?php
namespace Be\App\System\Service;

use Be\App\System\Service\Mail\Driver;
use Be\Be;
use Be\App\ServiceException;

class Mail
{
    /**
     * @var Driver
     */
    private $driver = null;

    // 构造函数
    public function __construct()
    {
        $config = Be::getConfig('App.System.Mail');
        $class = '\\Be\\App\\Service\\Mail\\' . $config->driver;
        $this->driver = new $class();
    }

    public function getDriver() {
        return $this->driver;
    }

    /**
     * 发送邮件
     *
     * @param string | array $to 收件人
     * @param string $subject 主题
     * @param string $body 内容
     * @param string | array | null $cc
     * @param string | array | null $bcc
     * @throws \Exception
     */
    public function send($to, $subject = '', $body = '', $cc = null, $bcc = null)
    {
        $toEmail = '';
        $toName = '';
        if (is_string($to)) {
            $toEmail = $to;
        } else {
            if (is_array($to)) {
                if (isset($to['email'])) {
                    $toEmail = $to['email'];
                }

                if (isset($to['name'])) {
                    $toName = $to['name'];
                }
            }
        }

        if (!$toEmail) {
            throw new ServiceException('收件人邮箱缺失！');
        }

        if (!$this->driver->verify($toEmail)) {
            throw new ServiceException('收件人邮箱（' . $toEmail . '）格式错误！');
        }

        $this->driver->to($toEmail, $toName);
        $this->driver->subject($subject);
        $this->driver->body($body);

        if ($cc !== null) {
            $ccEmail = null;
            $ccName = '';
            if (is_string($cc)) {
                $ccEmail = $cc;
            } else {
                if (is_array($cc)) {
                    if (isset($cc['email'])) {
                        $ccEmail = $cc['email'];

                        if (isset($cc['name'])) {
                            $ccName = $cc['name'];
                        }
                    }
                }
            }

            if ($ccEmail) {
                if (!$this->driver->verify($ccEmail)) {
                    throw new ServiceException('抄送人邮箱（' . $ccEmail . '）格式错误！');
                }

                $this->driver->cc($ccEmail, $ccName);
            }
        }

        if ($bcc !== null) {
            $bccEmail = null;
            $bccName = '';
            if (is_string($bcc)) {
                $bccEmail = $bcc;
            } else {
                if (is_array($bcc)) {
                    if (isset($bcc['email'])) {
                        $bccEmail = $bcc['email'];

                        if (isset($bcc['name'])) {
                            $bccName = $bcc['name'];
                        }
                    }
                }
            }

            if ($bccEmail) {
                if (!$this->driver->verify($bccEmail)) {
                    throw new ServiceException('暗送人邮箱（' . $bccEmail . '）格式错误！');
                }

                $this->driver->bcc($bccEmail, $bccName);
            }
        }

        $this->driver->send();

    }

}
