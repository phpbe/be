<?php

namespace Be\App\System\Service;

use Be\Be;
use Be\Task\TaskHelper;

class Task
{

    /**
     * 触发启动指定的计划任务
     *
     * @param string $taskRoute
     * @param $triggerType
     *              SYSTEM: 系统定时任务按时启动
     *              MANUAL: 用户手工触发
     *              RELATED：程序功能关联触发。
     */
    public function trigger($taskRoute, $triggerType = 'RELATED')
    {
        $parts = explode('.', $taskRoute);
        $app = $parts[0];
        $name = $parts[1];

        $tuple = Be::getTuple('system_task');
        $tuple->loadBy([
            'app' => $app,
            'name' => $name,
        ]);

        $tuple->trigger = $triggerType;

        if (Be::getRuntime()->getMode() == 'Swoole') {
            Be::getRuntime()->task($tuple->toObject());
        } else {
            $config = Be::getConfig('System.Task');
            $url = beUrl('System.Task.run', ['password' => $config->password, 'taskId' => $tuple->id, 'trigger' => $triggerType]);
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1);
            curl_exec($curl);
            curl_close($curl);
        }
    }


    /**
     * 普通PHP模式 下任务调度
     *
     * 调度
     */
    public function dispatch()
    {
        $db = Be::getDb();

        $sql = 'SELECT * FROM system_task WHERE is_enable = 1 AND is_delete = 0 AND schedule != \'\'';
        $tasks = $db->getObjects($sql);

        $config = Be::getConfig('System.Task');
        $t = time();
        foreach ($tasks as $task) {
            if (TaskHelper::isOnTime($task->schedule, $t)) {
                $url = beUrl('System.Task.run', ['password' => $config->password, 'taskId' => $task->id, 'timestamp' => $t, 'trigger' => 'SYSTEM']);
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_HEADER, 1);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_TIMEOUT, 1);
                curl_exec($curl);
                curl_close($curl);
            }
        }
    }

    /**
     * 普通PHP模式  执行计划任务
     *
     */
    public function run($taskId, $timestamp, $trigger)
    {
        $tuple = Be::getTuple('system_task');
        $tuple->load($taskId);
        $task = $tuple->toObject();

        /*
        if ($trigger == 'SYSTEM') {
            if (TaskHelper::isOnTime($task->schedule, $timestamp)) {
                return;
            }
        }
        */

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
                $taskLog->complete_time = '0000-00-00 00:00:00';
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
