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
    public int $internal = 0;

    /**
     * @BeConfigItem("AccessKey ID", driver="FormItemInput")
     */
    public string $accessKeyId = '';

    /**
     * @BeConfigItem("AccessKey Secret", driver="FormItemInput")
     */
    public string $accessKeySecret = '';

    /**
     * @BeConfigItem("地域ID", driver="FormItemInput")
     */
    public string $regionId = 'oss-cn-shenzhen';

    /**
     * @BeConfigItem("访问域名", driver="FormItemInput")
     */
    public string $endpoint = 'https://oss-cn-shenzhen.aliyuncs.com';

    /**
     * @BeConfigItem("访问域名（内网）", driver="FormItemInput")
     */
    public string $endpointInternal = 'https://oss-cn-shenzhen-internal.aliyuncs.com';

    /**
     * @BeConfigItem("Bucket", driver="FormItemInput")
     */
    public string $bucket = 'phpbe';

    /**
     * @BeConfigItem("访问网址", driver="FormItemInput")
     */
    public string $rootUrl = 'https://cdn.phpbe.com';


}
