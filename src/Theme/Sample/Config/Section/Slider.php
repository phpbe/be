<?php
namespace Be\Theme\Sample\Config\Section;


/**
 * @BeConfig("轮播图", icon="el-icon-video-play")
 */
class Slider
{
    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public $enable = 1;

    /**
     * @BeConfigItem("宽度",
     *     driver="FormItemSelect",
     *     keyValues = "return ['default' => '默认', 'fullWidth' => '全屏'];"
     * )
     */
    public $width = 'fullWidth';

    /**
     * @BeConfigItem("背景颜色",
     *     driver="FormItemColorPicker",
     *     ui="return ['form-item' => ['v-show' => 'formData.width == \'default\'']];"
     * )
     */
    public $backgroundColor = '#fff';

    /**
     * @BeConfigItem("自动揪放",
     *     driver = "FormItemSwitch")
     */
    public $autoplay = 1;

    /**
     * @BeConfigItem("自动播放间隔（毫秒）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.autoplay == 1']];")
     */
    public $delay = 3000;

    /**
     * @BeConfigItem("自动播放速度（毫秒）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.autoplay == 1']];")
     */
    public $speed = 300;

    /**
     * @BeConfigItem("循环",
     *     driver = "FormItemSwitch")
     */
    public $loop = 1;

    /**
     * @BeConfigItem("显示分页器",
     *     driver = "FormItemSwitch")
     */
    public $pagination = 1;

    /**
     * @BeConfigItem("分页器颜色",
     *     driver = "FormItemColorPicker",
     *     ui="return ['form-item' => ['v-show' => 'formData.pagination == 1']];")
     */
    public $paginationColor = '#FF6600';

    /**
     * @BeConfigItem("显示前进后退按钮",
     *     driver = "FormItemSwitch")
     */
    public $navigation = 1;

    /**
     * @BeConfigItem("前进后退按钮颜色",
     *     driver = "FormItemColorPicker",
     *     ui="return ['form-item' => ['v-show' => 'formData.navigation == 1']];")
     */
    public $navigationColor = '#FF6600';

    /**
     * @BeConfigItem("前进后退按钮大像（像素）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.navigation == 1']];")
     */
    public $navigationSize = 30;

    /**
     * @BeConfigItem("子项",
     *     driver = "FormItemsMixedConfigs",
     *     items = "return [
     *          \Be\Theme\Sample\Config\Section\Slider\Image::class,
     *     ]"
     * )
     */
    public $items = [];



}
