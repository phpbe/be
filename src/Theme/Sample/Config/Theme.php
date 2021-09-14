<?php
namespace Be\Theme\Sample\Config;

/**
 * @BeConfig("主题")
 */
class Theme
{

    /**
     * @BeConfigItem("页面字体大小",
     *     driver="FormItemInputNumberInt")
     */
    public $bodyFontSize = 12;

    /**
     * @BeConfigItem("页面背景颜色",
     *     driver="FormItemColorPicker")
     */
    public $bodyBackgroundColor = '#FFFFFF';

    /**
     * @BeConfigItem("页面字体颜色",
     *     driver="FormItemColorPicker")
     */
    public $bodyColor = '#333';

    /**
     * @BeConfigItem("超链接颜色",
     *     driver="FormItemColorPicker")
     */
    public $linkColor = '#333';

    /**
     * @BeConfigItem("超链接悬停颜色",
     *     driver="FormItemColorPicker")
     */
    public $linkHoverColor = '#333';


}
