<?php
namespace Be\Theme\Sample\Config\Section\GroupOfIconWithText;

/**
 * @BeConfig("图标和文字子组件", icon="el-icon-picture")
 */
class IconWithText
{
    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public $enable = 1;

    /**
     * @BeConfigItem("图标类型",
     *     driver="FormItemSelect",
     *     keyValues = "return ['name' => 'Bootstrap图标', 'svg' => 'SVG图像数据', 'image' => '上传图像'];"
     * )
     */
    public $icon = 'name';

    /**
     * @BeConfigItem("Bootstrap图标",
     *     driver="FormItemInput",
     *     ui="return ['form-item' => ['v-show' => 'formData.icon === \'name\'']];"
     * )
     */
    public $iconName = 'bi bi-x-diamond';

    /**
     * @BeConfigItem("SVG图像数据",
     *     driver="FormItemInput",
     *     ui="return ['form-item' => ['v-show' => 'formData.icon === \'svg\'']];"
     * )
     */
    public $iconSvg = '';

    /**
     * @BeConfigItem("上传图像",
     *     driver="FormItemImage",
     *     path = "/Theme/Sample/Section/GroupOfIconWithText/IconWithText/iconImage/"),
     *     ui="return ['form-item' => ['v-show' => 'formData.icon === \'image\'']];"
     * )
     */
    public $iconImage = '';

    /**
     * @BeConfigItem("标题",
     *     driver="FormItemInput")
     */
    public $title = '标题';

    /**
     * @BeConfigItem("链接文字",
     *     driver="FormItemInput")
     */
    public $linkText = '链接文字';

    /**
     * @BeConfigItem("链接网址",
     *     driver="FormItemInput")
     */
    public $linkUrl = 'http://';

}
