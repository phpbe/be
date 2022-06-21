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
        $response = Be::getResponse();
        $response->redirect(beAdminUrl('System.Installer.detect'));
    }

    /**
     * 检测环境
     */
    public function detect()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $runtime = Be::getRuntime();

        if ($request->isPost()) {

            $dataPath = $runtime->getDataPath();
            $uploadPath = $runtime->getUploadPath();

            if (!is_dir($dataPath)) {
                mkdir($dataPath, 0777, true);
                chmod($dataPath, 0777);
            }

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
                chmod($uploadPath, 0777);
            }

            $response->redirect(beAdminUrl('System.Installer.configDb'));
        } else {
            $value = [];
            $value['isPhpVersionGtMatch'] = version_compare(PHP_VERSION, '7.4.0') >= 0 ? 1 : 0;
            $value['isPdoMysqlInstalled'] = extension_loaded('pdo_mysql') ? 1 : 0;
            $value['isRedisInstalled'] = extension_loaded('redis') ? 1 : 0;

            $rootPath = $runtime->getRootPath();
            $dataPath = $runtime->getDataPath();
            $uploadPath = $runtime->getUploadPath();

            if (is_dir($dataPath)) {
                $value['isDataDirWritable'] = is_writable($dataPath) ? 1 : 0;
            } else {
                $value['isDataDirWritable'] = is_writable($rootPath) ? 1 : 0;
            }

            if (is_dir($uploadPath)) {
                $value['isUploadDirWritable'] = is_writable($uploadPath) ? 1 : 0;
            } else {
                $value['isUploadDirWritable'] = is_writable($rootPath) ? 1 : 0;
            }

            $response->set('value', $value);

            $isAllPassed = array_sum($value) === count($value);
            $response->set('isAllPassed', $isAllPassed);

            $response->set('steps', $this->steps);
            $response->set('step', 1);
            $response->set('title', $this->steps[0]);

            $response->display('App.System.Admin.Installer.detect', 'Installer');
        }
    }

    /**
     * 数据库配置
     */
    public function configDb()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if ($request->isPost()) {
            try {
                $postData = $request->post();
                Be::getService('App.System.Admin.Installer')->testDb($postData);

                $configDb = Be::getConfig('App.System.Db');

                $configDbDefault = new \Be\App\System\Config\Db();
                foreach ($configDbDefault->master as $k => $v) {
                    if (isset($postData[$k])) {
                        $configDb->master[$k] = $postData[$k];
                    }
                }

                ConfigHelper::update('App.System.Db', $configDb);

                $response->set('success', true);
                $response->set('redirectUrl', beAdminUrl('System.Installer.installApp'));
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }
        } else {
            $response->set('steps', $this->steps);
            $response->set('step', 2);

            $response->set('title', $this->steps[1]);

            $configDb = Be::getConfig('App.System.Db');
            $response->set('configDb', $configDb);

            $response->display('App.System.Admin.Installer.configDb', 'Installer');
        }
    }

    /**
     * 测试数据库连接
     */
    public function testDb()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        try {
            $postData = $request->post();
            $databases = Be::getService('App.System.Admin.Installer')->testDb($postData);
            $response->set('success', true);
            $response->set('message', '数据库连接成功！');
            $response->set('data', [
                'databases' => $databases,
            ]);
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * 安装应用
     */
    public function installApp()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        if ($request->isPost()) {
            try {
                $postData = $request->post();
                $service = Be::getService('App.System.Admin.App');
                if (isset($postData['names']) && is_array($postData['names']) && count($postData['names'])) {
                    foreach ($postData['names'] as $appName) {
                        $service->install($appName);
                    }
                }
                $response->set('success', true);
                $response->set('redirectUrl', beAdminUrl('System.Installer.setting'));
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }
        } else {
            $response->set('steps', $this->steps);
            $response->set('step', 3);
            $response->set('title', $this->steps[2]);

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

            $response->set('appProperties', $appProperties);
            $response->display('App.System.Admin.Installer.installApp', 'Installer');
        }
    }

    /**
     * 配置系统
     */
    public function setting()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $tuple = Be::getTuple('system_admin_user');
        $tuple->loadBy('username', 'admin');

        if ($request->isPost()) {
            try {
                $postData = $request->post();

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

                $response->set('success', true);
                $response->set('redirectUrl', beAdminUrl('System.Installer.complete'));
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }

        } else {
            $response->set('steps', $this->steps);
            $response->set('step', 4);
            $response->set('title', $this->steps[3]);

            $response->display('App.System.Admin.Installer.setting', 'Installer');
        }
    }

    /**
     * 安装完成
     */
    public function complete()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $response->set('steps', $this->steps);
        $response->set('step', 5);
        $response->set('url', beAdminUrl());
        $response->display('App.System.Admin.Installer.complete', 'Installer');

        $config = Be::getConfig('App.System.System');
        $config->home = 'System.Home.index';
        $config->installable = 0;

        ConfigHelper::update('App.System.System', $config);

        if (Be::getRuntime()->isSwooleMode()) {
            Be::getRuntime()->reload();
        }
    }


}