<?php

namespace Be\AdminUser;

/**
 * 角色基类
 */
abstract class AdminRole
{
    public $name = '';
    public $permission = -1;
    public $permissions = [];

    /**
     * 检测是否有权限访问指定控制器和任务
     *
     * @param string $app 应用
     * @param string $controller 控制器
     * @param string $action 动作
     * @return bool
     */
    public function hasPermission($app, $controller, $action)
    {
        if ($this->permission == 1) return true;
        if ($this->permission == 0) return false;

        $key = $app . '.' . $controller . '.' . $action;
        return in_array($key, $this->permissions);
    }
}