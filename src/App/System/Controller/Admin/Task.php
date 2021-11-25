<?php

namespace Be\App\System\Controller\Admin;

use Be\Be;

/**
 * @BeMenuGroup("管理")
 * @BePermissionGroup("管理")
 */
class Task extends Auth
{
    /**
     * @BeMenu("计划任务", icon="el-icon-timer", ordering="2.4")
     * @BePermission("计划任务", ordering="2.4")
     */
    public function dashboard()
    {
        Be::getAdminPlugin('Task')->setting(['appName' => 'System'])->execute();
    }
}
