<?php

namespace Be\App\System\Service;

use Be\App\ServiceException;
use Be\Be;


/**
 * 通过队列发送邮件
 *
 * Class MailQueue
 * @package Be\System\App\System\Service
 */
class MailQueue
{

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
        $tuple = Be::getTuple('system_mail_queue');

        $tuple->to_email = '';
        $tuple->to_name = '';
        if (is_string($to)) {
            $tuple->to_email = $to;
        } else {
            if (is_array($to)) {
                if (isset($to['email'])) {
                    $tuple->to_email = $to['email'];
                }

                if (isset($to['name'])) {
                    $tuple->to_name = $to['name'];
                }
            }
        }

        if (!$tuple->to_email) {
            throw new ServiceException('收件人邮箱缺失！');
        }

        $tuple->cc_email = '';
        $tuple->cc_name = '';
        if ($cc !== null) {
            if (is_string($cc)) {
                $tuple->cc_email = $cc;
            } else {
                if (is_array($cc)) {
                    if (isset($cc['email'])) {
                        $tuple->cc_email = $cc['email'];

                        if (isset($cc['name'])) {
                            $tuple->cc_name = $cc['name'];
                        }
                    }
                }
            }
        }

        $tuple->bcc_email = '';
        $tuple->bcc_name = '';
        if ($bcc !== null) {
            if (is_string($bcc)) {
                $tuple->bcc_email = $bcc;
            } else {
                if (is_array($bcc)) {
                    if (isset($bcc['email'])) {
                        $tuple->bcc_email = $bcc['email'];

                        if (isset($bcc['name'])) {
                            $tuple->bcc_name = $bcc['name'];
                        }
                    }
                }
            }
        }

        $tuple->subject = $subject;
        $tuple->body = $body;
        $tuple->sent = 0;
        $tuple->sent_time = null;
        $tuple->error_message = '';
        $tuple->times = 0;
        $tuple->create_time = date('Y-m-d H:i:s');
        $tuple->update_time = date('Y-m-d H:i:s');
        $tuple->insert();
    }

}
