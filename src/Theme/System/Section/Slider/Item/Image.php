<?php
namespace Be\Theme\System\Section\Slider\Item;

/**
 * @BeConfig("图像子组件", icon="el-icon-picture")
 */
class Image
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
     * @BeConfigItem("链接",
     *     driver="FormItemInput")
     */
    public string $link = '';

}
