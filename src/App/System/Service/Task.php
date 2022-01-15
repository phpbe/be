<?php

namespace Be\App\System\Service;

use Be\Be;
use Be\Task\Annotation\BeTask;
use Be\Task\TaskHelper;

class Task
{

    /**
     * 发现新的计划任务
     *
     * @param $appName
     */
    public function discover($appName)
    {
        $n = 0;
        $db = Be::getDb();

        $sql = 'SELECT * FROM system_task WHERE app=' . $db->quoteValue($appName);
        $dbTasks = $db->getKeyObjects($sql, null, 'name');

        $dir = Be::getRuntime()->getRootPath() . Be::getProperty('App.' . $appName)->getPath() . '/Task';
        if (file_exists($dir) && is_dir($dir)) {
            $fileNames = scandir($dir);
            foreach ($fileNames as $fileName) {
                if ($fileName !== '.' && $fileName !== '..' && is_file($dir . '/' . $fileName)) {
                    $taskName = substr($fileName, 0, -4);
                    $className = '\\Be\\App\\' . $appName . '\\Task\\' . $taskName;
                    if (class_exists($className)) {
                        $reflection = new \ReflectionClass($className);
                        $classComment = $reflection->getDocComment();
                        $parseClassComments = \Be\Util\Annotation::parse($classComment);
                        if (isset($parseClassComments['BeTask'][0])) {
                            $annotation = new BeTask($parseClassComments['BeTask'][0]);
                            $task = $annotation->toArray();

                            $schedule = $task['schedule'] ?? '';
                            $scheduleLock = 0;
                            $defaultProperties = $reflection->getDefaultProperties();
                            if (isset($defaultProperties['schedule']) && $defaultProperties['schedule']) {
                                $schedule = $defaultProperties['schedule'];
                                $scheduleLock = 1;
                            }

                            if (isset($dbTasks[$taskName])) {
                                $data = [
                                    'id' => $dbTasks[$taskName]->id,
                                    'name' => $taskName,
                                    'label' => $task['value'] ?? '',
                                    'schedule' => $schedule,
                                    'schedule_lock' => $scheduleLock,
                                    'is_delete' => 0,
                                    'update_time' => date('Y-m-d H:i:s'),
                                ];
                                $db->update('system_task', $data, 'id');
                            } else {

                                $taskId = null;
                                if (function_exists('uuid_create')) {
                                    $taskId = uuid_create();
                                } else {
                                    $taskId = $db->uuid();
                                }

                                $data = [
                                    'id' => $taskId,
                                    'app' => $appName,
                                    'name' => $taskName,
                                    'label' => $task['value'] ?? '',
                                    'schedule' => $schedule,
                                    'schedule_lock' => $scheduleLock,
                                    'is_enable' => 0,
                                    'is_delete' => 0,
                                    //'last_execute_time' => null,
                                    'create_time' => date('Y-m-d H:i:s'),
                                    'update_time' => date('Y-m-d H:i:s'),
                                ];
                                $db->insert('system_task', $data);
                                $n++;
                            }
                        }
                    }
                }
            }
        }
        return $n;
    }


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

        if (Be::getRuntime()->getMode() === 'Swoole') {
            Be::getRuntime()->task($tuple->toObject());
        } else {
            $config = Be::getConfig('App.System.Task');
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

        $config = Be::getConfig('App.System.Task');
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
        if ($trigger === 'SYSTEM') {
            if (TaskHelper::isOnTime($task->schedule, $timestamp)) {
                return;
            }
        }
        */

        $class = '\\Be\\App\\' . $task->app . '\\Task\\' . $task->name;
        if (class_exists($class)) {
            $db = Be::newDb();

            // 有任务正在运行
            $sql = 'SELECT * FROM system_task_log WHERE task_id = \'' . $task->id . '\' AND status = \'RUNNING\'';
            $taskLogs = $db->getObjects($sql);

            $running = count($taskLogs);
            if ($running > 0) {
                if ($task->timeout > 0) {
                    $t = time();
                    foreach ($taskLogs as $taskLog) {
                        if ($t - strtotime($taskLog->update_time) >= $task->timeout) {
                            $sql = 'UPDATE system_task_log SET status = \'ERROR\', message=\'执行超时\' WHERE id = \'' . $taskLog->id . '\'';
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
            $taskLogId = null;
            $instance = null;
            try {
                $now = date('Y-m-d H:i:s');

                if (function_exists('uuid_create')) {
                    $taskLog->id = uuid_create();
                } else {
                    $taskLog->id = $db->uuid();
                }

                $taskLog->task_id = $task->id;
                $taskLog->data = $task->data;
                $taskLog->status = 'RUNNING';
                $taskLog->message = '';
                $taskLog->trigger = $trigger;
                //$taskLog->complete_time = null;
                $taskLog->create_time = $now;
                $taskLog->update_time = $now;

                $db->insert('system_task_log', $taskLog);

                $taskLogId = $taskLog->id;

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
                    if ($taskLogId !== null) {
                        $now = date('Y-m-d H:i:s');
                        Be::newDb()->update('system_task_log', [
                            'id' => $taskLogId,
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
