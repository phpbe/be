<?php

namespace Be\App\System\AdminService;

use Be\Db\Tuple;
use Be\App\AdminServiceException;
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
     * @throws AdminServiceException
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
            throw new AdminServiceException('未找到指定编号（#' . $roleId . '）的角色！');
        }

        $code = '<?php' . "\n";
        $code .= 'namespace Be\\Data\\Cache\\AdminRole;' . "\n";
        $code .= "\n";
        $code .= 'class AdminRole' . $roleId . ' extends \\Be\\AdminUser\\AdminRole' . "\n";
        $code .= '{' . "\n";
        $code .= '  public $name = \'' . $tuple->name . '\';' . "\n";
        $code .= '  public $permission = \'' . $tuple->permission . '\';' . "\n";
        if ($tuple->permission == -1) {
            $code .= '  public $permissions = [\'' . implode('\',\'', explode(',', $tuple->permissions)) . '\'];' . "\n";
        } else {
            $code .= '  public $permissions = [];' . "\n";
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

        $apps = Be::getAdminService('System.App')->getApps();
        foreach ($apps as $app) {
            $appName = $app->name;
            $appProperty = Be::getProperty('App.' . $appName);
            $controllerDir = Be::getRuntime()->getRootPath() . $appProperty->getPath() . '/AdminController';
            if (!file_exists($controllerDir) && !is_dir($controllerDir)) continue;
            $controllers = scandir($controllerDir);
            foreach ($controllers as $controller) {
                if ($controller == '.' || $controller == '..' || is_dir($controllerDir . '/' . $controller)) continue;

                $controller = substr($controller, 0, -4);
                $className = 'Be\\App\\' . $appName . '\\AdminController\\' . $controller;
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
        $code .= '  public $permissions = [\'' . implode('\',\'', $permissions) . '\'];' . "\n";
        $code .= '}' . "\n";

        $path = Be::getRuntime()->getCachePath() . '/AdminRole/AdminRole0.php';
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        file_put_contents($path, $code, LOCK_EX);
        @chmod($path, 0755);
    }


    public function getPermissionTree()
    {
        $treeData = [];
        $apps = Be::getAdminService('System.App')->getApps();
        foreach ($apps as $app) {
            $appName = $app->name;

            $children = [];
            $appProperty = Be::getProperty('App.' . $appName);
            $controllerDir = Be::getRuntime()->getRootPath() . $appProperty->getPath() . '/AdminController';
            if (!file_exists($controllerDir) && !is_dir($controllerDir)) continue;
            $controllers = scandir($controllerDir);
            foreach ($controllers as $controller) {
                if ($controller == '.' || $controller == '..' || is_dir($controllerDir . '/' . $controller)) continue;

                $controller = substr($controller, 0, -4);
                $className = 'Be\\App\\' . $appName . '\\AdminController\\' . $controller;
                if (!class_exists($className)) continue;

                $reflection = new \ReflectionClass($className);
                $classComment = $reflection->getDocComment();
                $parseClassComments = Annotation::parse($classComment);

                $childKey = null;
                $childLabel = null;
                $childOrdering = null;
                foreach ($parseClassComments as $key => $val) {
                    if ($key == 'BePermissionGroup') {
                        if (is_array($val[0]) && isset($val[0]['value']) && $val[0]['value'] != '*') {
                            $childKey = $appName . '.' . $controller;
                            $childLabel = $val[0]['value'];
                            if (isset($val[0]['ordering'])) {
                                $childOrdering = $val[0]['ordering'];
                            }
                            break;
                        }
                    }
                }

                if ($childKey === null) {
                    continue;
                }

                if (!isset($children[$childLabel])) {
                    $children[$childLabel] = [
                        'key' => $childKey,
                        'label' => $childLabel,
                        'children' => [],
                    ];
                }

                if ($childOrdering !== null) {
                    $children[$childLabel]['ordering'] = $childOrdering;
                }

                $subChildren = [];
                $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($methods as &$method) {
                    $methodName = $method->getName();
                    if (substr($methodName, 0, 1) == '_') {
                        continue;
                    }

                    $methodComment = $method->getDocComment();
                    $methodComments = Annotation::parse($methodComment);
                    foreach ($methodComments as $key => $val) {
                        if ($key == 'BePermission') {
                            if (is_array($val[0]) && isset($val[0]['value']) && $val[0]['value'] != '*') {
                                $subChildren[] = [
                                    'key' => $appName . '.' . $controller . '.' . $methodName,
                                    'label' => $val[0]['value'],
                                    'ordering' => isset($val[0]['ordering']) ? $val[0]['ordering'] : 1000000,
                                ];
                            }
                            break;
                        }
                    }
                }

                if (count($subChildren) > 0) {
                    $children[$childLabel]['children'] = array_merge($children[$childLabel]['children'], $subChildren);
                }
            }

            if (count($children) > 0) {
                $filteredChildren = [];
                foreach ($children as $key => $val) {
                    if (count($val['children']) > 0) {
                        if (!isset($val['ordering'])) {
                            $val['ordering'] = 1000000;
                        }
                        $filteredChildren[] = $val;
                    }
                }

                if (count($filteredChildren) > 0) {
                    $treeData[] = [
                        'key' => $app->name,
                        'label' => $app->label,
                        'ordering' => 0,
                        'children' => $filteredChildren,
                    ];
                }
            }
        }

        // 排序
        foreach ($treeData as $k => &$v) {
            foreach ($v['children'] as $key => &$val) {
                $orderings = array_column($val['children'], 'ordering');
                array_multisort($val['children'], SORT_ASC, SORT_NUMERIC, $orderings);
            }
            unset($val);

            $orderings = array_column($v['children'], 'ordering');
            array_multisort($v['children'], SORT_ASC, SORT_NUMERIC, $orderings);
        }
        unset($v);

        $orderings = array_column($treeData, 'ordering');
        array_multisort($treeData, SORT_ASC, SORT_NUMERIC, $orderings);

        return $treeData;
    }

}
