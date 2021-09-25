<?php
namespace Be\Theme\Sample\Config\Section;


/**
 * @BeConfig("一组图像", icon="el-icon-menu")
 */
class Images
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
    public $hoverEffect = 'scale';

    /**
     * @BeConfigItem("背景颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public $backgroundColor = '#f5f5f5';

    /**
     * @BeConfigItem("顶部内边距 - 电脑端（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public $paddingTopDesktop = 40;

    /**
     * @BeConfigItem("顶部内边距 - 平板端（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public $paddingTopTablet = 30;

    /**
     * @BeConfigItem("顶部内边距 - 手机端（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public $paddingTopMobile = 20;

    /**
     * @BeConfigItem("底部内边距 - 电脑端（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public $paddingBottomDesktop = 40;

    /**
     * @BeConfigItem("底部内边距 - 平板端（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public $paddingBottomTablet = 30;

    /**
     * @BeConfigItem("底部内边距 - 手机端（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public $paddingBottomMobile = 20;

    /**
     * @BeConfigItem("间距 - 电脑端（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public $spacingDesktop = 40;

    /**
     * @BeConfigItem("间距 - 平板端（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public $spacingTablet = 30;

    /**
     * @BeConfigItem("间距 - 手机端（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public $spacingMobile = 20;

    /**
     * @BeConfigItem("子项",
     *     driver = "FormItemsConfigs",
     *     items = "return [
     *          \Be\Theme\Sample\Config\Section\Images\Image::class,
     *     ]"
     * )
     */
    public $items = [];



}
