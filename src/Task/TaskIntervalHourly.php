<?php

namespace Be\Task;

/**
 * 计划任务定时器 - 每小时
 */
class TaskIntervalHourly extends TaskInterval
{
    // 时间间隔
    protected $step = TaskInterval::HOURLY;

    // 每小时执行一次
    protected $schedule = '0 * * * *';

}
