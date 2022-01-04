<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("存储-阿里云OSS", enable="return \Be\Be::getConfig('App.System.Storage')->driver === 'AliyunOss';")
 */
class StorageAliyunOss
{

    /**
     * @BeConfigItem("内网访问", driver="FormItemSwitch")
     */
    public $internal = 0;

    /**
     * @BeConfigItem("AccessKey ID", driver="FormItemInput")
     */
    public $accessKeyId = '';

    /**
     * @BeConfigItem("AccessKey Secret", driver="FormItemInput")
     */
    public $accessKeySecret = '';

    /**
     * @BeConfigItem("地域ID", driver="FormItemInput")
     */
    public $regionId = 'oss-cn-shenzhen';

    /**
     * @BeConfigItem("访问域名", driver="FormItemInput")
     */
    public $endpoint = 'https://oss-cn-shenzhen.aliyuncs.com';

    /**
     * @BeConfigItem("访问域名（内网）", driver="FormItemInput")
     */
    public $endpointInternal = 'https://oss-cn-shenzhen-internal.aliyuncs.com';

    /**
     * @BeConfigItem("Bucket", driver="FormItemInput")
     */
    public $bucket = 'phpbe';

    /**
     * @BeConfigItem("访问网址", driver="FormItemInput")
     */
    public $rootUrl = 'https://cdn.phpbe.com';


}
