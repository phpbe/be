<?php

namespace Be\App\System\Service\Admin;

use Be\Db\Tuple;
use Be\App\ServiceException;
use Be\Util\Annotation;
use Be\Be;

class AdminPermission
{

    /**
     * 更新权限类
     */
    public function updateAdminPermission()
    {
        $permissionTree = $this->getOriginalPermissionTree();
        $permissionKeys = [];
        foreach ($permissionTree as $x) {
            foreach ($x['children'] as $xx) {
                $permissionLabels = [];
                foreach ($xx['children'] as $xxx) {
                    if (isset($permissionLabels[$xxx['label']])) {
                        $permissionKeys[$xxx['key']] = $permissionLabels[$xxx['label']];
                    } else {
                        $permissionKeys[$xxx['key']] = $xxx['key'];
                        $permissionLabels[$xxx['label']] = $xxx['key'];
                    }
                }
            }
        }

        $code = '<?php' . "\n";
        $code .= 'namespace Be\\Data\\Cache\\AdminPermission;' . "\n";
        $code .= "\n";
        $code .= 'class AdminPermission extends \\Be\\AdminUser\\AdminPermission' . "\n";
        $code .= '{' . "\n";
        $arr = [];
        foreach ($permissionKeys as $k => $v) {
            $arr[] = '\'' . $k . '\'=>\'' . $v . '\'';
        }
        $code .= '  public $keyValues = [' . implode(',', $arr) . '];' . "\n";
        $code .= '}' . "\n";

        $path = Be::getRuntime()->getCachePath() . '/AdminPermission/AdminPermission.php';
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        file_put_contents($path, $code, LOCK_EX);
        @chmod($path, 0755);

        return $permissionTree;
    }

    /**
     * 获取权限树
     *
     * @return array
     */
    public function getPermissionTree()
    {
        $permissionTree = $this->getOriginalPermissionTree();
        foreach ($permissionTree as $k => $v) {
            foreach ($v['children'] as $kk => $vv) {
                $permissionLabels = [];
                $children = [];
                foreach ($vv['children'] as $kkk => $vvv) {
                    if (!isset($permissionLabels[$vvv['label']])) {
                        $permissionLabels[$vvv['label']] = 1;
                        $children[] = $vvv;
                    }
                }
                $permissionTree[$k]['children'][$kk]['children'] = $children;
            }
        }
        return $permissionTree;
    }

    /**
     * 获取原始权限树
     *
     * @return array
     */
    protected function getOriginalPermissionTree()
    {
        $permissionTree = [];
        $apps = Be::getService('App.System.Admin.App')->getApps();
        foreach ($apps as $app) {
            $appName = $app->name;

            $children = [];
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
                    $permissionTree[] = [
                        'key' => $app->name,
                        'label' => $app->label,
                        'ordering' => 0,
                        'children' => $filteredChildren,
                    ];
                }
            }
        }

        // 排序
        foreach ($permissionTree as $k => &$v) {
            foreach ($v['children'] as $key => &$val) {
                $orderings = array_column($val['children'], 'ordering');
                array_multisort($val['children'], SORT_ASC, SORT_NUMERIC, $orderings);
            }
            unset($val);

            $orderings = array_column($v['children'], 'ordering');
            array_multisort($v['children'], SORT_ASC, SORT_NUMERIC, $orderings);
        }
        unset($v);

        $orderings = array_column($permissionTree, 'ordering');
        array_multisort($permissionTree, SORT_ASC, SORT_NUMERIC, $orderings);

        return $permissionTree;
    }

}
