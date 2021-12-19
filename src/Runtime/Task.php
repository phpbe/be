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

            $swooleHttpServer = Be::getRuntime()->getSwooleHttpServer();
            $taskState = $swooleHttpServer->state->get('task', 'value');
            if (!$taskState) {
                return;
            }

            // 每分钟执行一次
            $sec = (int)date('s', time());
            $sleep = 60 - $sec;
            if ($sleep > 0) {
                \Swoole\Coroutine::sleep($sleep);
            }

            $tasks = [];
            try {
                $db = Be::newDb();
                $sql = 'SELECT * FROM system_task WHERE is_enable = 1 AND is_delete = 0 AND schedule != \'\'';
                $tasks = $db->getObjects($sql);
            } catch (\Throwable $t) {
            }

            if (count($tasks) == 0) return;

            $t = time();
            foreach ($tasks as $task) {
                if (TaskHelper::isOnTime($task->schedule, $t)) {
                    $swooleHttpServer->task($task);
                }
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

        $class = '\\Be\\App\\' . $task->app . '\\Task\\' . $task->name;
        if (class_exists($class)) {
            $db = Be::newDb();

            // 有任务正在运行
            $sql = 'SELECT * FROM system_task_log WHERE task_id = ' . $task->id . ' AND status = \'RUNNING\'';
            $taskLogs = $db->getObjects($sql);

            $running = count($taskLogs);
            if ($running > 0) {
                if ($task->timeout > 0) {
                    $t = time();
                    foreach ($taskLogs as $taskLog) {
                        if ($t - strtotime($taskLog->update_time) >= $task->timeout) {
                            $sql = 'UPDATE system_task_log SET status = \'ERROR\', message=\'执行超时\' WHERE id = ' . $taskLog->id;
                            $db->query($sql);
                            $running--;
                        }
                    }
                }
            }

            if ($running > 0) {
                return;
            }

            $taskLog = new \stdClass();
            $instance = null;
            try {
                $now = date('Y-m-d H:i:s');

                $taskLog->task_id = $task->id;
                $taskLog->data = $task->data;
                $taskLog->status = 'RUNNING';
                $taskLog->message = '';
                $taskLog->trigger = $trigger;
                //$taskLog->complete_time = null;
                $taskLog->create_time = $now;
                $taskLog->update_time = $now;
                $taskLogId = $db->insert('system_task_log', $taskLog);
                $taskLog->id = $taskLogId;

                /**
                 * @var \Be\Task\Task $instance
                 */
                $instance = new $class($task, $taskLog);
                $instance->execute();

                $instance->complete();

                //返回任务执行的结果
                //$server->finish("{$data} -> OK");
            } catch (\Throwable $t) {
                if ($instance !== null) {
                    $instance->error($t->getMessage());
                } else {
                    if ($taskLog->id > 0) {
                        $now = date('Y-m-d H:i:s');
                        Be::newDb()->update('system_task_log', [
                            'id' => $taskLog->id,
                            'status' => 'ERROR',
                            'message' => $t->getMessage(),
                            'update_time' => $now
                        ]);
                    }
                }

                //Be::getLog()->emergency($t);
            }
        }
    }


}
