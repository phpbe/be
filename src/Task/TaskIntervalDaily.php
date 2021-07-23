<?php

namespace Be\Task;

/**
 * 计划任务定时器 - 每天
 */
class TaskIntervalDaily extends TaskInterval
{

    // 时间间隔
    protected $step = TaskInterval::DAILY;

    // 每天执行一次
    protected $schedule = '0 0 * * *';

}
