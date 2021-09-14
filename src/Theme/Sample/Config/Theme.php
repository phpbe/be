<?php
namespace Be\Theme\Sample\Config;

/**
 * @BeConfig("主题")
 */
class Theme
{

    /**
     * @BeConfigItem("页面宽度",
     *     driver="FormItemSelect",
     *     keyValues = "return ['default' => '默认', 'fullWidth' => '全屏', 'customWidth' => '自定义'];"
     * )
     */
    public $width = 'default';

    /**
     * @BeConfigItem("页面宽度（px）",
     *     driver="FormItemSlider",
     *     ui="return [':min' => 1200, ':max' => 1600, 'form-item' => ['v-show' => 'formData.width == \'customWidth\'']];"
     * )
     */
    public $customWidth = '1200';

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
