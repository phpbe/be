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
    public $mainColor = '#fd6506';

    /**
     * @BeConfigItem("页面字体大小",
     *     driver="FormItemInputNumberInt")
     */
    public $pageFontSize = 14;

    /**
     * @BeConfigItem("页面背景颜色",
     *     driver="FormItemColorPicker")
     */
    public $pageBackgroundColor = '#FFFFFF';

    /**
     * @BeConfigItem("页面字体颜色",
     *     driver="FormItemColorPicker")
     */
    public $pageColor = '#333333';

    /**
     * @BeConfigItem("超链接颜色",
     *     driver="FormItemColorPicker")
     */
    public $linkColor = '#333333';

    /**
     * @BeConfigItem("超链接悬停颜色",
     *     driver="FormItemColorPicker")
     */
    public $linkHoverColor = '#FF3300';

    /**
     * @BeConfigItem("默认按钮颜色",
     *     driver="FormItemColorPicker")
     */
    public $btnColor = '#333333';

    /**
     * @BeConfigItem("默认按钮背景颜色",
     *     driver="FormItemColorPicker")
     */
    public $btnBackgroundColor = '#FFFFFF';

    /**
     * @BeConfigItem("默认按钮边框颜色",
     *     driver="FormItemColorPicker")
     */
    public $btnBorderColor = '#333333';

    /**
     * @BeConfigItem("默认按钮悬停时颜色",
     *     driver="FormItemColorPicker")
     */
    public $btnHoverColor = '#FFFFFF';

    /**
     * @BeConfigItem("默认按钮悬停时背景颜色",
     *     driver="FormItemColorPicker")
     */
    public $btnHoverBackgroundColor = '#FF3300';

    /**
     * @BeConfigItem("默认按钮悬停时边框颜色",
     *     driver="FormItemColorPicker")
     */
    public $btnHoverBorderColor = '#FF3300';

}
