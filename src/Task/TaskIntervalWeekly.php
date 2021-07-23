<?php

namespace Be\Task;

/**
 * 计划任务定时器 - 每周
 */
class TaskIntervalWeekly extends TaskInterval
{

    // 时间间隔
    protected $step = TaskInterval::WEEKLY;

    // 每周执行一次
    protected $schedule = '* * * * *';

}
