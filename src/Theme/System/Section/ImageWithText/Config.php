<?php
namespace Be\Theme\System\Section\ImageWithText;


/**
 * @BeConfig("图像+文字", icon="el-icon-picture")
 */
class Config
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public int $enable = 1;

    /**
     * @BeConfigItem("宽度",
     *     description="位于middle时有效",
     *     driver="FormItemSelect",
     *     keyValues = "return ['default' => '默认', 'fullWidth' => '全屏'];"
     * )
     */
    public string $width = 'default';

    /**
     * @BeConfigItem("图像",
     *     driver="FormItemStorageImage"
     * )
     */
    public string $image = '';

    /**
     * @BeConfigItem("手机版图像",
     *     driver="FormItemStorageImage"
     * )
     */
    public string $imageMobile = '';

    /**
     * @BeConfigItem("图像位置",
     *     driver = "FormItemSelect",
     *     keyValues = "return ['left' => '左侧', 'right' => '右侧'];"
     * )
     */
    public string $imagePosition = 'left';

    /**
     * @BeConfigItem("内容标题",
     *     driver = "FormItemInput"
     * )
     */
    public string $contentTitle = '标题...';

    /**
     * @BeConfigItem("内容标题文字大小（像素）",
     *     driver = "FormItemInputNumberInt",
     * )
     */
    public int $contentTitleFontSize = 20;

    /**
     * @BeConfigItem("内容标题颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public string $contentTitleColor = '#232323';

    /**
     * @BeConfigItem("内容详细",
     *     driver = "FormItemInputTextArea"
     * )
     */
    public string $contentDescription = '详细...';

    /**
     * @BeConfigItem("内容详细文字大小（像素）",
     *     driver = "FormItemInputNumberInt",
     * )
     */
    public int $contentDescriptionFontSize = 16;

    /**
     * @BeConfigItem("内容详细颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public string $contentDescriptionColor = '#232323';

    /**
     * @BeConfigItem("内容按钮",
     *     driver = "FormItemInput"
     * )
     */
    public string $contentButton = '查看';

    /**
     * @BeConfigItem("内容按钮链接",
     *     driver = "FormItemInput"
     * )
     */
    public string $contentButtonLink = '#';

    /**
     * @BeConfigItem("内容按钮颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public string $contentButtonColor = '#FFFFFF';

    /**
     * @BeConfigItem("内容背景颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public string $contentBackgroundColor = '#FAFAFA';

    /**
     * @BeConfigItem("背景颜色",
     *     driver="FormItemColorPicker"
     * )
     */
    public string $backgroundColor = '#FFFFFF';

    /**
     * @BeConfigItem("内边距 （电脑端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS padding 语法）"
     * )
     */
    public string $paddingDesktop = '0';

    /**
     * @BeConfigItem("内边距 （平板端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS padding 语法）"
     * )
     */
    public string $paddingTablet = '0';

    /**
     * @BeConfigItem("内边距 （手机端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS padding 语法）"
     * )
     */
    public string $paddingMobile = '0';

    /**
     * @BeConfigItem("外边距 （电脑端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS margin 语法）"
     * )
     */
    public string $marginDesktop = '2rem 0';

    /**
     * @BeConfigItem("外边距 （平板端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS margin 语法）"
     * )
     */
    public string $marginTablet = '1.75rem 0';

    /**
     * @BeConfigItem("外边距 （手机端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS margin 语法）"
     * )
     */
    public string $marginMobile = '1.5rem 0';


}
