<?php

namespace Be\App\System\Service\Admin;

use Be\Config\ConfigHelper;
use Be\Be;
use Be\App\ServiceException;
use Be\Util\File\Dir;
use Be\Util\Str\CaseConverter;

class App
{

    private $apps = null;

    /**
     * @return array|null
     */
    public function getApps()
    {
        if ($this->apps === null) {
            $configApp = Be::getConfig('App.System.App');
            $apps = [];
            foreach ($configApp->names as $appName) {
                $appProperty = Be::getProperty('App.' . $appName);
                $apps[] = (object)[
                    'name' => $appName,
                    'label' => $appProperty->getLabel(),
                    'icon' => $appProperty->getIcon(),
                    'path' => $appProperty->getPath(),
                    'relativePath' => $appProperty->getRelativePath(),
                ];
            }

            $this->apps = $apps;
        }

        return $this->apps;
    }

    /**
     * @return array
     */
    public function getAppNames()
    {
        $configApp = Be::getConfig('App.System.App');
        return $configApp->names;
    }

    /**
     * @return int
     */
    public function getAppCount()
    {
        return count($this->getApps());
    }

    /**
     * @return array
     */
    public function getAppNameLabelKeyValues()
    {
        return array_column($this->getApps(), 'label', 'name');
    }

    /**
     * @param string $appName 应应用名
     * @return bool
     * @throws ServiceException
     */
    public function install($appName)
    {
        $class = '\\Be\\App\\' . $appName . '\\Installer';
        if (class_exists($class)) {
            /**
             * @var \Be\App\Installer $installer
             */
            $installer = new $class();
            $installer->install();
        }

        $this->updateWww($appName);

        $configApp = Be::getConfig('App.System.App');
        $names = $configApp->names;
        $names[] = $appName;
        $configApp->names = array_unique($names);
        ConfigHelper::update('App.System.App', $configApp);

        Be::getService('App.System.Admin.AdminMenu')->update();

        return true;
    }

    /**
     * 卸载应用
     *
     * @param string $appName 应应用名
     * @return bool
     * @throws ServiceException
     */
    public function uninstall($appName)
    {
        $class = '\\Be\\App\\' . $appName . '\\UnInstaller';
        if (class_exists($class)) {
            /**
             * @var \Be\App\UnInstaller $unInstaller
             */
            $unInstaller = new $class();
            $unInstaller->uninstall();
        }

        $this->deleteWww($appName);

        $configApp = Be::getConfig('App.System.App');
        $names = $configApp->names;
        $newNames = [];
        foreach ($names as $name) {
            if ($name === $appName) {
                continue;
            }
            $newNames[] = $name;
        }
        $configApp->names = array_unique($newNames);
        ConfigHelper::update('App.System.App', $configApp);

        Be::getService('App.System.Admin.AdminMenu')->update();

        return true;
    }

    /**
     * 更新 www 目录
     * @param $appName
     * @return void
     */
    public function updateWww($appName)
    {
        // 如果写入外部CDN，需要较多时间
        set_time_limit(600);

        $rootPath = Be::getRuntime()->getRootPath();
        $property = Be::getProperty('App.' . $appName);
        $src = $property->getPath() . '/www';
        if (is_dir($src)) {
            $dst = $rootPath . '/www/app/' . CaseConverter::camel2Hyphen($appName);
            Dir::copy($src, $dst, true);

            $configWww = Be::getConfig('App.System.Www');
            if ($configWww->cdnWrite === 1) {
                $configStorage = Be::getConfig('App.System.Storage');
                if ($configStorage->driver !== 'LocalDisk') {
                    $dst = '/app/' . CaseConverter::camel2Hyphen($appName);

                    Be::getStorage()->uploadDir($dst, $src, true);
                }
            }
        }
    }

    /**
     * 删除 www 目录
     * @param $appName
     * @return void
     */
    public function deleteWww($appName)
    {
        $dst = Be::getRuntime()->getRootPath() . '/www/app/' . CaseConverter::camel2Hyphen($appName);
        if (is_dir($dst)) {
            Dir::rm($dst);

            $configWww = Be::getConfig('App.System.Www');
            if ($configWww->cdnWrite === 1) {
                $configStorage = Be::getConfig('App.System.Storage');
                if ($configStorage->driver !== 'LocalDisk') {
                    $dst = '/app/' . CaseConverter::camel2Hyphen($appName);
                    Be::getStorage()->deleteDir($dst);
                }
            }
        }
    }

    /**
     * 某个APP是否已安装
     * @return bool
     */
    public function isInstalled($appName): bool
    {
        $configApp = Be::getConfig('App.System.App');
        return in_array($appName, $configApp->names);
    }

}
