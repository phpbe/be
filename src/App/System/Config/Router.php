<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("路由器", enable="return \Be\Be::getConfig('App.System.System')->urlRewrite === 'router';")
 */
class Router
{
    /**
     * @BeConfigItem("Hashmap路由 - 内存缓存",
     *     driver="FormItemSwitch",
     *     description="当有使用 Hashmap路由 时才需要此配置藉由, 开启后将占用较多内存")
     */
    public int $cache = 0;


}
