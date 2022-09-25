<?php
namespace Be\Theme\Blank\Config;

/**
 * @BeConfig("主题")
 */
class Theme
{


    /**
     * @BeConfigItem("加载 be-icons 图标",
     *     description="如果页面中未用到 be-icons 可选择不加载，以节省流量",
     *     driver = "FormItemSwitch")
     */
    public int $loadBeIcons = 0;


}
