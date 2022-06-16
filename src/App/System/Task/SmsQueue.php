<?php

namespace Be\App\System\Task;

use Be\Be;
use Be\Task\TaskException;

/**
 * @BeTask("发短信队列")
 */
class SmsQueue extends \Be\Task\TaskInterval
{

    protected $schedule='* * * * *';

    public function execute()
    {
        $t0 = microtime(1);

        $db = Be::getDb();
        while (true) {
            $sql = 'SELECT * FROM system_sms_queue WHERE sent = 0 AND times<10 ORDER BY create_time ASC, times ASC';
            $queues = $db->getObjects($sql);
            if (count($queues) > 0) {
                foreach ($queues as $queue) {

                    try {
                        $sms = Be::getService('System.Sms');

                        $sms->subject($queue->subject ?? '');
                        $sms->body($queue->body ?? '');
                        $sms->send($queue->mobile, $queue->content);

                        $sql = 'UPDATE system_sms_queue SET sent = 1, sent_time = ? WHERE id = ?';
                        $db->query($sql, [date('Y-m-d H:i:s'), $queue->id]);

                    } catch (\Throwable $t) {
                        $sql = 'UPDATE system_sms_queue SET times = times + 1, message = ? WHERE id = ?';
                        $db->query($sql, [$t->getMessage(), $queue->id]);
                    }
                }
            } else {
                if (Be::getRuntime()->isSwooleMode()) {
                    \Swoole\Coroutine::sleep(10);
                } else {
                    sleep(10);
                }
            }

            $t1 = microtime(1);
            if ($t1 - $t0 > 50) {
                break;
            }
        }
    }

}
