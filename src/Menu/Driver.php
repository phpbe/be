<?php

namespace Be\Menu;

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
     * @param string $label 中文名称
     * @param string $route 路由
     * @param array $params 路由参数
     * @param string $url 网址
     * @param string $target 打开方式
     */
    public function addItem(string $itemId, string $parentId, string $label, string $route = '', array $params = [], string $url = '', string $target = '_self')
    {
        $item = new \stdClass();
        $item->id = $itemId;
        $item->parentId = $parentId;
        $item->label = $label;
        $item->route = $route;
        $item->params = $params;
        if ($route) {
            $item->url = beUrl($route, $params);
        } else {
            $item->url = $url;
        }
        $item->target = $target;
        $item->active = 0;

        $this->items[$itemId] = $item;
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

    /**
     * 设置当前生效的菜单ID
     * @param string $activeId
     * @return void
     */
    public function setActiveId(string $activeId)
    {
        $class = get_called_class();
        $pos = strrpos($class, '\\');
        if ($pos !== false) {
            $class = substr($class, $pos + 1);
        }
        $contextKey = 'Be:Menu:' . $class . ':activeId';
        Be::setContext($contextKey, $activeId);

        $this->updateActiveItems($activeId);
    }

    /**
     * 获取当前生效的菜单ID
     *
     * @return string
     */
    public function getActiveId(): string
    {
        $class = get_called_class();
        $pos = strrpos($class, '\\');
        if ($pos !== false) {
            $class = substr($class, $pos + 1);
        }
        $contextKey = 'Be:Menu:' . $class . ':activeId';
        if (Be::hasContext($contextKey)) {
            return Be::getContext($contextKey);
        }

        $activeId = '';
        
        $route = Request::getRoute();
        foreach ($this->items as $item) {
            if ($item->route) {
                if ($item->params) {
                    $paramsMatched = true;
                    foreach ($item->params as $key => $val) {
                        if ($val != Request::get($key, '')) {
                            $paramsMatched = false;
                            break;
                        }
                    }

                    if ($paramsMatched) {
                        $activeId = $item->id;
                        break;
                    }
                } else {
                    if ($route === $item->route) {
                        $activeId = $item->id;
                        break;
                    }
                }
            } else {
                if ($item->url === '/') {
                    $configSystem = Be::getConfig('App.System.System');
                    if ($configSystem->home === $route) {
                        $activeId = $item->id;
                        break;
                    }
                }
            }
        }

        $this->updateActiveItems($activeId);

        Be::setContext($contextKey, $activeId);
        return $activeId;
    }

    /**
     * 更新活动项
     *
     * @param string $activeId 活动项ID
     * @return void
     */
    public function updateActiveItems(string $activeId = null)
    {
        if ($activeId === null) {
            $this->getActiveId();
        } else {
            $parentId = $activeId;
            while ($parentId !== '') {
                $newParentId = '';
                foreach ($this->items as $item) {
                    if ($item->id === $parentId) {
                        $item->active = 1;
                        $newParentId = $item->parentId;
                        break;
                    }
                }
                $parentId = $newParentId;
            }
        }
    }

}