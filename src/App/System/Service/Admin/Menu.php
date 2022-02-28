<?php

namespace Be\App\System\Service\Admin;

use Be\App\ServiceException;
use Be\Be;

class Menu
{

    /**
     * 获取指定菜单的菜单项
     *
     * @param string $id 菜单ID
     * @return object 配置项的值
     */
    public function getMenuById(string $id): \stdClass
    {
        $sql = 'SELECT * FROM system_menu WHERE id=?';
        $menu = Be::getDb()->getObject($sql, [$id]);
        return $menu;
    }

    /**
     * 获取菜单
     *
     * @param string $menuName 菜单名称
     * @return object 菜单
     */
    public function getMenu(string $menuName = 'North'): \stdClass
    {
        $sql = 'SELECT * FROM system_menu WHERE `name`=?';
        return Be::getDb()->getObject($sql, [$menuName]);
    }


    /**
     * 获取菜单项
     *
     * @param string $menuName 菜单名称
     * @return array 菜单项
     */
    public function getItems(string $menuName = 'North'): array
    {
        $sql = 'SELECT * FROM system_menu_item WHERE menu_name=? ORDER BY ordering ASC';
        return Be::getDb()->getObjects($sql, [$menuName]);
    }

    /**
     * 获取指定菜单的菜单项摘要
     *
     * @param string $menuName 菜单名称
     * @return string 菜单项摘要
     */
    public function getSummary(string $menuName = 'North'): string
    {
        $sql = 'SELECT `name` FROM system_menu_item WHERE menu_name=? AND parent_id=\'\' ORDER By ordering ASC';
        $menuItemNames = Be::getDb()->getValues($sql, [$menuName]);

        $summary = null;
        $n = count($menuItemNames);
        if ($n > 3) {
            $summary = implode('，', array_slice($menuItemNames, 0, 3)) . ' 等' . $n . '项';
        } elseif ($n === 0) {
            $summary = '-';
        } else {
            $summary = implode('，', $menuItemNames);
        }

        return $summary;
    }

    /**
     * 获取指定菜单的菜单项的树状结构 - 多维
     *
     * @param string $menuName 菜单名称
     * @return array 配置项的树状结构
     */
    public function getTree(string $menuName = 'North'): array
    {
        $menuItems = $this->getItems($menuName);
        $menuItemTree = $this->makeTree($menuItems);
        return $menuItemTree;
    }

    /**
     * 生成树
     *
     * @param array $menuItems
     * @param string $parentId
     * @param int $level
     * @return array
     */
    private function makeTree(array $menuItems, string $parentId = '', int $level = 1): array
    {
        $tree = [];
        foreach ($menuItems as $menuItem) {
            if ($menuItem->parent_id === $parentId) {
                $item = [
                    'id' => $menuItem->id,
                    'parent_id' => $menuItem->parent_id,
                    'name' => $menuItem->name,
                    'route' => $menuItem->route,
                    'params' => $menuItem->params ? json_decode($menuItem->params, true) : [],
                    'url' => $menuItem->url,
                    'description' => $menuItem->description,
                    'target' => $menuItem->target,
                    'level' => $level,
                ];

                $sub = $this->makeTree($menuItems, $menuItem->id, $level + 1);
                $item['sub_count'] = count($sub);
                $item['sub'] = $sub;

                $tree[] = $item;
            }
        }
        return $tree;
    }

    /**
     * 获取指定菜单的菜单项的树状结构 - 一维
     *
     * @param string $menuName 菜单名称
     * @return array 配置项的树状结构 - 一维
     */
    public function getFlatTree(string $menuName = 'North'): array
    {
        $menuItemFlatTree = [];
        $menuItems = $this->getItems($menuName);
        $this->makeFlatTree($menuItems, $menuItemFlatTree);
        return $menuItemFlatTree;
    }

    /**
     * 生成树
     *
     * @param $menuItems
     * @param $menuItemFlatTree
     * @param string $parentId
     * @param int $level
     */
    private function makeFlatTree(array $menuItems, array &$menuItemFlatTree, string $parentId = '', int $level = 1)
    {
        foreach ($menuItems as $menuItem) {
            if ($menuItem->parent_id === $parentId) {
                $menuItemFlatTree[] = [
                    'id' => $menuItem->id,
                    'parent_id' => $menuItem->parent_id,
                    'name' => $menuItem->name,
                    'route' => $menuItem->route,
                    'params' => $menuItem->params ? json_decode($menuItem->params, true) : [],
                    'url' => $menuItem->url,
                    'description' => $menuItem->description,
                    'target' => $menuItem->target,
                    'level' => $level,
                ];

                $this->makeFlatTree($menuItems, $menuItemFlatTree, $menuItem->id, $level + 1);
            }
        }
    }

    /**
     * 保存菜单项
     *
     * @param string $menuId 菜单ID
     * @param array $formData 菜单项数据
     * @return bool
     * @throws \Throwable
     */
    public function saveItems($menuId, $formData)
    {
        $menu = $this->getMenuById($menuId);
        if (!$menu) {
            throw new ServiceException('菜单（#' . $menuId . '）不存在！');
        }

        $menuItems = $formData['menuItems'];

        $db = Be::getDb();
        $db->startTransaction();
        try {

            $keepIds = [];
            foreach ($menuItems as $menuItem) {
                if (!isset($menuItem['id'])) {
                    throw new ServiceException('菜单项参数（id）缺失！');
                }

                if (substr($menuItem['id'], 0, 1) !== '-') {
                    $keepIds[] = $menuItem['id'];
                }
            }

            if (count($keepIds) > 0) {
                Be::newTable('system_menu_item')
                    ->where('menu_name', $menu->name)
                    ->where('id', 'NOT IN', $keepIds)
                    ->delete();
            } else {
                Be::newTable('system_menu_item')
                    ->where('menu_name', $menu->name)
                    ->delete();
            }

            $now = date('Y-m-d H:i:s');

            $parentIds = [];
            $ordering = 0;
            foreach ($menuItems as $menuItem) {
                $isNew = false;
                if (substr($menuItem['id'], 0, 1) === '-') {
                    $isNew = true;
                }

                $tupleMenuItem = Be::newTuple('system_menu_item');

                if (!$isNew) {
                    try {
                        $tupleMenuItem->load($menuItem['id']);
                    } catch (\Throwable $t) {
                        throw new ServiceException('菜单（' . $menu->name . '）下的菜单项（# ' . $menuItem['id'] . '）不存在！');
                    }
                }

                $tupleMenuItem->menu_name = $menu->name;

                $parentId = $menuItem['parent_id'] ?? '';
                $name = $menuItem['name'] ?? '';
                $route = $menuItem['route'] ?? '';
                $params = $menuItem['params'] ?? [];
                $url = $menuItem['url'] ?? '';
                $description = $menuItem['description'] ?? '';
                $target = $menuItem['target'] ?? '';

                if (substr($parentId, 0, 1) === '-') {
                    $parentId = $parentIds[$parentId];
                }

                if (!$name) {
                    throw new ServiceException('请填写第' . ($ordering + 1) . '个菜单项的名称！');
                }

                $params = json_encode($params);

                if (!in_array($target, ['_self', '_blank'])) {
                    $target = '_self';
                }

                $tupleMenuItem->parent_id = $parentId;
                $tupleMenuItem->name = $name;
                $tupleMenuItem->route = $route;
                $tupleMenuItem->params = $params;
                $tupleMenuItem->url = $url;
                $tupleMenuItem->description = $description;
                $tupleMenuItem->target = $target;

                $tupleMenuItem->ordering = $ordering;

                if (!$isNew) {
                    $tupleMenuItem->create_time = $now;
                }

                $tupleMenuItem->update_time = $now;
                $tupleMenuItem->save();

                if ($isNew) {
                    $parentIds[$menuItem['id']] = $tupleMenuItem->id;
                }

                $ordering++;
            }

            $db->commit();

            $this->update($menu->name);

        } catch (\Throwable $t) {
            $db->rollback();
            Be::getLog()->error($t);

            throw new ServiceException('保存菜单项异常！');
        }

        return true;
    }

    /**
     * 更新事台菜单
     */
    public function update($menuName)
    {
        $menus = $this->getItems($menuName);

        $code = '<?php' . "\n";
        $code .= 'namespace Be\\Data\\Cache\\Menu;' . "\n";
        $code .= "\n";
        $code .= 'class ' . $menuName . ' extends \\Be\\Menu\\Driver' . "\n";
        $code .= '{' . "\n";
        $code .= '  public function __construct()' . "\n";
        $code .= '  {' . "\n";

        foreach ($menus as $k => $v) {
            $params = [];
            if ($v->params) {
                parse_str($v->params, $parsedParams);
                if ($parsedParams) {
                    $params = $parsedParams;
                }
            }
            $code .= '    $this->addItem(\'' . $v->id . '\', \'' . $v->parent_id . '\', \'' . $v->name . '\', \'' . $v->route . '\', ' . var_export($params, true) . ', \'' . $v->url . '\', \'' . $v->target . '\');' . "\n";
        }

        $code .= '  }' . "\n";
        $code .= '}' . "\n";

        $runtime = Be::getRuntime();

        $path = $runtime->getCachePath() . '/Menu/' . $menuName . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        file_put_contents($path, $code, LOCK_EX);
        chmod($path, 0777);

        if ($runtime->isSwooleMode()) {
            // Swoole 模式下，需重新加载服务器
            $runtime->reload();
        }
    }

}
