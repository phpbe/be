<?php

namespace Be\App\System\AdminService;

use Be\Config\ConfigHelper;
use Be\Be;
use Be\App\AdminServiceException;

class App
{

    private $apps = null;

    /**
     * @return array|null
     */
    public function getApps()
    {
        if ($this->apps == null) {
            $configApp = Be::getConfig('App.System.App');
            $apps = [];
            foreach( $configApp->names as $appName) {
                $appProperty = Be::getProperty('App.' . $appName);
                $apps[] = (object)[
                    'name' => $appName,
                    'label' => $appProperty->getLabel(),
                    'icon' => $appProperty->getIcon(),
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
     * @throws AdminServiceException
     */
    public function install($appName)
    {
        try {
            $exist = Be::getTuple('system_app')->loadBy('name', $appName);
            throw new AdminServiceException('应用已于' . $exist->install_time . '安装过！');
        } catch (\Throwable $t) {

        }

        $class = '\\Be\\App\\' . $appName . '\\Installer';
        if (class_exists($class)) {
            /**
             * @var \Be\App\Installer $installer
             */
            $installer = new $class();
            $installer->install();
        }

        $configApp = Be::getConfig('App.System.App');
        $names = $configApp->names;
        $names[] = $appName;
        $configApp->names = array_unique($names);
        ConfigHelper::update('System.App', $configApp);

        return true;
    }

    /**
     * 卸载应用
     *
     * @param string $appName 应应用名
     * @return bool
     * @throws AdminServiceException
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

        $configApp = Be::getConfig('App.System.App');
        $names = $configApp->names;
        $newNames = [];
        foreach ($names as $name) {
            if ($name == $appName) {
                continue;
            }
            $newNames[] = $name;
        }
        $configApp->names = array_unique($newNames);
        ConfigHelper::update('System.App', $configApp);

        return true;
    }


}
