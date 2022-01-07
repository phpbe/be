<?php
namespace Be\Theme\Sample\Config\Section\Slider;


/**
 * @BeConfig("图像和文字子组件", icon="el-icon-picture-outline")
 */
class ImageWithTextOverlay
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public $enable = 1;

    /**
     * @BeConfigItem("图像",
     *     driver="FormItemImage",
     *     path = "/Theme/Sample/Section/Slider/ImageWithTextOverlay/image/")
     */
    public $image = '';

    /**
     * @BeConfigItem("手机版图像",
     *     driver="FormItemImage",
     *     path = "/Theme/Sample/Section/Slider/ImageWithTextOverlay/imageMobile/")
     */
    public $imageMobile = '';

    /**
     * @BeConfigItem("标题",
     *     driver = "FormItemInput"
     * )
     */
    public $contentTitle = '标题...';

    /**
     * @BeConfigItem("标题文字大小（像素）",
     *     driver = "FormItemInputNumberInt",
     * )
     */
    public $contentTitleFontSize = 40;

    /**
     * @BeConfigItem("标题颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public $contentTitleColor = '#fff';

    /**
     * @BeConfigItem("描述",
     *     driver = "FormItemInputTextArea"
     * )
     */
    public $contentDescription = '描述...';

    /**
     * @BeConfigItem("描述文字大小（像素）",
     *     driver = "FormItemInputNumberInt",
     * )
     */
    public $contentDescriptionFontSize = 16;

    /**
     * @BeConfigItem("描述文字颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public $contentDescriptionColor = '#fff';

    /**
     * @BeConfigItem("按钮",
     *     driver = "FormItemInput"
     * )
     */
    public $contentButton = '查看';

    /**
     * @BeConfigItem("按钮链接",
     *     driver = "FormItemInput"
     * )
     */
    public $contentButtonLink = '#';

    /**
     * @BeConfigItem("按钮颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public $contentButtonColor = '#FFF';

    /**
     * @BeConfigItem("内容区宽度（像素）",
     *     driver = "FormItemInputNumberInt"
     * )
     */
    public $contentWidth = '400';

    /**
     * @BeConfigItem("内容区位置",
     *     driver = "FormItemSelect",
     *     keyValues = "return ['left' => '左侧', 'center' => '中间', 'right' => '右侧', 'custom' => '指定位置'];"
     * )
     */
    public $contentPosition = 'right';

    /**
     * @BeConfigItem("内容区位置左侧边距（像素，小于0时不生效）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.contentPosition === \'custom\'']];"
     * )
     */
    public $contentPositionLeft = -1;

    /**
     * @BeConfigItem("内容区位置右侧边距（像素，小于0时不生效）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.contentPosition === \'custom\'']];"
     * )
     */
    public $contentPositionRight = 30;

    /**
     * @BeConfigItem("内容区位置顶部边距（像素，小于0时不生效）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.contentPosition === \'custom\'']];"
     * )
     */
    public $contentPositionTop = 30;

    /**
     * @BeConfigItem("内容区位置底部边距（像素，小于0时不生效）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.contentPosition === \'custom\'']];"
     * )
     */
    public $contentPositionBottom = -1;


}
