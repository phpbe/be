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
    public string $majorColor = '#ff5c35';

    /**
     * @BeConfigItem("搭配颜色",
     *     driver="FormItemColorPicker")
     */
    public string $minorColor = '#213343';

    /**
     * @BeConfigItem("字体大小",
     *     driver="FormItemInputNumberInt")
     */
    public int $fontSize = 16;

    /**
     * @BeConfigItem("背景颜色",
     *     driver="FormItemColorPicker")
     */
    public string $backgroundColor = '#f6f9fc';

    /**
     * @BeConfigItem("字体颜色",
     *     driver="FormItemColorPicker")
     */
    public string $fontColor = '#2e475d';

    /**
     * @BeConfigItem("链接颜色", driver="FormItemColorPicker")
     */
    public string $linkColor = '#2e475d';

    /**
     * @BeConfigItem("链接悬停颜色", driver="FormItemColorPicker")
     */
    public string $linkHoverColor = '#ff5c35';

}
