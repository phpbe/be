<?php
namespace Be\Theme\Sample\Config\Section\Slider;

/**
 * @BeConfig("图像子组件", icon="el-icon-picture")
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
     * @BeConfigItem("链接",
     *     driver="FormItemInput")
     */
    public $link = '';

}
