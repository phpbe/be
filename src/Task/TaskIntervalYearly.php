<?php

namespace Be\Task;

/**
 * 计划任务定时器 - 每年
 */
class TaskIntervalYearly extends TaskInterval
{

    // 时间间隔
    protected $step = TaskInterval::YEARLY;

    // 每月执行一次
    protected $schedule = '0 0 1 * *';

}
