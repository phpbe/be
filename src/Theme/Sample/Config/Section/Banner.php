<?php
namespace Be\Theme\Sample\Config\Section;


/**
 * @BeConfig("广告位", icon="el-icon-menu")
 */
class Banner
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public $enable = 1;

    /**
     * @BeConfigItem("背景颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public $backgroundColor = '#f5f5f5';

    /**
     * @BeConfigItem("顶部内边距（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public $paddingTop = 40;

    /**
     * @BeConfigItem("底部内边距（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public $paddingBottom = 40;

    /**
     * @BeConfigItem("内部间距（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public $spacing = 40;

    /**
     * @BeConfigItem("鼠标悬停效果",
     *     driver = "FormItemSelect",
     *     keyValues = "return ['none' => '无', 'scale' => '放大', 'rotateScale' => '旋转放大'];"
     * )
     */
    public $hoverEffect = 'scale';

    /**
     * @BeConfigItem("子项",
     *     driver = "FormItemsConfigs",
     *     items = "return [
     *          \Be\Theme\Sample\Config\Section\Banner\Image::class,
     *     ]"
     * )
     */
    public $items = [];



}
