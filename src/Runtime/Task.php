<?php

namespace Be\Runtime;


use Be\Be;
use Be\Task\TaskHelper;

class Task
{

    /**
     * 定时计划任务调度
     *
     * @param $process
     */
    public static function process($process)
    {
        while (true) {
            // 每分钟执行一次
            $sec = (int)date('s', time());
            $sleep = 60 - $sec;
            if ($sleep > 0) {
                \Swoole\Coroutine::sleep($sleep);
            }

            $tasks = [];
            try {
                $db = Be::getDb();
                $sql = 'SELECT * FROM system_task WHERE is_enable = 1 AND is_delete = 0 AND schedule != \'\'';
                $tasks = $db->getObjects($sql);
            } catch (\Throwable $t) {
            }

            if (count($tasks) > 0) {
                $t = time();
                foreach ($tasks as $task) {
                    if (TaskHelper::isOnTime($task->schedule, $t)) {
                        $task->trigger = 'SYSTEM';
                        Be::getRuntime()->getSwooleHttpServer()->task($task);
                    }
                }
            } else {
                \Swoole\Coroutine::sleep(600);
            }
        }
    }

    /**
     * \Swoole\Http\Server task 回调
     *
     * @param \Swoole\Http\Server $swooleHttpServer
     * @param \Swoole\Server\Task $swooleServerTask
     */
    public static function onTask(\Swoole\Http\Server $swooleHttpServer, \Swoole\Server\Task $swooleServerTask)
    {
        $task = $swooleServerTask->data;
        $trigger = $task->trigger;
        unset($task->trigger);

        $task->parallel = (int)$task->parallel;

        try {
            Be::getService('App.System.Task')->run($task, $trigger);
        } catch (\Throwable $t) {
            Be::getLog()->fatal($t);
        }

        Be::gc();
    }


}
