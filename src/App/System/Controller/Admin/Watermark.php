<?php

namespace Be\App\System\Controller\Admin;

use Be\Be;

/**
 * @BeMenuGroup("管理")
 * @BePermissionGroup("管理")
 */
class Watermark extends Auth
{

    /**
     * @BePermission("水印测试", ordering="2.72")
     */
    public function test()
    {
        $response = Be::getResponse();

        $src = Be::getRuntime()->getRootPath() . Be::getProperty('App.System')->getPath() . '/Template/Admin/Watermark/images/material.jpg';
        $dst = Be::getRuntime()->getUploadPath() . '/tmp/watermark-rendering.jpg';

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