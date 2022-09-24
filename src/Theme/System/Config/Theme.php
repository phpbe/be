<?php
namespace Be\Theme\System\Config;

/**
 * @BeConfig("主题")
 */
class Theme
{

    /**
     * @BeConfigItem("主色调",
     *     driver="FormItemColorPicker")
     */
    public string $mainColor = '#FF6600';

    /**
     * @BeConfigItem("字体大小",
     *     driver="FormItemInputNumberInt")
     */
    public int $fontSize = 16;

    /**
     * @BeConfigItem("背景颜色",
     *     driver="FormItemColorPicker")
     */
    public string $backgroundColor = '#F5F5F5';

    /**
     * @BeConfigItem("字体颜色",
     *     driver="FormItemColorPicker")
     */
    public string $fontColor = '#333333';

    /**
     * @BeConfigItem("超链接颜色",
     *     driver="FormItemColorPicker")
     */
    public string $linkColor = '#333333';

    /**
     * @BeConfigItem("超链接悬停颜色",
     *     driver="FormItemColorPicker")
     */
    public string $linkHoverColor = '#FF6600';

    /**
     * @BeConfigItem("加载 be-icons 图标",
     *     description="如果页面中未用到 be-icons 可选择不加载，以节省流量",
     *     driver = "FormItemSwitch")
     */
    public int $loadBeIcons = 0;


}
