<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("www 配置")
 */
class Www
{

    /**
     * @BeConfigItem("www 目录写入外部存储",
     *     description="<ul><li>将应用，主题中的 www 相关资源（如：js,css等静态文件）写入外部对象存储</li><li>开启后通过应用，主题管理界面的 '更新www' 功能刷新写入</li><li>本选项仅开启写入，生效需进一步开启 '启用存储系统域名接入'</li><li>www目录默认写入本地存储，因此当存储类型为本地文件时，此选项不生效</li></ul>",
     *     driver="FormItemSwitch"
     * )
     */
    public int $cdnWrite = 0;

    /**
     * @BeConfigItem("启用存储系统域名接入",
     *     description="<ul><li>如果使用了非本地文件存储，启用本项前请先确认资源文件已成功写入存储系统，很重要！！！</li><li>启用后将通过存储系统的域名访问相关资源</li><li>通常以CDN加速存储系统的域名，以获得更快的速度</li></ul>",
     *     driver="FormItemSwitch"
     * )
     */
    public int $cdnEffect = 0;


}
