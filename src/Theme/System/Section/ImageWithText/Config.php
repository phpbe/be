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
     *     driver="FormItemSelect",
     *     keyValues = "return ['default' => '默认', 'fullWidth' => '全屏'];"
     * )
     */
    public string $width = 'fullWidth';

    /**
     * @BeConfigItem("背景颜色",
     *     driver="FormItemColorPicker"
     * )
     */
    public string $backgroundColor = '#fff';

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
    public int $contentDescriptionFontSize = 12;

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
    public string $contentButtonColor = '#FFF';

    /**
     * @BeConfigItem("内容背景颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public string $contentBackgroundColor = '#FAFAFA';

    /**
     * @BeConfigItem("顶部内边距 - 电脑端（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingTopDesktop = 40;

    /**
     * @BeConfigItem("顶部内边距 - 平板端（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingTopTablet = 30;

    /**
     * @BeConfigItem("顶部内边距 - 手机端（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingTopMobile = 20;

    /**
     * @BeConfigItem("底部内边距 - 电脑端（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingBottomDesktop = 40;

    /**
     * @BeConfigItem("底部内边距 - 平板端（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingBottomTablet = 30;

    /**
     * @BeConfigItem("底部内边距 - 手机端（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingBottomMobile = 20;

}
