<?php

namespace Be\App\System\Service\Admin;

use Be\Be;
use Be\App\ServiceException;
use Be\Config\ConfigHelper;

class AdminTheme
{

    private $availableThemes = null;

    public function getAvailableThemes()
    {
        if ($this->availableThemes === null) {
            $themes = [];
            $configAdmin = Be::getConfig('App.System.Admin');
            $configAdminTheme = Be::getConfig('App.System.AdminTheme');
            foreach ($configAdminTheme->available as $name) {
                $themProperty = Be::getProperty('AdminTheme.' . $name);
                $themes[] = [
                    'name' => $name,
                    'label' => $themProperty->getLabel(),
                    'path' => $themProperty->getPath(),
                    'previewImageUrl' => $themProperty->getPreviewImageUrl(),
                    'is_enable' => in_array($name, $configAdminTheme->enable) ? '1' : '0',
                    'is_default' => $name == $configAdmin->theme ? '1' : '0',
                ];
            }

            $this->availableThemes = $themes;
        }

        return $this->availableThemes;
    }

    public function getAvailableThemeKeyValues()
    {
        return array_column($this->getAvailableThemes(), 'label', 'name');
    }

    public function getAvailableThemeCount()
    {
        return count($this->getAvailableThemes());
    }


    private $enableThemes = null;

    public function getEnableThemes()
    {
        if ($this->enableThemes === null) {
            $themes = [];
            $configAdmin = Be::getConfig('App.System.Admin');
            $configAdminTheme = Be::getConfig('App.System.AdminTheme');
            foreach ($configAdminTheme->enable as $name) {
                $themProperty = Be::getProperty('AdminTheme.' . $name);
                $themes[] = [
                    'name' => $name,
                    'label' => $themProperty->getLabel(),
                    'path' => $themProperty->getPath(),
                    'previewImageUrl' => $themProperty->getPreviewImageUrl(),
                    'is_default' => $name == $configAdmin->theme ? '1' : '0',
                ];
            }

            $this->enableThemes = $themes;
        }

        return $this->enableThemes;
    }

    public function getEnableThemeKeyValues()
    {
        return array_column($this->getEnableThemes(), 'label', 'name');
    }

    public function getEnableThemeCount()
    {
        return count($this->getEnableThemes());
    }


    /**
     * 发现
     *
     * @return int
     * @throws ServiceException
     */
    public function discover()
    {
        $themes = [];
        $vendorThemePath = Be::getRuntime()->getRootPath() . '/vendor';
        $dirs = scandir($vendorThemePath);
        foreach ($dirs as $dir) {
            if ($dir != '..' && $dir != '.') {
                $subVendorPath = $vendorThemePath . '/' . $dir;
                if (is_dir($subVendorPath)) {
                    $subDirs = scandir($subVendorPath);
                    foreach ($subDirs as $subDir) {
                        if ($subDir != '..' && $subDir != '.') {
                            $propertyPath = $subVendorPath . '/' . $subDir . '/src/Property.php';
                            if (!file_exists($propertyPath)) {
                                $propertyPath = $subVendorPath . '/' . $subDir . '/Property.php';
                            }

                            if (file_exists($propertyPath)) {
                                $content = file_get_contents($propertyPath);
                                preg_match('/namespace\s+Be\\\\AdminTheme\\\\(\w+)/i', $content, $matches);
                                if (isset($matches[1])) {
                                    $themes[] = $matches[1];
                                }
                            }
                        }
                    }
                }
            }
        }

        $themePath = Be::getRuntime()->getRootPath() . '/AdminTheme';
        if (!file_exists($themePath)) {
            $themePath = Be::getRuntime()->getRootPath() . '/src/AdminTheme';
        }

        if (file_exists($themePath)) {
            $dirs = scandir($themePath);
            foreach ($dirs as $dir) {
                if ($dir != '..' && $dir != '.') {
                    $subVendorPath = $themePath . '/' . $dir;
                    if (is_dir($subVendorPath)) {
                        $propertyPath = $subVendorPath . '/Property.php';
                        if (file_exists($propertyPath)) {
                            $content = file_get_contents($propertyPath);
                            preg_match('/namespace\s+Be\\\\AdminTheme\\\\(\w+)/i', $content, $matches);
                            if (isset($matches[1])) {
                                $themes[] = $matches[1];
                            }
                        }
                    }
                }
            }
        }

        $configAdminTheme = Be::getConfig('App.System.AdminTheme');

        // 有效主题，先移除要排除的
        $availableThemes = [];
        foreach ($themes as $x) {
            if (!in_array($x, $configAdminTheme->exclude)) {
                $availableThemes[] = $x;
            }
        }

        // 移除已经不可用的主题
        $available = [];
        foreach ($configAdminTheme->available as $x) {
            if (in_array($x, $availableThemes)) {
                $available[] = $x;
            }
        }
        $configAdminTheme->available = $available;

        $enable = [];
        foreach ($configAdminTheme->enable as $x) {
            if (in_array($x, $availableThemes)) {
                $enable[] = $x;
            }
        }
        $configAdminTheme->enable = $enable;

        // 检测新增的主题
        $n = 0;
        foreach ($availableThemes as $x) {
            if (!in_array($x, $configAdminTheme->available)) {
                $configAdminTheme->available[] = $x;
                $n++;
            }
        }

        ConfigHelper::update('App.System.AdminTheme', $configAdminTheme);

        return $n;
    }


    /**
     * 禁用应用
     *
     * @param string $themeName 应应用名
     * @param bool $enable 禁用/应用
     * @return bool
     * @throws ServiceException
     */
    public function toggleEnable($themeName, $enable)
    {
        $configAdminTheme = Be::getConfig('App.System.AdminTheme');

        if ($enable) {
            if (!in_array($themeName, $configAdminTheme->enable)) {
                $configAdminTheme->enable[] = $themeName;
            }
        } else {
            $configAdmin = Be::getConfig('App.System.Admin');
            if ($configAdmin->theme == $themeName) {
                throw new ServiceException('正在使用中的主题不可禁用！');
            }

            if (in_array($themeName, $configAdminTheme->enable)) {
                $newEnable = [];
                foreach($configAdminTheme->enable as $x) {
                    if ($x == $themeName) {
                        continue;
                    }
                    $newEnable[] = $x;
                }
                $configAdminTheme->enable = $newEnable;
            }
        }

        $newEnable = [];
        foreach($configAdminTheme->available as $x) {
            if (in_array($x, $configAdminTheme->enable)) {
                $newEnable[] = $x;
            }
        }
        $configAdminTheme->enable = $newEnable;
        ConfigHelper::update('App.System.AdminTheme', $configAdminTheme);

        return true;
    }


    /**
     * 设为墨认主题
     *
     * @param string $themeName 应应用名
     * @return bool
     * @throws ServiceException
     */
    public function toggleDefault($themeName)
    {
        $configAdminTheme = Be::getConfig('App.System.AdminTheme');
        if (!in_array($themeName, $configAdminTheme->enable)) {
            throw new ServiceException('该主题当前已被禁用，不可设为墨认主题！');
        }

        $configAdmin = Be::getConfig('App.System.Admin');
        if ($configAdmin->theme != $themeName) {
            $configAdmin->theme = $themeName;
            ConfigHelper::update('App.System.Admin', $configAdmin);
        }
        return true;
    }


}
