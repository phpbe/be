<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("www 配置", enable="return \Be\Be::getConfig('App.System.Storage')->driver !== 'LocalDisk';")
 */
class Www
{

    /**
     * @BeConfigItem("www 目录写入外部存储",
     *     description="将相关资源（如：js,css等静态文件）写入外部对象存储，通过CDN访问获去更快的速度",
     *     driver="FormItemSwitch"
     * )
     */
    public int $storage = 0;

    /**
     * @BeConfigItem("存储路径",
     *     driver="FormItemInput",
     *     ui="return ['form-item' => ['v-show' => 'formData.storage === 1']];"
     * )
     */
    public string $storageRoot = '/';

    /**
     * @BeConfigItem("启用外部存储",
     *     description="先确保相关应用及主题的资源写入外部对象存储成功后再启用",
     *     driver="FormItemSwitch",
     *     ui="return ['form-item' => ['v-show' => 'formData.storage === 1']];"
     * )
     */
    public int $storageEffect = 0;



}
