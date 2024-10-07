<?php

namespace Be\AdminMenu;

use Be\Be;

/**
 * 菜单基类
 */
class Driver
{

    protected array $items = [];
    protected ?array $tree = null;

    /**
     * 添加菜单项
     *
     * @param string $itemId 菜单项ID
     * @param string $parentId 父级菜单项编号， 等于空时为顶级菜单
     * @param string $icon 图标
     * @param string $label 中文名称
     * @param string $route 路由
     * @param array $params 路由参数
     * @param string $url 网址
     * @param string $target 打开方式
     */
    public function addItem(string $itemId, string $parentId, string $icon, string $label, string $route, array $params = [], string $url = '', string $target = '_self')
    {
        $item = new \stdClass();
        $item->id = $itemId;
        $item->parentId = $parentId;
        $item->icon = $icon;
        $item->label = $label;
        $item->route = $route;
        $item->params = $params;
        if ($route) {
            $item->url = beAdminUrl($route, $params);
        } else {
            $item->url = $url;
        }
        $item->target = $target;

        $this->items[$itemId] = $item;
    }

    /**
     * 设置肖前菜单的键名
     */
    public function setActiveMenuKey(string $activeMenuKey)
    {
        $contextKey = 'AdminMenu:activeMenuKey';
        Be::setContext($contextKey, $activeMenuKey);
    }

    /**
     * 获取肖前菜单的键名
     *
     * @return string
     */
    public function getActiveMenuKey(): string
    {
        $contextKey = 'AdminMenu:activeMenuKey';
        if (Be::hasContext($contextKey)) {
            return Be::getContext($contextKey);
        }

        

        $menuKey1Matched = false;
        $menuKey2Matched = false;
        $menuKey3Matched = false;
        $menuKey1Flag = Request::getAppName() . '.';
        $menuKey2Flag = Request::getAppName() . '.' . Request::getControllerName() . '.';
        $menuKey3Flag = Request::getAppName() . '.' . Request::getControllerName() . '.' . Request::getActionName();
        $menuKey1 = null;
        $menuKey2 = null;
        $menuKey3 = null;

        foreach ($this->items as $item) {
            if (!$menuKey1Matched) {
                if (strpos($item->route, $menuKey1Flag) === 0) {
                    $menuKey1Matched = true;
                    $menuKey1 = $item->route;
                }
            }
            if (!$menuKey1Matched) continue;

            if (!$menuKey2Matched) {
                if (strpos($item->route, $menuKey2Flag) === 0) {
                    $menuKey2Matched = true;
                    $menuKey2 = $item->route;
                }
            }
            if (!$menuKey2Matched) continue;

            if (!$menuKey3Matched) {
                if (strpos($item->route, $menuKey3Flag) === 0) {
                    $menuKey3Matched = true;
                    $menuKey3 = $item->route;
                    break;
                }
            }
        }

        if ($menuKey3Matched) {
            Be::setContext($contextKey, $menuKey3);
            return $menuKey3;
        }

        if ($menuKey2Matched) {
            Be::setContext($contextKey, $menuKey2);
            return $menuKey2;
        }

        if ($menuKey1Matched) {
            Be::setContext($contextKey, $menuKey1);
            return $menuKey1;
        }

        Be::setContext($contextKey, $menuKey3Flag);
        return $menuKey3Flag;
    }

    /**
     * 获取菜单项列表
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * 获取菜单树
     *
     * @return array
     */
    public function getTree(): array
    {
        if ($this->tree === null) {
            $this->tree = $this->createTree();
        }
        return $this->tree;
    }

    /**
     * 获取当前位置
     *
     * @param string $url 网址
     * @return array
     */
    public function getPathwayByUrl($url): array
    {
        $itemId = null;
        foreach ($this->items as $item) {
            if ($item->url === $url) {
                $itemId = $item->id;
                break;
            }
        }

        if ($itemId === null) return [];
        return $this->getPathway($itemId);
    }

    /**
     * 获取当前位置
     *
     * @param string $itemId
     * @return array
     */
    public function getPathway(string $itemId = ''): array
    {
        $route = [];
        if (isset($this->items[$itemId])) {
            $route[] = $this->items[$itemId];
            $parentId = $this->items[$itemId]->parentId;
            while ($parentId) {
                if (isset($this->items[$parentId])) {
                    $route[] = $this->items[$parentId];
                    $parentId = $this->items[$parentId]->parentId;
                } else {
                    $parentId = '';
                }
            }
        }
        $route = array_reverse($route, true);
        return $route;
    }

    /**
     * 创建菜单树
     * @param string $itemId
     * @return array
     */
    protected function createTree(string $itemId = ''): array
    {
        $subItems = [];
        foreach ($this->items as $item) {
            if ($item->parentId === $itemId) {
                $item->subItems = $this->createTree($item->id);
                $subItems[] = $item;
            }
        }

        return $subItems;
    }

}