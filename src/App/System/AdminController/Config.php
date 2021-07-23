<?php

namespace Be\App\System\AdminController;

use Be\Be;

/**
 * @BeMenuGroup("系统配置", icon="el-icon-setting", ordering="3")
 * @BePermissionGroup("系统配置", icon="el-icon-setting", ordering="3")
 */
class Config
{

    /**
     * @BeMenu("系统配置", icon="el-icon-setting", ordering="3.1")
     * @BePermission("系统配置", ordering="3.1")
     */
    public function dashboard()
    {
        Be::getAdminPlugin('Config')->setting(['appName' => 'System'])->execute();
    }


}