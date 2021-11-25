<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("路由器")
 */
class Router
{

    /**
     * @BeConfigItem("REDIS库",
     *     driver="FormItemSelect",
     *     keyValues = "return \Be\Session\SessionHelper::getConfigRedisKeyValues();")
     */
    public $redis = 'master';

    /**
     * @BeConfigItem("内存缓存",
     *     driver="FormItemSwitch",
     *     description="开启内存缓存占用较多内存")
     */
    public $cache = 0;


}
