<?php
namespace Be\Theme\System\Section\GroupOfIconWithText\Item;

/**
 * @BeConfig("图标和文字子组件", icon="el-icon-picture")
 */
class IconWithText
{
    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public int $enable = 1;

    /**
     * @BeConfigItem("图标类型",
     *     driver="FormItemSelect",
     *     keyValues = "return ['svg' => 'SVG图像数据', 'image' => '上传图像'];"
     * )
     */
    public string $iconType = 'image';

    /**
     * @BeConfigItem("SVG图像数据",
     *     driver="FormItemInput",
     *     ui="return ['form-item' => ['v-show' => 'formData.iconType === \'svg\'']];"
     * )
     */
    public string $iconSvg = '';

    /**
     * @BeConfigItem("上传图像",
     *     driver="FormItemStorageImage",
     *     ui="return ['form-item' => ['v-show' => 'formData.iconType === \'image\'']];"
     * )
     */
    public string $iconImage = '';

    /**
     * @BeConfigItem("标题",
     *     driver="FormItemInput")
     */
    public string $title = '标题';

    /**
     * @BeConfigItem("链接文字",
     *     driver="FormItemInput")
     */
    public string $linkText = '链接文字';

    /**
     * @BeConfigItem("链接网址",
     *     driver="FormItemInput")
     */
    public string $linkUrl = '#';

}
