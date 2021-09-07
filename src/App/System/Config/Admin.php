<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("后台设置")
 */
class Admin
{
    /**
     * @BeConfigItem("主题",
     *     driver="FormItemSelect",
     *     keyValues = "return \Be\Be::getService('App.System.Admin.AdminTheme')->getEnableThemeKeyValues();")
     */
    public $theme = 'Admin';

    /**
     * @BeConfigItem("默认首页", driver="FormItemInput")
     */
    public $home = 'System.Index.index';

    /**
     * @BeConfigItem("默认分页",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 1];")
     */
    public $pageSize = 12;

}
