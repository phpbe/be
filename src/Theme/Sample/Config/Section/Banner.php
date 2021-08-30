<?php
namespace Be\Theme\Sample\Config\Section;


/**
 * @BeConfig("广告位")
 */
class Banner
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public $enable = 1;

    /**
     * @BeConfigItem("鼠标悬停效果",
     *     driver = "FormItemSelect",
     *     keyValues = "return ['none' => '无', 'scale' => '放大', 'rotateScale' => '旋转放大'];"
     * )
     */
    public $hoverEffect = 'rotateScale';

    /**
     * @BeConfigItem("顶部外边距（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public $marginTop = 20;

    /**
     * @BeConfigItem("左右外边距（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public $marginLeftRight = 0;

    /**
     * @BeConfigItem("内部间距（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public $spacing = 30;

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
