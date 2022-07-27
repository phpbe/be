<?php

namespace Be\App\System\Controller\Admin;

use Be\Be;

/**
 * @BeMenuGroup("控制台")
 * @BePermissionGroup("控制台")
 */
class Task extends Auth
{
    /**
     * @BeMenu("计划任务", icon="el-icon-timer", ordering="3.3")
     * @BePermission("计划任务", ordering="3.3")
     */
    public function dashboard()
    {
        Be::getAdminPlugin('Task')->setting(['appName' => 'System'])->execute();
    }
}
