<?php

namespace Be\Task;

use Be\Be;

/**
 * 计划任务定时器
 */
class TaskInterval extends Task
{
    const HOURLY = -1;
    const DAILY = -2;
    const WEEKLY = -3;
    const MONTHLY = -4;
    const YEARLY = -5;

    // 断点
    protected $breakpoint = null;

    // 时间间隔
    protected $step = 600;


    public function __construct($task, $taskLog)
    {
        parent::__construct($task, $taskLog);

        if (isset($this->task->data['breakpoint'])) {
            $this->breakpoint = $this->task->data['breakpoint'];
        } else {
            $this->task->data['breakpoint'] = $this->breakpoint;
        }
    }


    public function execute()
    {

    }

}
