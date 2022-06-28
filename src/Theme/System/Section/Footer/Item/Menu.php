<?php
namespace Be\Theme\System\Section\Footer\Item;

/**
 * @BeConfig("底部菜单", icon="el-icon-fa fa-navicon")
 */
class Menu
{
    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public int $enable = 1;

    /**
     * @BeConfigItem("展示菜单组数",
     *     description="菜单取自系统的底部菜单，菜单层数取两级",
     *     values="return [1, 2, 3, 4]",
     *     driver="FormItemSelect")
     */
    public int $quantity = 4;

    /**
     * @BeConfigItem("所占列数",
     *     description="底部默认有4列",
     *     values="return [1, 2, 3, 4]",
     *     driver="FormItemSelect")
     */
    public int $cols = 4;

}
