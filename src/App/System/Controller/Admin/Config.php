<?php

namespace Be\App\System\Controller\Admin;

use Be\Be;

/**
 * @BeMenuGroup("控制台")
 * @BePermissionGroup("控制台")
 */
class Config extends Auth
{

    /**
     * @BeMenu("参数", icon="el-icon-setting", ordering="2.8")
     * @BePermission("参数", ordering="2.8")
     */
    public function dashboard()
    {
        Be::getAdminPlugin('Config')->setting(['appName' => 'System', 'title' => '参数'])->execute();
    }


}