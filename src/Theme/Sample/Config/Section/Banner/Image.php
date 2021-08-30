<?php
namespace Be\Theme\Sample\Config\Section\Banner;

/**
 * @BeConfig("图像子组件")
 */
class Image
{
    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public $enable = 1;

    /**
     * @BeConfigItem("图像",
     *     driver="FormItemImage",
     *     path = "/Theme/Sample/Section/Banner/Image/")
     */
    public $image = '';

    /**
     * @BeConfigItem("链接",
     *     driver="FormItemInput")
     */
    public $link = '';

}
