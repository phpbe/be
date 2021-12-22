<?php
namespace Be\AdminTheme\Blank\Config;

/**
 * @BeConfig("主题")
 */
class Theme
{

    /**
     * @BeConfigItem("JS/CSS等包是否存放本地",
     *     driver = "FormItemSwitch")
     */
    public $localAssetLib = 1;


}
