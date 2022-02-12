<?php

namespace Be\App\System\Controller\Admin;

use Be\Be;

/**
 * @BeMenuGroup("系统配置")
 * @BePermissionGroup("系统配置")
 */
class Watermark extends Auth
{

    /**
     * @BeMenu("水印测试", icon = "el-icon-fa fa-image", ordering="3.3")
     * @BePermission("水印测试", ordering="3.3")
     */
    public function test()
    {
        $response = Be::getResponse();

        $src = Be::getRuntime()->getRootPath() . Be::getProperty('App.System')->getPath() . '/Template/Admin/Watermark/images/material.jpg';
        $dst = Be::getRuntime()->getUploadPath() . '/System/Admin/Watermark/rendering.jpg';

        if (!file_exists($src)) $response->end($src . ' 不存在');
        $dir = dirname($dst);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        if (file_exists($dst)) @unlink($dst);

        copy($src, $dst);

        sleep(1);

        $serviceWatermark = Be::getService('App.System.Watermark');
        $serviceWatermark->mark($dst);

        $response->set('title', '水印测试');
        $response->display();
    }

}