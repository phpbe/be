<?php

namespace Be\App\System\Service\Admin;

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

        $apps = Be::getService('App.System.Admin.App')->getApps();
        foreach ($apps as $app) {

            $appProperty = Be::getProperty('App.'.$app->name);
            $appName = $app->name;
            $controllerDir = Be::getRuntime()->getRootPath() . $appProperty->getPath(). '/Controller/Admin';
            if (!file_exists($controllerDir) && !is_dir($controllerDir)) continue;

            $app->route = '';
            $className = '\\Be\\App\\' . $appName . '\\Controller\\Admin\\Index';
            if (class_exists($className)) {
                $reflection = new \ReflectionClass($className);
                $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($methods as &$method) {
                    $methodName = $method->getName();
                    if ($methodName === 'index') {
                        $app->route = $app->name . '.Index.index';
                        break;
                    }
                }
            }

            $controllers = scandir($controllerDir);
            foreach ($controllers as $controller) {
                if ($controller === '.' || $controller === '..' || is_dir($controllerDir . '/' . $controller)) continue;

                $controller = substr($controller, 0, -4);
                $className = '\\Be\\App\\' . $appName . '\\Controller\\Admin\\' . $controller;
                if (!class_exists($className)) continue;

                $reflection = new \ReflectionClass($className);
                $classMenuGroup = [];

                // 类注释
                $classComment = $reflection->getDocComment();
                $parseClassComments = Annotation::parse($classComment);
                foreach ($parseClassComments as $key => $val) {
                    if ($key === 'BeMenuGroup') {
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
                    $item = [];
                    foreach ($methodComments as $key => $val) {
                        if ($key === 'BeMenuGroup') {
                            if (is_array($val[0])) {
                                if (!isset($val[0]['label']) && isset($val[0]['value'])) {
                                    $val[0]['label'] = $val[0]['value'];
                                }

                                if (isset($val[0]['label'])) {
                                    $menuGroup = $val[0];
                                }
                            }
                        } elseif ($key === 'BeMenu') {
                            if (is_array($val[0])) {
                                if (!isset($val[0]['label']) && isset($val[0]['value'])) {
                                    $val[0]['label'] = $val[0]['value'];
                                }

                                if (isset($val[0]['label'])) {
                                    $item = $val[0];
                                }
                            }
                        }
                    }

                    if (!$menuGroup) {
                        $menuGroup = $classMenuGroup;
                    }

                    if (!$menuGroup || !$item) {
                        continue;
                    }

                    $app->key = $appName;
                    $menuGroup['key'] = $appName . '.' . $controller;

                    $item['key'] = $appName . '.' . $controller . '.' . $methodName;
                    $item['route'] = $appName . '.' . $controller . '.' . $methodName;
                    if (!isset($item['ordering'])) {
                        $item['ordering'] = 1000000;
                    }

                    if (!isset($menus[$appName])) {
                        $menus[$appName] = [
                            'app' => $app,
                            'groups' => [],
                            'ordering' => $item['ordering'],
                        ];
                    }

                    if (!isset($menus[$appName]['groups'][$menuGroup['label']])) {
                        $menus[$appName]['groups'][$menuGroup['label']] = [
                            'group' => $menuGroup,
                            'items' => [
                                $item
                            ],
                        ];
                    } else {
                        $menus[$appName]['groups'][$menuGroup['label']]['group'] = array_merge($menus[$appName]['groups'][$menuGroup['label']]['group'], $menuGroup);
                        $menus[$appName]['groups'][$menuGroup['label']]['items'][] = $item;
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
                $orderings = array_column($val['items'],'ordering');
                array_multisort($val['items'],SORT_ASC, SORT_NUMERIC,$orderings);
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
        $items = $this->getMenus();

        $code = '<?php' . "\n";
        $code .= 'namespace Be\\Data\\Runtime;' . "\n";
        $code .= "\n";
        $code .= 'class AdminMenu extends \\Be\\AdminMenu\\Driver' . "\n";
        $code .= '{' . "\n";
        $code .= '  public function __construct()' . "\n";
        $code .= '  {' . "\n";

        foreach ($items as $k => $v) {
            $app = $v['app'];
            $code .= '    $this->addItem(\'' . $app->key . '\', \'\', \'' . $app->icon . '\',\'' . $app->label . '\', \'' . $app->route . '\', [], \'\', \'\');' . "\n";
            foreach ($v['groups'] as $key => $val) {
                $group = $val['group'];
                $firstItem = current($val['items']);
                $code .= '    $this->addItem(\'' . $group['key'] . '\',\'' . $app->key . '\',\'' . (isset($group['icon']) ? $group['icon'] : 'el-icon-folder') . '\',\'' . $group['label'] . '\', \'' . $firstItem['route'] . '\', [], \'\', \'\');' . "\n";
                foreach ($val['items'] as $item) {
                    $code .= '    $this->addItem(\'' . $item['key'] . '\', \'' . $group['key'] . '\', \'' . (isset($item['icon']) ? $item['icon'] : 'el-icon-arrow-right') . '\', \'' . $item['label'] . '\', \'' . $item['route'] . '\', [], \'\', \'' . (isset($item['target']) ? $item['target'] : '') . '\');' . "\n";
                }
            }
        }
        $code .= '  }' . "\n";
        $code .= '}' . "\n";

        $path = Be::getRuntime()->getRootPath() . '/data/Runtime/AdminMenu.php';
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        file_put_contents($path, $code, LOCK_EX);
        chmod($path, 0777);
    }

}
