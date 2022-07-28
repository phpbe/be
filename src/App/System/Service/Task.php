<?php

namespace Be\App\System\Service;

use Be\App\ServiceException;
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

        $taskNames = [];
        $dir =  Be::getProperty('App.' . $appName)->getPath() . '/Task';
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

                            $parallel = $task['parallel'] ?? 0;
                            $schedule = $task['schedule'] ?? '';
                            $timeout = $task['timeout'] ?? 60;

                            $parallelLock = 0;
                            $scheduleLock = 0;
                            $timeoutLock = 0;

                            $defaultProperties = $reflection->getDefaultProperties();

                            if (isset($defaultProperties['parallel'])) {
                                $parallel = $defaultProperties['parallel'] ? 1 : 0;
                                $parallelLock = 1;
                            }

                            if (isset($defaultProperties['schedule']) && $defaultProperties['schedule']) {
                                $schedule = $defaultProperties['schedule'];
                                $scheduleLock = 1;
                            }

                            if (isset($defaultProperties['timeout'])) {
                                $timeout = (int)$defaultProperties['timeout'];
                                $timeoutLock = 1;
                            }

                            if (isset($dbTasks[$taskName])) {

                                $taskNames[] = $taskName;

                                $data = [
                                    'id' => $dbTasks[$taskName]->id,
                                    'name' => $taskName,
                                    'label' => $task['value'] ?? '',
                                    'is_delete' => 0,
                                    'update_time' => date('Y-m-d H:i:s'),
                                ];

                                if ($parallelLock) {
                                    $data['parallel'] = $parallel;
                                    $data['parallel_lock'] = $parallelLock;
                                }

                                if ($scheduleLock) {
                                    $data['schedule'] = $schedule;
                                    $data['schedule_lock'] = $scheduleLock;
                                }

                                if ($timeoutLock) {
                                    $data['timeout'] = $timeout;
                                    $data['timeout_lock'] = $timeoutLock;
                                }

                                $db->update('system_task', $data, 'id');
                            } else {

                                $taskId = $db->quickUuid();

                                $data = [
                                    'id' => $taskId,
                                    'app' => $appName,
                                    'name' => $taskName,
                                    'label' => $task['value'] ?? '',
                                    'parallel' => $parallel,
                                    'parallel_lock' => $parallelLock,
                                    'schedule' => $schedule,
                                    'schedule_lock' => $scheduleLock,
                                    'timeout' => $timeout,
                                    'timeout_lock' => $timeoutLock,
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

        // 删除已不存在的 task
        if (count($dbTasks) !== count($taskNames)) {
            foreach ($dbTasks as $taskName => $dbTask) {
                if (!is_array($taskName, $taskNames)) {
                    $sql = 'UPDATE system_task SET is_enable=1, is_delete=1 WHERE id=' . $db->quoteValue($dbTask->id);
                    $db->query($sql);
                }
            }
        }

        return $n;
    }


    /**
     * 触发启动指定的计划任务
     *
     * @param string $taskRoute
     * @param array $taskData 数据
     * @param string $triggerType
     *              SYSTEM: 系统定时任务按时启动
     *              MANUAL: 用户手工触发
     *              RELATED：程序功能关联触发。
     */
    public function trigger(string $taskRoute, array $taskData = null, string $triggerType = 'RELATED')
    {
        $parts = explode('.', $taskRoute);
        if (count($parts) !== 2) {
            return;
        }

        $app = $parts[0];
        $name = $parts[1];

        $tupleTask = Be::getTuple('system_task');
        try {
            $tupleTask->loadBy([
                'app' => $app,
                'name' => $name,
            ]);
        } catch (\Throwable $t) {
            Be::getLog()->warning($t);
            // throw new ServiceException('Task does not register!');
            return;
        }

        $task = $tupleTask->toObject();

        if (Be::getRuntime()->isSwooleMode()) {
            $task->trigger = $triggerType;
            if ($taskData !== null && count($taskData) > 0) {
                $task->data = $taskData;
            }
            Be::getRuntime()->task($task);
        } else {
            $config = Be::getConfig('App.System.Task');
            $url = beUrl('System.Task.run', ['password' => $config->password, 'taskId' => $task->id, 'trigger' => $triggerType]);
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1);
            if ($taskData !== null && count($taskData) > 0) {
                $options[CURLOPT_POST] = 1;
                $options[CURLOPT_POSTFIELDS] = http_build_query($taskData);
            }
            curl_exec($curl);
            curl_close($curl);
        }
    }


    /**
     * 普通PHP模式 下任务调度
     *
     * 调度
     */
    public function schedule()
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
    public function onTask($taskId, $timestamp, $trigger, $taskData = null)
    {
        $tuple = Be::getTuple('system_task');
        try {
            $tuple->load($taskId);
        } catch (\Throwable $t) {
            return;
        }

        $task = $tuple->toObject();

        /*
        if ($trigger === 'SYSTEM') {
            if (TaskHelper::isOnTime($task->schedule, $timestamp)) {
                return;
            }
        }
        */

        $task->parallel = (int)$task->parallel;

        if ($taskData !== null) {
            $task->data = $taskData;
        }

        $this->run($task, $trigger);
    }

    /**
     * 执行计划任务
     */
    public function run($task, $trigger)
    {
        $cache = null;
        $cacheKey =  null;
        if ($task->parallel === 0) {
            $cache = Be::getCache();
            $cacheKey = 'be:task:running:' . $task->id;

            // 计划任务串行且正在执行，直接返回
            if ($cache->has($cacheKey)) {
                return;
            } else {
                $cache->set($cacheKey, 1, $task->timeout);
            }
        }

        $db = Be::getDb();
        $class = '\\Be\\App\\' . $task->app . '\\Task\\' . $task->name;
        if (class_exists($class)) {
            // 计划任务不允许并行
            if ($task->parallel === 0) {
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
            }

            $taskLog = new \stdClass();
            $taskLogId = null;
            $instance = null;
            try {
                $now = date('Y-m-d H:i:s');
                $taskLog->id = $db->quickUuid();
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

                if ($task->parallel === 0) {
                    $cache->delete($cacheKey);
                }

                $instance->complete();

                //返回任务执行的结果
                //$server->finish("{$data} -> OK");

            } catch (\Throwable $t) {

                if ($task->parallel === 0) {
                    $cache->delete($cacheKey);
                }

                Be::getLog()->fatal($t);

                $message = $t->getMessage();
                if (mb_strlen($message, 'utf8') > 600) {
                    $message = mb_substr($message, 0, 600, 'utf8');
                }

                if ($instance !== null) {
                    $instance->error($message);
                } else {
                    if ($taskLogId !== null) {

                        $now = date('Y-m-d H:i:s');
                        Be::getDb()->update('system_task_log', [
                            'id' => $taskLogId,
                            'status' => 'ERROR',
                            'message' => $message,
                            'update_time' => $now
                        ]);
                    }
                }
            }

        } else {
            if ($task->parallel === 0) {
                $cache->delete($cacheKey);
            }

            $now = date('Y-m-d H:i:s');

            $taskLog = new \stdClass();
            $taskLog->id = $db->quickUuid();
            $taskLog->task_id = $task->id;
            $taskLog->data = $task->data;
            $taskLog->status = 'ERROR';
            $taskLog->message = '计划任务类（' . $class . '）不存在！';
            $taskLog->trigger = $trigger;
            //$taskLog->complete_time = null;
            $taskLog->create_time = $now;
            $taskLog->update_time = $now;

            $db->insert('system_task_log', $taskLog);
        }

    }

}
