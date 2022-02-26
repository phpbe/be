<?php

namespace Be\Menu;

/**
 * 菜单基类
 */
class Driver
{

    protected $items = [];
    protected $tree = null;

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

        $this->items[$itemId] = $item;
    }

    /**
     * 获取菜单项列表
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * 获取菜单树
     *
     * @return array()
     */
    public function getTree()
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
    protected function createTree(string $itemId = '')
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