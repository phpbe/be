<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("后台设置")
 */
class Admin
{

    /**
     * @BeConfigItem("默认首页", driver="FormItemInput")
     */
    public $home = 'System.Index.index';

    /**
     * @BeConfigItem("默认分页条数（条/页）",
     *     driver="FormItemSelect",
     *     values="return [10, 12, 15, 20, 25, 30, 50, 100, 200, 500]",
     *     valueType="int")
     */
    public $pageSize = 10;

}
