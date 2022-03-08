<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("存储-腾讯云COS", enable="return \Be\Be::getConfig('App.System.Storage')->driver === 'TencentCos';")
 */
class StorageTencentCos
{

    /**
     * @BeConfigItem("永久密钥 SecretId", driver="FormItemInput", description="请登录访问管理控制台进行查看和管理，https://console.cloud.tencent.com/cam/capi")
     */
    public $secretId = '';

    /**
     * @BeConfigItem("永久密钥 SecretKey", driver="FormItemInput", description="请登录访问管理控制台进行查看和管理，https://console.cloud.tencent.com/cam/capi")
     */
    public $secretKey = '';

    /**
     * @BeConfigItem("临时密钥 Token", driver="FormItemInput", description="如果使用永久密钥则不需要填入临时密钥，如果使用临时密钥需要填入，临时密钥生成和使用指引参见https://cloud.tencent.com/document/product/436/14048")
     */
    public $token = '';

    /**
     * @BeConfigItem("地域", driver="FormItemInput", description="已创建桶归属的region可以在控制台查看，https://console.cloud.tencent.com/cos5/bucket")
     */
    public $region = 'ap-guangzhou';

    /**
     * @BeConfigItem("存储桶名称", driver="FormItemInput", description="由BucketName-Appid 组成，可以在COS控制台查看 https://console.cloud.tencent.com/cos5/bucket")
     */
    public $bucket = '';

    /**
     * @BeConfigItem("传输协议", driver="FormItemSelect", values="return ['http', 'https']")
     */
    public $schema = 'https';

    /**
     * @BeConfigItem("访问网址", driver="FormItemInput", description="对象存储的访问域名，您上传的文件将通过该域名访问")
     */
    public $rootUrl = '';


}
