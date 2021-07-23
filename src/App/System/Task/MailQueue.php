<?php

namespace Be\App\System\Task;

use Be\App\System\Service\Mail\Driver;
use Be\Be;
use Be\Task\TaskException;

/**
 * @BeTask("发邮件队列")
 */
class MailQueue extends \Be\Task\TaskInterval
{

    protected $schedule='* * * * *';

    public function execute()
    {
        $t0 = microtime(1);

        $config = Be::getConfig('System.Mail');
        $mailQueueMaxTryTimes = 10;
        if (isset($config->mailQueueMaxTryTimes)) {
            $mailQueueMaxTryTimes = $config->mailQueueMaxTryTimes;
        }

        $db = Be::newDb();
        while (true) {
            $sql = 'SELECT * FROM system_mail_queue WHERE sent = 0 AND times<' . $mailQueueMaxTryTimes . ' ORDER BY create_time ASC, times ASC';
            $queues = $db->getObjects($sql);
            if (count($queues) > 0) {
                foreach ($queues as $queue) {
                    try {
                        $this->send($queue);

                        $now = date('Y-m-d H:i:s');
                        $sql = 'UPDATE system_mail_queue SET sent = 1, sent_time = ?, update_time = ? WHERE id = ?';
                        $db->query($sql, [$now, $now, $queue->id]);
                    } catch (\Throwable $t) {
                        $now = date('Y-m-d H:i:s');
                        $sql = 'UPDATE system_mail_queue SET times = times + 1, error_message = ?, update_time = ? WHERE id = ?';
                        $db->query($sql, [$t->getMessage(), $now, $queue->id]);
                    }
                }
            } else {
                \Swoole\Coroutine::sleep(10);
            }

            $t1 = microtime(1);
            if ($t1 - $t0 > 50) {
                break;
            }
        }
    }

    private function send($queue)
    {
        $config = Be::getConfig('System.Mail');
        $class = '\\Be\\App\\System\\Service\\Mail\\' . $config->driver;

        /**
         * @var Driver $mailer
         */
        $mailer = new $class();

        if (!$queue->to_email) {
            throw new TaskException('收件人邮箱缺失！');
        }

        if (!$mailer->verify($queue->to_email)) {
            throw new TaskException('收件人邮箱（' . $queue->to_email . '）格式错误！');
        }

        $mailer->to($queue->to_email, $queue->to_name ?? '');
        $mailer->subject($queue->subject ?? '');
        $mailer->body($queue->body ?? '');

        if ($queue->cc_email) {
            if (!$mailer->verify($queue->cc_email)) {
                throw new TaskException('抄送人邮箱（' . $queue->cc_email . '）格式错误！');
            }

            $mailer->cc($queue->cc_email, $queue->cc_name ?? '');
        }

        if ($queue->bcc_email) {
            if (!$mailer->verify($queue->bcc_email)) {
                throw new TaskException('暗送人邮箱（' . $queue->bcc_email . '）格式错误！');
            }

            $mailer->cc($queue->bcc_email, $queue->bcc_name ?? '');
        }

        $mailer->send();
    }

}
