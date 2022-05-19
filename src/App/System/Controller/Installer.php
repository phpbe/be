<?php

namespace Be\App\System\Controller;

use Be\Be;

class Installer
{

    public function index()
    {
        $response = Be::getResponse();

        //系统配置
        $configSystem = Be::getConfig('App.System.System');

        // 可安装配置启用
        if ($configSystem->developer === 1 && $configSystem->installable === 1) {

            // 数据库为默认配置
            $configDb = Be::getConfig('App.System.Db');
            if ($configDb->master['host'] === '127.0.0.1' &&
                $configDb->master['username'] === 'root' &&
                $configDb->master['password'] === 'root' &&
                $configDb->master['name'] === 'be') {

                // 跳转到安装页面
                $response->redirect(beAdminUrl('System.Installer.index'));
                return;
            }
        }

        // 跳转到安装页面
        $response->redirect(beUrl('System.Home.index'));
    }

}
