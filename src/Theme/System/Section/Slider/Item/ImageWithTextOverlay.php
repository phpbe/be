<?php
namespace Be\Theme\System\Section\Slider\Item;


/**
 * @BeConfig("图像和文字子组件", icon="el-icon-picture-outline")
 */
class ImageWithTextOverlay
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public int $enable = 1;

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
     * @BeConfigItem("标题",
     *     driver = "FormItemInput"
     * )
     */
    public string $contentTitle = '标题...';

    /**
     * @BeConfigItem("标题文字大小（像素）",
     *     driver = "FormItemInputNumberInt",
     * )
     */
    public int $contentTitleFontSize = 40;

    /**
     * @BeConfigItem("标题颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public string $contentTitleColor = '#fff';

    /**
     * @BeConfigItem("描述",
     *     driver = "FormItemInputTextArea"
     * )
     */
    public string $contentDescription = '描述...';

    /**
     * @BeConfigItem("描述文字大小（像素）",
     *     driver = "FormItemInputNumberInt",
     * )
     */
    public int $contentDescriptionFontSize = 16;

    /**
     * @BeConfigItem("描述文字颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public string $contentDescriptionColor = '#fff';

    /**
     * @BeConfigItem("按钮",
     *     driver = "FormItemInput"
     * )
     */
    public string $contentButton = '查看';

    /**
     * @BeConfigItem("按钮链接",
     *     driver = "FormItemInput"
     * )
     */
    public string $contentButtonLink = '#';

    /**
     * @BeConfigItem("按钮颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public string $contentButtonColor = '#FFF';

    /**
     * @BeConfigItem("内容区宽度（像素）",
     *     driver = "FormItemInputNumberInt"
     * )
     */
    public string $contentWidth = '400';

    /**
     * @BeConfigItem("内容区位置",
     *     driver = "FormItemSelect",
     *     keyValues = "return ['left' => '左侧', 'center' => '中间', 'right' => '右侧', 'custom' => '指定位置'];"
     * )
     */
    public string $contentPosition = 'right';

    /**
     * @BeConfigItem("内容区位置左侧边距（像素，小于0时不生效）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.contentPosition === \'custom\'']];"
     * )
     */
    public int $contentPositionLeft = -1;

    /**
     * @BeConfigItem("内容区位置右侧边距（像素，小于0时不生效）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.contentPosition === \'custom\'']];"
     * )
     */
    public int $contentPositionRight = 30;

    /**
     * @BeConfigItem("内容区位置顶部边距（像素，小于0时不生效）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.contentPosition === \'custom\'']];"
     * )
     */
    public int $contentPositionTop = 30;

    /**
     * @BeConfigItem("内容区位置底部边距（像素，小于0时不生效）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.contentPosition === \'custom\'']];"
     * )
     */
    public int $contentPositionBottom = -1;


}
