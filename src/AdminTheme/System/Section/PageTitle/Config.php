<?php
namespace Be\AdminTheme\System\Section\PageTitle;


/**
 * @BeConfig("页面主体标题", icon="el-icon-fa fa-minus", ordering="1")
 */
class Config
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public int $enable = 1;

}
