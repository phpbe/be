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
        return Be::newTuple('system_admin_role')->load($roleId);
    }

    /**
     * 获取角色列表
     *
     * @return array
     */
    public function getAdminRoles()
    {
        return Be::newTable('system_admin_role')->orderBy('ordering', 'ASC')->getObjects();
    }

    /**
     * 获取角色銉值对
     *
     * @return array
     */
    public function getAdminRoleKeyValues()
    {
        return Be::newTable('system_admin_role')->orderBy('ordering', 'ASC')->getKeyValues('id', 'name');
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
     * @param $roleId
     * @throws ServiceException
     */
    public function updateAdminRole($roleId)
    {
        if ($roleId == 0) {
            $this->updateAdminRole0();
            return;
        }

        $tuple = Be::newTuple('system_admin_role');
        $tuple->load($roleId);
        if (!$tuple->id) {
            throw new ServiceException('未找到指定编号（#' . $roleId . '）的角色！');
        }

        $code = '<?php' . "\n";
        $code .= 'namespace Be\\Data\\Cache\\AdminRole;' . "\n";
        $code .= "\n";
        $code .= 'class AdminRole' . $roleId . ' extends \\Be\\AdminUser\\AdminRole' . "\n";
        $code .= '{' . "\n";
        $code .= '  public $name = \'' . $tuple->name . '\';' . "\n";
        $code .= '  public $permission = \'' . $tuple->permission . '\';' . "\n";
        if ($tuple->permission == -1) {
            $code .= '  public $permissionKeys = [\'' . implode('\',\'', explode(',', $tuple->permissions)) . '\'];' . "\n";
        } else {
            $code .= '  public $permissionKeys = [];' . "\n";
        }
        $code .= '}' . "\n";

        $path = Be::getRuntime()->getCachePath() . '/AdminRole/AdminRole' . $roleId . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        file_put_contents($path, $code, LOCK_EX);
        @chmod($path, 0755);
    }

    /**
     * 公共用户权限
     */
    public function updateAdminRole0()
    {
        $permissions = [];

        $apps = Be::getService('App.System.Admin.App')->getApps();
        foreach ($apps as $app) {
            $appName = $app->name;
            $appProperty = Be::getProperty('App.' . $appName);
            $controllerDir = Be::getRuntime()->getRootPath() . $appProperty->getPath() . '/Controller/Admin';
            if (!file_exists($controllerDir) && !is_dir($controllerDir)) continue;
            $controllers = scandir($controllerDir);
            foreach ($controllers as $controller) {
                if ($controller == '.' || $controller == '..' || is_dir($controllerDir . '/' . $controller)) continue;

                $controller = substr($controller, 0, -4);
                $className = 'Be\\App\\' . $appName . '\\Controller\\Admin\\' . $controller;
                if (!class_exists($className)) continue;

                $reflection = new \ReflectionClass($className);
                $classMenuGroup = [];

                // 类注释
                $classComment = $reflection->getDocComment();
                $parseClassComments = Annotation::parse($classComment);

                $permission = 0;
                foreach ($parseClassComments as $key => $val) {
                    if ($key == 'BePermissionGroup') {
                        if (is_array($val[0]) && isset($val[0]['value']) && $val[0]['value'] == '*') {
                            $permission = 1;
                            break;
                        }
                    }
                }

                $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($methods as &$method) {
                    $methodName = $method->getName();
                    if (substr($methodName, 0, 1) == '_') {
                        continue;
                    }

                    if ($permission == 1) {
                        $permissions[] = $appName . '.' . $controller . '.' . $methodName;
                    } else {
                        $methodComment = $method->getDocComment();
                        $methodComments = Annotation::parse($methodComment);
                        foreach ($methodComments as $key => $val) {
                            if ($key == 'BePermission') {
                                if (is_array($val[0]) && isset($val[0]['value']) && $val[0]['value'] == '*') {
                                    $permissions[] = $appName . '.' . $controller . '.' . $methodName;
                                }
                                break;
                            }
                        }
                    }
                }
            }
        }

        $code = '<?php' . "\n";
        $code .= 'namespace Be\\Data\\Cache\\AdminRole;' . "\n";
        $code .= "\n";
        $code .= 'class AdminRole0 extends \\Be\\AdminUser\\AdminRole' . "\n";
        $code .= '{' . "\n";
        $code .= '  public $name = \'公共功能\';' . "\n";
        $code .= '  public $permission = \'-1\';' . "\n";
        $code .= '  public $permissionKeys = [\'' . implode('\',\'', $permissions) . '\'];' . "\n";
        $code .= '}' . "\n";

        $path = Be::getRuntime()->getCachePath() . '/AdminRole/AdminRole0.php';
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        file_put_contents($path, $code, LOCK_EX);
        @chmod($path, 0755);
    }

}
