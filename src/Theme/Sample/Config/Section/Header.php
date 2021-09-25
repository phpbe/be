<?php
namespace Be\Theme\Sample\Config\Section;

/**
 * @BeConfig("头部")
 */
class Header
{
    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public $enable = 1;

    /**
     * @BeConfigItem("Logo类型",
     *     driver="FormItemSelect",
     *     keyValues = "return ['text' => '文字', 'image' => '图像']"
     * )
     */
    public $logoType = 'text';

    /**
     * @BeConfigItem("Logo文字",
     *     driver="FormItemInput",
     *     ui="return ['form-item' => ['v-show' => 'formData.logoType == \'text\'']];"
     * )
     */
    public $logoText = 'Beyond Exception';

    /**
     * @BeConfigItem("Logo图像",
     *     driver="FormItemImage",
     *     path = "/Theme/Sample/Section/Header/logo/",
     *     filename = "logo-{random}",
     *     ui="return ['form-item' => ['v-show' => 'formData.logoType == \'image\'']];"
     * )
     */
    public $logoImage = '';

    /**
     * @BeConfigItem("Logo图像最大宽度",
     *     driver="FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.logoType == \'image\'']];"
     * )
     */
    public $logoImageMaxWidth = '0';

    /**
     * @BeConfigItem("Logo图像最大高度",
     *     driver="FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.logoType == \'image\'']];"
     * )
     */
    public $logoImageMaxHeight = '0';

    /**
     * @BeConfigItem("背景颜色",
     *     driver="FormItemColorPicker"
     * )
     */
    public $backgroundColor = '#fff';

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


    public function __icon() {
        return '<svg viewBox="0 0 20 20" focusable="false" aria-hidden="true"><path d="M1 2.5V9h18V2.5A1.5 1.5 0 0 0 17.5 1h-15A1.5 1.5 0 0 0 1 2.5zM2 19a1 1 0 0 1-1-1v-2h2v1h1v2H2zm17-1a1 1 0 0 1-1 1h-2v-2h1v-1h2v2zM1 14v-3h2v3H1zm16-3v3h2v-3h-2zM6 17h3v2H6v-2zm8 0h-3v2h3v-2z"></path></svg>';
    }
}
