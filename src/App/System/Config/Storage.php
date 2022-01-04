<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("存储")
 */
class Storage
{

    /**
     * @BeConfigItem("驱动",
     *     driver="FormItemSelect",
     *     keyValues = "return ['LocalDisk' => '本地磁盘', 'AliyunOss' => '阿里云OSS'];")
     */
    public $driver = 'LocalDisk';


}
