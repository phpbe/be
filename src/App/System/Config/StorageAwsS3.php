<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("存储-亚驰逊AWS S3", enable="return \Be\Be::getConfig('App.System.Storage')->driver === 'AwsS3';")
 */
class StorageAwsS3
{

    /**
     * @BeConfigItem("AccessKey ID", driver="FormItemInput")
     */
    public string $key = '';

    /**
     * @BeConfigItem("AccessKey Secret", driver="FormItemInput")
     */
    public string $secret = '';

    /**
     * @BeConfigItem("区域（region）", driver="FormItemInput")
     */
    public string $region = 'us-west-1';

    /**
     * @BeConfigItem("存储桶名称（bucket）", driver="FormItemInput")
     */
    public string $bucket = 'phpbe';

    /**
     * @BeConfigItem("访问网址", driver="FormItemInput")
     */
    public string $rootUrl = 'https://aws-s3.cdn.phpbe.com';

}
