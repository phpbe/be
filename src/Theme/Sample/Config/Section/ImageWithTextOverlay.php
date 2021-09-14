<?php
namespace Be\Theme\Sample\Config\Section;


/**
 * @BeConfig("图片背景和文字", icon="el-icon-picture-outline")
 */
class ImageWithTextOverlay
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
     *     driver="FormItemColorPicker"
     * )
     */
    public $backgroundColor = '#fff';

    /**
     * @BeConfigItem("图像",
     *     driver="FormItemImage",
     *     path = "/Theme/Sample/Section/ImageWithTextOverlay/image/")
     */
    public $image = '';

    /**
     * @BeConfigItem("内容标题",
     *     driver = "FormItemInput"
     * )
     */
    public $contentTitle = '标题...';

    /**
     * @BeConfigItem("内容标题文字大小（像素）",
     *     driver = "FormItemInputNumberInt",
     * )
     */
    public $contentTitleFontSize = 20;

    /**
     * @BeConfigItem("内容标题颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public $contentTitleColor = '#232323';

    /**
     * @BeConfigItem("内容详细",
     *     driver = "FormItemInputTextArea"
     * )
     */
    public $contentDescription = '详细...';

    /**
     * @BeConfigItem("内容详细文字大小（像素）",
     *     driver = "FormItemInputNumberInt",
     * )
     */
    public $contentDescriptionFontSize = 12;

    /**
     * @BeConfigItem("内容详细颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public $contentDescriptionColor = '#232323';

    /**
     * @BeConfigItem("内容按钮",
     *     driver = "FormItemInput"
     * )
     */
    public $contentButton = '查看';

    /**
     * @BeConfigItem("内容按钮链接",
     *     driver = "FormItemInput"
     * )
     */
    public $contentButtonLink = '#';

    /**
     * @BeConfigItem("内容区宽度（像素）",
     *     driver = "FormItemInputNumberInt"
     * )
     */
    public $contentWidth = '400';

    /**
     * @BeConfigItem("内容位置",
     *     driver = "FormItemSelect",
     *     keyValues = "return ['left' => '左侧', 'center' => '中间', 'right' => '右侧', 'custom' => '指定位置'];"
     * )
     */
    public $contentPosition = 'right';

    /**
     * @BeConfigItem("内容位置左侧边距（像素，小于0时不生效）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.contentPosition == \'custom\'']];"
     * )
     */
    public $contentPositionLeft = -1;

    /**
     * @BeConfigItem("内容位置右侧边距（像素，小于0时不生效）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.contentPosition == \'custom\'']];"
     * )
     */
    public $contentPositionRight = 30;


    /**
     * @BeConfigItem("内容位置顶部边距（像素，小于0时不生效）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.contentPosition == \'custom\'']];"
     * )
     */
    public $contentPositionTop = 30;

    /**
     * @BeConfigItem("内容位置底部边距（像素，小于0时不生效）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.contentPosition == \'custom\'']];"
     * )
     */
    public $contentPositionBottom = -1;


}
