<?php

namespace Be\App\System\Controller\Admin;

use Be\App\ControllerException;
use Be\Config\ConfigHelper;
use Be\AdminPlugin\Detail\Item\DetailItemIcon;
use Be\AdminPlugin\Form\Item\FormItemAutoComplete;
use Be\AdminPlugin\Form\Item\FormItemCustom;
use Be\AdminPlugin\Form\Item\FormItemInputNumberInt;
use Be\Be;
use Be\Util\Crypt\Random;


/**
 * 安装器
 */
class Installer
{

    private $steps = null;

    public function __construct()
    {
        $config = Be::getConfig('App.System.System');
        if (!$config->developer || !$config->installable) {
            throw new ControllerException('请先开启系统配置中的 "开发者模式" 和 "可安装及重装" 配置项！');
        }

        $this->steps = ['环境检测', '配置数据库', '安装应用', '初始化系统', '完成'];
    }

    /**
     * 安装首页
     */
    public function index()
    {
        
        Resonse::redirect(beAdminUrl('System.Installer.detect'));
    }

    /**
     * 检测环境
     */
    public function detect()
    {
        
        

        $runtime = Be::getRuntime();

        if (Request::isPost()) {

            $rootPath = $runtime->getRootPath();
            $dataPath = $rootPath . '/data';
            $wwwPath = $rootPath . '/www';

            if (!is_dir($dataPath)) {
                mkdir($dataPath, 0777, true);
                @chmod($dataPath, 0777);
            }

            if (!is_dir($wwwPath)) {
                mkdir($wwwPath, 0777, true);
                @chmod($wwwPath, 0777);
            }

            Resonse::redirect(beAdminUrl('System.Installer.configDb'));
        } else {
            $value = [];
            $value['isPhpVersionGtMatch'] = version_compare(PHP_VERSION, '7.4.0') >= 0 ? 1 : 0;
            $value['isPdoMysqlInstalled'] = extension_loaded('pdo_mysql') ? 1 : 0;
            $value['isRedisInstalled'] = extension_loaded('redis') ? 1 : 0;

            $rootPath = $runtime->getRootPath();
            $dataPath = $rootPath . '/data';
            $wwwPath = $rootPath . '/www';

            if (is_dir($dataPath)) {
                $value['isDataDirWritable'] = is_writable($dataPath) ? 1 : 0;
            } else {
                $value['isDataDirWritable'] = is_writable($rootPath) ? 1 : 0;
            }

            if (is_dir($wwwPath)) {
                $value['isWwwDirWritable'] = is_writable($wwwPath) ? 1 : 0;
            } else {
                $value['isWwwDirWritable'] = is_writable($rootPath) ? 1 : 0;
            }

            Resonse::set('value', $value);

            $isAllPassed = array_sum($value) === count($value);
            Resonse::set('isAllPassed', $isAllPassed);

            Resonse::set('steps', $this->steps);
            Resonse::set('step', 1);
            Resonse::set('title', $this->steps[0]);

            Resonse::display('App.System.Admin.Installer.detect', 'Installer');
        }
    }

    /**
     * 数据库配置
     */
    public function configDb()
    {
        
        

        if (Request::isPost()) {
            try {
                $postData = Request::post();
                Be::getService('App.System.Admin.Installer')->testDb($postData);

                $configDb = Be::getConfig('App.System.Db');

                $configDbDefault = new \Be\App\System\Config\Db();
                foreach ($configDbDefault->master as $k => $v) {
                    if (isset($postData[$k])) {
                        $configDb->master[$k] = $postData[$k];
                    }
                }

                ConfigHelper::update('App.System.Db', $configDb);

                Resonse::set('success', true);
                Resonse::set('redirectUrl', beAdminUrl('System.Installer.installApp'));
                Resonse::json();
            } catch (\Throwable $t) {
                Resonse::set('success', false);
                Resonse::set('message', $t->getMessage());
                Resonse::json();
            }
        } else {
            Resonse::set('steps', $this->steps);
            Resonse::set('step', 2);

            Resonse::set('title', $this->steps[1]);

            $configDb = Be::getConfig('App.System.Db');
            Resonse::set('configDb', $configDb);

            Resonse::display('App.System.Admin.Installer.configDb', 'Installer');
        }
    }

    /**
     * 测试数据库连接
     */
    public function testDb()
    {
        
        
        try {
            $postData = Request::post();
            $databases = Be::getService('App.System.Admin.Installer')->testDb($postData);
            Resonse::set('success', true);
            Resonse::set('message', '数据库连接成功！');
            Resonse::set('data', [
                'databases' => $databases,
            ]);
            Resonse::json();
        } catch (\Throwable $t) {
            Resonse::set('success', false);
            Resonse::set('message', $t->getMessage());
            Resonse::json();
        }
    }

    /**
     * 安装应用
     */
    public function installApp()
    {
        
        
        if (Request::isPost()) {
            try {
                $postData = Request::post();
                $service = Be::getService('App.System.Admin.App');
                if (isset($postData['names']) && is_array($postData['names']) && count($postData['names'])) {
                    foreach ($postData['names'] as $appName) {
                        $service->install($appName);
                    }
                }
                Resonse::set('success', true);
                Resonse::set('redirectUrl', beAdminUrl('System.Installer.setting'));
                Resonse::json();
            } catch (\Throwable $t) {
                Resonse::set('success', false);
                Resonse::set('message', $t->getMessage());
                Resonse::json();
            }
        } else {
            Resonse::set('steps', $this->steps);
            Resonse::set('step', 3);
            Resonse::set('title', $this->steps[2]);

            $appProperties = [];
            $property = Be::getProperty('App.System');
            $appProperties[] = [
                'name' => $property->getName(),
                'icon' => $property->getIcon(),
                'label' => $property->getLabel(),
                'description' => $property->getDescription(),
            ];
            $appNames = Be::getService('App.System.Admin.Installer')->getAppNames();
            foreach ($appNames as $appName) {
                $property = Be::getProperty('App.' . $appName);
                $appProperties[] = [
                    'name' => $property->getName(),
                    'icon' => $property->getIcon(),
                    'label' => $property->getLabel(),
                    'description' => $property->getDescription(),
                ];
            }

            Resonse::set('appProperties', $appProperties);
            Resonse::display('App.System.Admin.Installer.installApp', 'Installer');
        }
    }

    /**
     * 配置系统
     */
    public function setting()
    {
        
        

        $tuple = Be::getTuple('system_admin_user');
        $tuple->loadBy('username', 'admin');

        if (Request::isPost()) {
            try {
                $postData = Request::post();

                if (!isset($postData['username'])) {
                    throw new ControllerException('超级管理员账号缺失！');
                }
                $postData['username'] = trim($postData['username']);
                if ($postData['username'] === '') {
                    throw new ControllerException('超级管理员账号不能为空！');
                }

                if (!isset($postData['password']) || !$postData['password']) {
                    throw new ControllerException('密码缺失！');
                }
                $postData['password'] = trim($postData['password']);
                if ($postData['password'] === '') {
                    throw new ControllerException('密码不能为空！');
                }

                if (!isset($postData['name']) || !$postData['name']) {
                    throw new ControllerException('名称缺失！');
                }
                $postData['name'] = trim($postData['name']);
                if ($postData['name'] === '') {
                    throw new ControllerException('名称不能为空！');
                }

                $tuple->username = $postData['username'];
                $tuple->salt = Random::complex(32);
                $tuple->password = Be::getService('App.System.Admin.AdminUser')->encryptPassword($postData['password'], $tuple->salt);
                $tuple->name = $postData['name'];
                $tuple->email = $postData['email'];
                $tuple->update_time = date('Y-m-d H:i:s');
                $tuple->update();

                Resonse::set('success', true);
                Resonse::set('redirectUrl', beAdminUrl('System.Installer.complete'));
                Resonse::json();
            } catch (\Throwable $t) {
                Resonse::set('success', false);
                Resonse::set('message', $t->getMessage());
                Resonse::json();
            }

        } else {
            Resonse::set('steps', $this->steps);
            Resonse::set('step', 4);
            Resonse::set('title', $this->steps[3]);

            Resonse::display('App.System.Admin.Installer.setting', 'Installer');
        }
    }

    /**
     * 安装完成
     */
    public function complete()
    {
        
        

        Resonse::set('steps', $this->steps);
        Resonse::set('step', 5);
        Resonse::set('url', beAdminUrl());
        Resonse::display('App.System.Admin.Installer.complete', 'Installer');

        $config = Be::getConfig('App.System.System');
        $config->home = 'System.Home.index';
        $config->installable = 0;

        ConfigHelper::update('App.System.System', $config);

        if (Be::getRuntime()->isSwooleMode()) {
            Be::getRuntime()->reload();
        }
    }


}