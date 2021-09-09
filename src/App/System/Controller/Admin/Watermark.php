<?php

namespace Be\App\System\Controller\Admin;

use Be\Be;

/**
 * @BeMenuGroup("系统配置")
 * @BePermissionGroup("系统配置")
 */
class Watermark
{

    /**
     * @BeMenu("水印测试", icon = "el-icon-fa fa-image", ordering="3.3")
     * @BePermission("水印测试", ordering="3.3")
     */
    public function test()
    {
        $response = Be::getResponse();

        $src = Be::getRuntime()->getRootPath() . Be::getProperty('App.System')->getPath() . '/Template/Admin/Watermark/images/material.jpg';
        $dst = Be::getRuntime()->getUploadPath() . '/System/Watermark/rendering.jpg';

        if (!file_exists($src)) $response->end($src . ' 不存在');
        $dir = dirname($dst);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        } else {
            if (file_exists($dst)) @unlink($dst);
        }

        copy($src, $dst);

        sleep(1);

        $serviceWatermark = Be::getService('App.System.Watermark');
        $serviceWatermark->mark($dst);

        $response->set('title', '水印预览');
        $response->display();
    }

}