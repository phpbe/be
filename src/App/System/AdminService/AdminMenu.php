<?php

namespace Be\App\System\AdminService;

use Be\Util\Annotation;
use Be\Be;

class AdminMenu
{

    private $menus = null;

    /**
     * 获取后台菜单
     */
    public function getMenus()
    {
        if ($this->menus !== null) return $this->menus;

        $menus = [];

        $apps = Be::getAdminService('System.App')->getApps();
        foreach ($apps as $app) {

            $appProperty = Be::getProperty('App.'.$app->name);
            $appName = $app->name;
            $controllerDir = Be::getRuntime()->getRootPath() . $appProperty->getPath(). '/AdminController';
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
                foreach ($parseClassComments as $key => $val) {
                    if ($key == 'BeMenuGroup') {
                        if (is_array($val[0])) {
                            if (!isset($val[0]['label']) && isset($val[0]['value'])) {
                                $val[0]['label'] = $val[0]['value'];
                            }

                            if (isset($val[0]['label'])) {
                                $classMenuGroup = $val[0];
                            }
                        }
                    }
                }

                $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($methods as &$method) {
                    $methodName = $method->getName();
                    $methodComment = $method->getDocComment();
                    $methodComments = Annotation::parse($methodComment);
                    $menuGroup = [];
                    $menu = [];
                    foreach ($methodComments as $key => $val) {
                        if ($key == 'BeMenuGroup') {
                            if (is_array($val[0])) {
                                if (!isset($val[0]['label']) && isset($val[0]['value'])) {
                                    $val[0]['label'] = $val[0]['value'];
                                }

                                if (isset($val[0]['label'])) {
                                    $menuGroup = $val[0];
                                }
                            }
                        } elseif ($key == 'BeMenu') {
                            if (is_array($val[0])) {
                                if (!isset($val[0]['label']) && isset($val[0]['value'])) {
                                    $val[0]['label'] = $val[0]['value'];
                                }

                                if (isset($val[0]['label'])) {
                                    $menu = $val[0];
                                }
                            }
                        }
                    }

                    if (!$menuGroup) {
                        $menuGroup = $classMenuGroup;
                    }

                    if (!$menuGroup || !$menu) {
                        continue;
                    }

                    $app->key = $appName;
                    $menuGroup['key'] = $appName . '.' . $controller;

                    $menu['key'] = $appName . '.' . $controller . '.' . $methodName;
                    $menu['url'] = 'beAdminUrl(\'' . $appName . '.' . $controller . '.' . $methodName . '\')';
                    if (!isset($menu['ordering'])) {
                        $menu['ordering'] = 1000000;
                    }

                    if (!isset($menus[$appName])) {
                        $menus[$appName] = [
                            'app' => $app,
                            'groups' => [],
                            'ordering' => $menu['ordering'],
                        ];
                    }

                    if (!isset($menus[$appName]['groups'][$menuGroup['label']])) {
                        $menus[$appName]['groups'][$menuGroup['label']] = [
                            'group' => $menuGroup,
                            'menus' => [
                                $menu
                            ],
                        ];
                    } else {
                        $menus[$appName]['groups'][$menuGroup['label']]['group'] = array_merge($menus[$appName]['groups'][$menuGroup['label']]['group'], $menuGroup);
                        $menus[$appName]['groups'][$menuGroup['label']]['menus'][] = $menu;
                    }

                    if (isset($menuGroup['ordering'])) {
                        $menus[$appName]['groups'][$menuGroup['label']]['ordering'] = $menuGroup['ordering'];
                    }
                }
            }

            if (isset($menus[$appName]['groups']) && is_array($menus[$appName]['groups']) && count($menus[$appName]['groups']) > 0) {
                foreach ($menus[$appName]['groups'] as &$group) {
                    if (!isset($group['group']['ordering'])) {
                        $group['group']['ordering'] = 1000000;
                    }
                    $group['ordering'] = $group['group']['ordering'];
                }
                unset($group);
            }
        }

        // 排序
        foreach ($menus as $k => &$v) {
            foreach ($v['groups'] as $key => &$val) {
                $orderings = array_column($val['menus'],'ordering');
                array_multisort($val['menus'],SORT_ASC, SORT_NUMERIC,$orderings);
            }
            unset($val);

            $orderings = array_column($v['groups'],'ordering');
            array_multisort($v['groups'],SORT_ASC, SORT_NUMERIC,$orderings);
        }
        unset($v);

        $orderings = array_column($menus,'ordering');
        array_multisort($menus,SORT_ASC, SORT_NUMERIC,$orderings);

        $this->menus = $menus;
        return $menus;
    }

    /**
     * 更新事台菜单
     */
    public function update()
    {
        $menus = $this->getMenus();

        $code = '<?php' . "\n";
        $code .= 'namespace Be\\Data\\Cache;' . "\n";
        $code .= "\n";
        $code .= 'class AdminMenu extends \\Be\\AdminMenu\\Driver' . "\n";
        $code .= '{' . "\n";
        $code .= '  public function __construct()' . "\n";
        $code .= '  {' . "\n";

        foreach ($menus as $k => $v) {
            $app = $v['app'];
            $code .= '    $this->addMenu(\'' . $app->key . '\', \'0\', \'' . $app->icon . '\',\'' . $app->label . '\', \'\', \'\');' . "\n";
            foreach ($v['groups'] as $key => $val) {
                $group = $val['group'];
                $code .= '    $this->addMenu(\'' . $group['key'] . '\',\'' . $app->key . '\',\'' . (isset($group['icon']) ? $group['icon'] : 'el-icon-folder') . '\',\'' . $group['label'] . '\', \'\', \'\');' . "\n";
                foreach ($val['menus'] as $menu) {
                    $code .= '    $this->addMenu(\'' . $menu['key'] . '\', \'' . $group['key'] . '\', \'' . (isset($menu['icon']) ? $menu['icon'] : 'el-icon-arrow-right') . '\', \'' . $menu['label'] . '\', ' . $menu['url'] . ', \'' . (isset($menu['target']) ? $menu['target'] : '') . '\');' . "\n";
                }
            }
        }
        $code .= '  }' . "\n";
        $code .= '}' . "\n";

        $path = Be::getRuntime()->getCachePath() . '/AdminMenu.php';
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        file_put_contents($path, $code, LOCK_EX);
        @chmod($path, 0755);
    }

}
