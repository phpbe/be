<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("存储-本地磁盘", enable="return \Be\Be::getConfig('App.System.Storage')->driver === 'LocalDisk';")
 */
class StorageLocalDisk
{

    /**
     * @BeConfigItem("访问网址", driver="FormItemInput")
     */
    public string $rootUrl = '';


}
