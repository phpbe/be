<?php
namespace Be\Theme\System\Section\Footer\Item;

/**
 * @BeConfig("版权信息", icon="el-icon-fa fa-copyright")
 */
class Copyright
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public int $enable = 1;

    /**
     * @BeConfigItem("内容",
     *     driver="FormItemTinymce"
     * )
     */
    public string $content = 'Copyright &copy; 版权所有';

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
    public int $cols = 4;

}
