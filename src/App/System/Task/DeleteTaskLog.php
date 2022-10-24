<?php

namespace Be\App\System\Task;

use Be\Be;
use Be\Task\TaskException;

/**
 * @BeTask("删除任务日志", schedule='0 2 * * *')
 */
class DeleteTaskLog extends \Be\Task\TaskInterval
{

    public function execute()
    {
        if (!is_array($this->task->data)) {
            $this->task->data = [];
        }

        $interval = 86400;
        if (isset($this->task->data['interval']) && is_numeric($this->task->data['interval']) && $this->task->data['interval'] > 0) {
            $interval = $this->task->data['interval'];
        } else {
            $this->task->data['interval'] = $interval;
        }

        $sql = 'DELETE FROM system_task_log WHERE status=\'COMPLETE\' AND create_time < \'' . date('Y-m-d H:i:s', time() - $interval) . '\'';
        Be::getDb()->query($sql);
    }

}
