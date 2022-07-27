<?php
namespace Be\AdminTheme\System\Section\PageContent;


/**
 * @BeConfig("页面主体内容", icon="el-icon-fa fa-navicon", ordering="2")
 */
class Config
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public int $enable = 1;


}
