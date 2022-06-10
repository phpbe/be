<?php

namespace Be\Task;

use Be\Be;

/**
 * 计划任务
 */
class Task
{

    /**
     * @var null|object
     */
    protected $task = null;

    /**
     * @var null|object
     */
    protected $taskLog = null;

    /**
     * 执行计划，与注解中的执行计划不同，此参数配置的执行计划禁止改动
     * @var null|string
     */
    protected $schedule = null;

    /**
     * 是否可并行执行
     *
     * @var null|bool
     */
    protected $parallel = null;

    /**
     * 执行超时时间
     *
     * @var null|int
     */
    protected $timeout = null;


    public function __construct($task, $taskLog)
    {
        if ($task->data !== '') {
            $task->data = json_decode($task->data, true);
        }

        $taskLog->data = $task->data;

        $this->task = $task;
        $this->taskLog = $taskLog;
    }


    public function execute()
    {

    }

    public function updateTask()
    {
        if ($this->task->data) {
            $this->task->data = json_encode($this->task->data);
        }

        Be::newDb()->update('system_task', $this->task, 'id');
    }

    public function updateTaskLog()
    {
        if ($this->taskLog->data) {
            $this->taskLog->data = json_encode($this->taskLog->data);
        }

        Be::newDb()->update('system_task_log', $this->taskLog, 'id');
    }

    public function getSchedule(): string
    {
        return $this->schedule;
    }

    public function isParallel($parallel = null): bool
    {
        if ($parallel !== null) {
            $this->parallel = $parallel;
        }

        return $this->parallel;
    }

    public function setTimeout(int $timeout = 60): int
    {
        return $this->timeout = $timeout;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function complete()
    {
        $now = date('Y-m-d H:i:s');
        $db = Be::newDb();

        $this->taskLog->status = 'COMPLETE';
        $this->taskLog->complete_time = $now;
        $this->taskLog->update_time = $now;
        if ($this->taskLog->data) {
            $this->taskLog->data = json_encode($this->taskLog->data);
        }
        $db->update('system_task_log', $this->taskLog, 'id');

        $this->task->last_execute_time = $now;
        $this->task->update_time = $now;
        if ($this->task->data) {
            $this->task->data = json_encode($this->task->data);
        }
        $db->update('system_task', $this->task, 'id');
    }

    public function error($message)
    {
        $now = date('Y-m-d H:i:s');

        $this->taskLog->status = 'ERROR';
        $this->taskLog->message = $message;
        $this->taskLog->update_time = $now;
        $this->updateTaskLog();
    }

}
