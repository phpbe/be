<?php

namespace Be\AdminPlugin\Toolbar\Item;


/**
 * 工具栏 自定义
 */
class ToolbarItemCustom extends ToolbarItem
{

    /**
     * 获取html内容
     *
     * @return string
     */
    public function getHtml(): string
    {
        return $this->value;
    }

}
