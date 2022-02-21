<?php
namespace Be\Theme\Sample\Config\Section;


/**
 * @BeConfig("图像和文字", icon="el-icon-picture")
 */
class ImageWithText
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
     *     driver="FormItemStorageImage"
     * )
     */
    public $image = '';

    /**
     * @BeConfigItem("手机版图像",
     *     driver="FormItemStorageImage"
     * )
     */
    public $imageMobile = '';

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
     * @BeConfigItem("内容按钮颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public $contentButtonColor = '#FFF';

    /**
     * @BeConfigItem("内容背景颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public $contentBackgroundColor = '#FAFAFA';

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

}
