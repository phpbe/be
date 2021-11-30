<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("路由器")
 */
class Router
{

    /**
     * @BeConfigItem("Hashmap路由 - REDIS库",
     *     driver="FormItemSelect",
     *     keyValues = "return \Be\Session\SessionHelper::getConfigRedisKeyValues();",
     *     description="当有使用 Hashmap路由 时才需要此配置藉由")
     */
    public $redis = 'master';

    /**
     * @BeConfigItem("Hashmap路由 - 内存缓存",
     *     driver="FormItemSwitch",
     *     description="当有使用 Hashmap路由 时才需要此配置藉由, 开启后将占用较多内存")
     */
    public $cache = 0;


}
