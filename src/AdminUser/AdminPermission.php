<?php

namespace Be\AdminUser;

/**
 * 权限基类
 */
abstract class AdminPermission
{

    public $permissionKeys = [];

    /**
     * 检测是否有权限访问指定控制器和任务
     *
     * @param string $app 应用
     * @param string $controller 控制器
     * @param string $action 动作
     * @return string | bool
     */
    public function getPermissionKey($app, $controller, $action)
    {
        $key = $app . '.' . $controller . '.' . $action;
        if (isset($this->permissionKeys[$key])) {
            return $this->permissionKeys[$key];
        }

        return false;
    }


}