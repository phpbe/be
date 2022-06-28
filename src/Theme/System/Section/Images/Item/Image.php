<?php
namespace Be\Theme\System\Section\Images\Item;

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
     * @BeConfigItem("链接",
     *     driver="FormItemInput")
     */
    public string $link = '';

}
