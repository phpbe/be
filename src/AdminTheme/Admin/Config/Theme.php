<?php
namespace Be\AdminTheme\Admin\Config;

/**
 * @BeConfig("主题")
 */
class Theme
{

    /**
     * @BeConfigItem("JS/CSS等包是否存放本地",
     *     driver = "FormItemSwitch")
     */
    public $localAssetLib = 0;


}
