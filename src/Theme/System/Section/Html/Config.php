<?php
namespace Be\Theme\System\Section\Html;


/**
 * @BeConfig("HTML源码", icon="el-icon-fa fa-file-code-o")
 */
class Config
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public int $enable = 1;

    /**
     * @BeConfigItem("内容",
     *     driver="FormItemCode",
     *     language="html"
     * )
     */
    public string $content = '';

}
