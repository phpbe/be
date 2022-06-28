<?php
namespace Be\Theme\System\Section\Footer\Item;

/**
 * @BeConfig("富文本", icon="el-icon-fa fa-edit")
 */
class RichText
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public int $enable = 1;

    /**
     * @BeConfigItem("标题",
     *     driver="FormItemInput"
     * )
     */
    public string $title = '富文本';

    /**
     * @BeConfigItem("内容",
     *     driver="FormItemInputTinymce"
     * )
     */
    public string $content = '在这里添加您自定义的富文本内容';

    /**
     * @BeConfigItem("所占列数",
     *     description="底部默认有4列",
     *     values="return [1, 2, 3, 4]",
     *     driver="FormItemSelect")
     */
    public int $cols = 1;

}
