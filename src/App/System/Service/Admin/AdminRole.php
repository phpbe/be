<?php

namespace Be\App\System\Service\Admin;

use Be\Db\Tuple;
use Be\App\ServiceException;
use Be\Util\Annotation;
use Be\Be;

class AdminRole
{

    /**
     * 获取角色
     *
     * @return Tuple
     */
    public function getAdminRole($roleId)
    {
        return Be::getTuple('system_admin_role')->load($roleId);
    }

    /**
     * 获取角色列表
     *
     * @return array
     */
    public function getAdminRoles()
    {
        return Be::getTable('system_admin_role')->orderBy('ordering', 'ASC')->getObjects();
    }

    /**
     * 获取角色銉值对
     *
     * @return array
     */
    public function getAdminRoleKeyValues()
    {
        return Be::getTable('system_admin_role')->orderBy('ordering', 'ASC')->getKeyValues('id', 'name');
    }

    /**
     * 更新所有角色缓存
     */
    public function updateAdminRoles()
    {
        $roles = $this->getAdminRoles();
        foreach ($roles as $role) {
            $this->updateAdminRole($role->id);
        }
    }

    /**
     * 更新指定角色到文件缓存中
     *
     * @param string $roleId
     * @throws ServiceException
     */
    public function updateAdminRole(string $roleId)
    {
        $tuple = Be::getTuple('system_admin_role');
        $tuple->load($roleId);
        if (!$tuple->id) {
            throw new ServiceException('未找到指定编号（#' . $roleId . '）的角色！');
        }

        $suffix = str_replace('-', '', $roleId);

        $code = '<?php' . "\n";
        $code .= 'namespace Be\\Data\\Cache\\AdminRole;' . "\n";
        $code .= "\n";
        $code .= 'class AdminRole_' . $suffix . ' extends \\Be\\AdminUser\\AdminRole' . "\n";
        $code .= '{' . "\n";
        $code .= '  public $name = \'' . $tuple->name . '\';' . "\n";
        $code .= '  public $permission = ' . $tuple->permission . ';' . "\n";
        if ($tuple->permission === -1) {
            $code .= '  public $permissionKeys = [\'' . implode('\',\'', explode(',', $tuple->permission_keys)) . '\'];' . "\n";
        } else {
            $code .= '  public $permissionKeys = [];' . "\n";
        }
        $code .= '}' . "\n";

        $path = Be::getRuntime()->getCachePath() . '/AdminRole/AdminRole_' . $suffix . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        file_put_contents($path, $code, LOCK_EX);
        chmod($path, 0777);
    }

}
