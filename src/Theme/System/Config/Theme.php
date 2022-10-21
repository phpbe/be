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
    public string $majorColor = '#FF6600';

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

}
