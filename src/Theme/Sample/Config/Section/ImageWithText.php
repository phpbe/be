<?php
namespace Be\Theme\Sample\Config\Section;


/**
 * @BeConfig("图片和文字")
 */
class ImageWithText
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public $enable = 1;

    /**
     * @BeConfigItem("图像",
     *     driver="FormItemImage",
     *     path = "/Theme/Sample/Section/ImageWithText/image/")
     */
    public $image = '';

    /**
     * @BeConfigItem("图像位置",
     *     driver = "FormItemSelect",
     *     keyValues = "return ['left' => '左侧', 'right' => '右侧'];"
     * )
     */
    public $imagePosition = 'left';

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
     *     driver = "FormItemInputTextarea"
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
     * @BeConfigItem("内容背景颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public $contentBackgroundColor = '#FAFAFA';

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


}
