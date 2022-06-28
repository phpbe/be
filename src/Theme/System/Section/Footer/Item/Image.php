<?php
namespace Be\Theme\System\Section\Footer\Item;

/**
 * @BeConfig("图像", icon="el-icon-picture")
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
     * @BeConfigItem("宽度",
     *     description="像素或百分比，例: 80%、200px",
     *     driver = "FormItemInput",
     * )
     */
    public string $width = '100px';

    /**
     * @BeConfigItem("对齐方式",
     *     driver="FormItemSelect",
     *     keyValues="return ['left' => '居左', 'center' => '居中', 'right' => '居右']"
     * )
     */
    public string $align = 'center';

    /**
     * @BeConfigItem("所占列数",
     *     description="底部默认有4列",
     *     values="return [1, 2, 3, 4]",
     *     driver="FormItemSelect")
     */
    public int $cols = 1;

}
