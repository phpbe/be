<?php

namespace Be\App\System\Service\Admin;

use Be\AdminPlugin\AdminPluginException;
use Be\Be;
use Be\App\ServiceException;
use Be\Config\Annotation\BeConfig;
use Be\Config\Annotation\BeConfigItem;
use Be\Config\ConfigHelper;
use Be\Runtime\RuntimeException;

class Theme
{

    private $themes = null;

    public function getThemes()
    {
        if ($this->themes === null) {
            $this->themes = Be::getDb()->getKeyObjects('SELECT * FROM system_theme', null, 'name');
        }

        return $this->themes;
    }

    public function getThemeKeyValues()
    {
        return array_column($this->getThemes(), 'label', 'name');
    }

    public function getThemeCount()
    {
        return count($this->getThemes());
    }

    /**
     * @param string $themeName 主题用名
     * @return bool
     * @throws ServiceException
     */
    public function install($themeName)
    {
        try {
            $exist = Be::getTuple('system_theme')->loadBy('name', $themeName);
            throw new ServiceException('主题已于' . $exist->install_time . '安装过！');
        } catch (\Throwable $t) {

        }

        $property = Be::getProperty('Theme.' . $themeName);
        Be::getDb()->insert('system_theme', [
            'name' => $property->getName(),
            'label' => $property->getLabel(),
            'install_time' => date('Y-m-d H:i:s')
        ]);

        return true;
    }

    /**
     * 卸载应用
     *
     * @param string $themeName 应应用名
     * @return bool
     * @throws ServiceException
     */
    public function uninstall($themeName)
    {
        $exist = null;
        try {
            $exist = Be::getTuple('system_theme')->loadBy('name', $themeName);
        } catch (\Throwable $t) {
            throw new ServiceException('该主题尚未安装！');
        }

        $exist->delete();
        return true;
    }


    public function getTheme($themeName)
    {
        $theme = [];
        $theme['name'] = $themeName;
        $theme['url'] = beAdminUrl('System.Theme.editSectionItem', ['themeName' => $themeName]);

        $property = Be::getProperty('Theme.' . $themeName);
        $theme['property'] = $property;

        $pages = [];
        foreach ($property->pages as $pageName => $pageData) {
            $className = '\\Be\\Theme\\' . $themeName . '\\Config\\Page\\' . $pageName;
            $page = $this->getConfigAnnotation($className, false);
            $page['url'] = beAdminUrl('System.Theme.setting', ['themeName' => $themeName, 'pageName' => $pageName]);
            $pages[] = $page;
        }
        $theme['pages'] = $pages;

        return $theme;
    }

    public function getThemePage($themeName, $pageName)
    {
        $themeProperty = Be::getProperty('Theme.' . $themeName);
        if (!isset($themeProperty->pages[$pageName])) {
            throw new ServiceException('主题 ' . $themeName . ' 属性 pages 配置项中' . $pageName . ' 缺失！');
        }
        $themePageProperty = $themeProperty->pages[$pageName];

        $themePage = [];

        $configPageInstance = Be::getConfig('Theme.' . $themeName . '.Page.' . $pageName);

        $params = [];
        if (isset($themePageProperty['url'][1]) && is_array($themePageProperty['url'][1]) && count($themePageProperty['url'][1]) > 0) {
            $params = $themePageProperty['url'][1];
        }
        $desktopPreviewUrl = beUrl($themePageProperty['url'][0], array_merge($params, ['_theme' => $themeName]));
        $mobilePreviewUrl = beUrl($themePageProperty['url'][0], array_merge($params, ['_theme' => $themeName, '_isMobile' => 1]));
        $themePage['desktopPreviewUrl'] = $desktopPreviewUrl;
        $themePage['mobilePreviewUrl'] = $mobilePreviewUrl;

        $className = '\\Be\\Theme\\' . $themeName . '\\Config\\Page\\' . $pageName;
        $themePage['page'] = $this->getConfigAnnotation($className, false);
        $originalConfigPageInstance = new $className();

        foreach (array_keys($themePageProperty['sections']) as $sectionType) {
            if (isset($themePageProperty['sections'][$sectionType]) && $themePageProperty['sections'][$sectionType]) {
                $sections = [];
                foreach ($themePageProperty['sections'][$sectionType] as $sectionName) {
                    $className = '\\Be\\Theme\\' . $themeName . '\\Config\\Section\\' . $sectionName;
                    $sectionInstance = new $className();

                    $section = $this->getConfigAnnotation($className, false);

                    $icon = null;
                    if (isset($section['icon'])) {
                        $icon = $section['icon'];
                    } else if (is_callable([$sectionInstance, '__icon'])) {
                        $icon = $sectionInstance->__icon();
                    } else {
                        $icon = 'el-icon-menu';
                    }
                    if (strpos($icon, '<') === false) {
                        $icon = '<i class="' . $icon . '"></i>';
                    }
                    $section['icon'] = $icon;

                    $sections[] = $section;
                }

                $themePage[$sectionType . 'SectionsAvailable'] = $sections;
            }

            $sectionsEnabled = [];
            $key = $sectionType . 'Sections';
            if (isset($configPageInstance->$key) && $configPageInstance->$key) {
                foreach ($configPageInstance->$key as $sectionKey => $sectionName) {
                    $sectionsEnabled[] = $this->getThemeSection($themeName, $pageName, $sectionType, $sectionKey, $sectionName);
                }
                $themePage[$key] = $sectionsEnabled;
            }

            $themePage[$sectionType . 'Extended'] = isset($originalConfigPageInstance->$key) ? false : true;
        }

        return $themePage;
    }

    private function getThemeSection($themeName, $pageName, $sectionType, $sectionKey, $sectionName)
    {
        $configPageInstance = Be::getConfig('Theme.' . $themeName . '.Page.' . $pageName);

        $className = '\\Be\\Theme\\' . $themeName . '\\Config\\Section\\' . $sectionName;
        $sectionInstance = new $className();

        if (isset($sectionInstance->items)) {
            $section = $this->getConfigAnnotation($className, true);

            $configItems = $section['configItems'];
            unset($section['configItems']);

            $items = [];
            $itemDriverClasses = [];
            foreach ($configItems as $configItem) {
                if ($configItem['name'] == 'items') {
                    $items = $configItem;

                    if (isset($configItem['items'])) {
                        foreach ($configItem['items'] as $driverClass) {
                            $name = substr($driverClass, strrpos($driverClass, '\\') + 1);
                            $itemDriverClasses[$name] = [
                                'class' => $driverClass,
                                'annotation' => $this->getConfigAnnotation($driverClass, false)
                            ];
                        }
                    }
                    break;
                }
            }

            $sectionData = $sectionType . 'SectionsData';
            if (!isset($configPageInstance->$sectionData)) {
                throw new ServiceException('配置项 ' . $sectionData . ' 缺失！');
            }

            $existItems = [];
            if (isset($configPageInstance->$sectionData[$sectionKey]['items'])) {
                foreach ($configPageInstance->$sectionData[$sectionKey]['items'] as $key => $item) {
                    if (isset($itemDriverClasses[$item['name']])) {
                        $itemDriver = $itemDriverClasses[$item['name']];
                        $itemDriverClass = $itemDriver['class'];
                        $itemInstance = new $itemDriverClass();
                        foreach ($item['data'] as $k => $v) {
                            $itemInstance->$k = $v;
                        }

                        $icon = null;
                        if (isset($itemDriver['annotation']['icon'])) {
                            $icon = $itemDriver['annotation']['icon'];
                        } else if (is_callable([$itemInstance, '__icon'])) {
                            $icon = $itemInstance->__icon();
                        } else {
                            $icon = 'el-icon-full-screen';
                        }

                        if (strpos($icon, '<') === false) {
                            $icon = '<i class="' . $icon . '"></i>';
                        }

                        $url = beAdminUrl('System.Theme.editSectionItem', ['themeName' => $themeName, 'pageName' => $pageName, 'sectionType' => $sectionType, 'sectionKey' => $sectionKey, 'sectionName' => $sectionName, 'itemKey' => $key, 'itemName' => $item['name']]);

                        $existItems[] = [
                            'icon' => $icon,
                            'name' => $item['name'],
                            'label' => $itemDriver['annotation']['label'],
                            'url' => $url,
                        ];
                    }
                }
            }
            $items['existItems'] = $existItems;

            if (!isset($items['resize']) || $items['resize']) {
                $newItems = [];
                foreach ($itemDriverClasses as $itemDriver) {
                    $icon = null;
                    if (isset($itemDriver['annotation']['icon'])) {
                        $icon = $itemDriver['annotation']['icon'];
                    } else {
                        $icon = 'el-icon-full-screen';
                    }

                    if (strpos($icon, '<') === false) {
                        $icon = '<i class="' . $icon . '"></i>';
                    }

                    $url = beAdminUrl('System.Theme.addSectionItem', ['themeName' => $themeName, 'pageName' => $pageName, 'sectionType' => $sectionType, 'sectionKey' => $sectionKey, 'sectionName' => $sectionName, 'itemName' => $itemDriver['annotation']['name']]);
                    $newItems[] = [
                        'icon' => $icon,
                        'name' => $itemDriver['annotation']['name'],
                        'label' => $itemDriver['annotation']['label'],
                        'url' => $url,
                    ];
                }
                $items['newItems'] = $newItems;
            }

            if (!isset($items['labelNewItem'])) {
                $items['labelNewItem'] = '新建子组件';
            }

            $section['items'] = $items;
        } else {
            $section = $this->getConfigAnnotation($className, false);
        }

        $section['url'] = beAdminUrl('System.Theme.editSectionItem', ['themeName' => $themeName, 'pageName' => $pageName, 'sectionType' => $sectionType, 'sectionKey' => $sectionKey, 'sectionName' => $sectionName]);

        $icon = null;
        if (isset($section['icon'])) {
            $icon = $section['icon'];
        } else if (is_callable([$sectionInstance, '__icon'])) {
            $icon = $sectionInstance->__icon();
        } else {
            $icon = 'el-icon-menu';
        }
        if (strpos($icon, '<') === false) {
            $icon = '<i class="' . $icon . '"></i>';
        }
        $section['icon'] = $icon;

        return $section;
    }


    public function getThemeDrivers($themeName)
    {
        $className = '\\Be\\Theme\\' . $themeName . '\\Config\\Theme';
        $configAnnotation = $this->getConfigAnnotation($className, true);
        if ($configAnnotation['configItems']) {
            $configItemDrivers = [];
            $configInstance = Be::getConfig('Theme.' . $themeName . '.Theme');
            foreach ($configAnnotation['configItems'] as $configItem) {

                $itemName = $configItem['name'];
                if (isset($configInstance->$itemName)) {
                    $configItem['value'] = $configInstance->$itemName;
                }

                $driverClass = null;
                if (isset($configItem['driver'])) {
                    if (substr($configItem['driver'], 0, 8) == 'FormItem') {
                        $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $configItem['driver'];
                    } else {
                        $driverClass = $configItem['driver'];
                    }
                } else {
                    $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
                }
                $driver = new $driverClass($configItem);

                $configItemDrivers[] = $driver;
            }

            return $configItemDrivers;
        }

        return [];
    }

    public function getThemeSectionDrivers($themeName, $pageName, $sectionType, $sectionKey)
    {
        $configInstance = Be::getConfig('Theme.' . $themeName . '.Page.' . $pageName);

        $propertyName = $sectionType . 'Sections';
        $sectionName = $configInstance->$propertyName[$sectionKey];

        $propertyName = $sectionType . 'SectionsData';
        $data = $configInstance->$propertyName;

        $sectionData = isset($data[$sectionKey]) ? $data[$sectionKey] : [];

        $className = '\\Be\\Theme\\' . $themeName . '\\Config\\Section\\' . $sectionName;
        $configAnnotation = $this->getConfigAnnotation($className, true);

        if ($configAnnotation['configItems']) {
            $configItemDrivers = [];
            foreach ($configAnnotation['configItems'] as $configItem) {

                if ($configItem['name'] == 'items') {
                    continue;
                }

                if (isset($sectionData[$configItem['name']])) {
                    $configItem['value'] = $sectionData[$configItem['name']];
                }

                $driverClass = null;
                if (isset($configItem['driver'])) {
                    if (substr($configItem['driver'], 0, 8) == 'FormItem') {
                        $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $configItem['driver'];
                    } else {
                        $driverClass = $configItem['driver'];
                    }
                } else {
                    $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
                }
                $driver = new $driverClass($configItem);

                $configItemDrivers[] = $driver;
            }

            return $configItemDrivers;
        }

        return [];
    }


    public function getThemeSectionItemDrivers($themeName, $pageName, $sectionType, $sectionKey, $itemKey)
    {
        $configInstance = Be::getConfig('Theme.' . $themeName . '.Page.' . $pageName);

        $propertyName = $sectionType . 'Sections';
        $sectionName = $configInstance->$propertyName[$sectionKey];

        $propertyName = $sectionType . 'SectionsData';
        $data = $configInstance->$propertyName;

        $itemData = isset($data[$sectionKey]['items'][$itemKey]) ? $data[$sectionKey]['items'][$itemKey] : [];

        $className = '\\Be\\Theme\\' . $themeName . '\\Config\\Section\\' . $sectionName;
        $configAnnotation = $this->getConfigAnnotation($className, true);

        if ($configAnnotation['configItems']) {
            foreach ($configAnnotation['configItems'] as $configItem) {
                if ($configItem['name'] == 'items') {

                    if (isset($configItem['items'])) {
                        foreach ($configItem['items'] as $driverClass) {
                            $name = substr($driverClass, strrpos($driverClass, '\\') + 1);
                            if ($name == $itemData['name']) {
                                $configItemAnnotation = $this->getConfigAnnotation($driverClass, true);

                                if ($configItemAnnotation['configItems']) {
                                    $configItemDrivers = [];
                                    foreach ($configItemAnnotation['configItems'] as $configItemItem) {

                                        if (isset($itemData['data'][$configItemItem['name']])) {
                                            $configItemItem['value'] = $itemData['data'][$configItemItem['name']];
                                        }

                                        $driverClass = null;
                                        if (isset($configItemItem['driver'])) {
                                            if (substr($configItemItem['driver'], 0, 8) == 'FormItem') {
                                                $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $configItemItem['driver'];
                                            } else {
                                                $driverClass = $configItemItem['driver'];
                                            }
                                        } else {
                                            $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
                                        }
                                        $driver = new $driverClass($configItemItem);

                                        $configItemDrivers[] = $driver;
                                    }

                                    return $configItemDrivers;
                                }
                                break;
                            }
                        }
                    }
                    break;
                }
            }
        }

        return [];
    }


    private static $cache = [];

    public static function getConfigAnnotation($className, $withItemAnnotation = true)
    {
        if (!class_exists($className)) {
            throw new ServiceException('配置文件（' . $className . '）不存在！');
        }

        $reflection = null;
        if (isset(self::$cache['configAnnotation'][$className])) {
            $configAnnotation = self::$cache['configAnnotation'][$className];
        } else {
            $reflection = new \ReflectionClass($className);
            $classComment = $reflection->getDocComment();
            $parseClassComments = \Be\Util\Annotation::parse($classComment);
            if (!isset($parseClassComments['BeConfig'][0])) {
                throw new ServiceException('配置文件（' . $className . '）中未检测到 BeConfig 注解！');
            }

            $annotation = new BeConfig($parseClassComments['BeConfig'][0]);
            $configAnnotation = $annotation->toArray();
            if (isset($configAnnotation['value'])) {
                $configAnnotation['label'] = $configAnnotation['value'];
                unset($configAnnotation['value']);
            }

            $configAnnotation['name'] = substr($className, strrpos($className, '\\') + 1);

            self::$cache['configAnnotation'][$className] = $configAnnotation;
        }

        if ($withItemAnnotation) {
            if (isset(self::$cache['configItemAnnotation'][$className])) {
                $configItemAnnotations = self::$cache['configItemAnnotation'][$className];
            } else {
                $configItemAnnotations = [];
                $originalConfigInstance = new $className();
                if ($reflection === null) {
                    $reflection = new \ReflectionClass($className);
                }
                $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
                foreach ($properties as $property) {
                    $itemName = $property->getName();
                    $itemComment = $property->getDocComment();
                    $parseItemComments = \Be\Util\Annotation::parse($itemComment);

                    $configItemAnnotation = null;
                    if (isset($parseItemComments['BeConfigItem'][0])) {
                        $annotation = new BeConfigItem($parseItemComments['BeConfigItem'][0]);
                        $configItemAnnotation = $annotation->toArray();
                        if (isset($configItemAnnotation['value'])) {
                            $configItemAnnotation['label'] = $configItemAnnotation['value'];
                            unset($configItemAnnotation['value']);
                        }
                    } else {
                        $fn = '_' . $itemName;
                        if (is_callable([$originalConfigInstance, $fn])) {
                            $configItemAnnotation = $originalConfigInstance->$fn($itemName);
                        }
                    }

                    if ($configItemAnnotation) {
                        $configItemAnnotation['name'] = $itemName;
                        $configItemAnnotations[] = $configItemAnnotation;
                    }
                }

                self::$cache['configItemAnnotation'][$className] = $configItemAnnotations;
            }

            $configAnnotation['configItems'] = $configItemAnnotations;
        }

        return $configAnnotation;
    }

    /**
     * 启用指定页面指定方位的的自定义样式，不再从首页继承
     *
     * @param $themeName
     * @param $pageName
     * @param $sectionType
     */
    public function enableSectionType($themeName, $pageName, $sectionType)
    {
        if ($pageName == 'Home') {
            throw new ServiceException('首页不支持此功能！');
        }

        $configKey = 'Theme.' . $themeName . '.Page.' . $pageName;
        $configPageInstance = Be::getConfig($configKey);

        $configPageHomeInstance = Be::getConfig('Theme.' . $themeName . '.Page.Home');

        $propertyName = $sectionType . 'Sections';
        $configPageInstance->$propertyName = $configPageHomeInstance->$propertyName;

        $propertyName = $sectionType . 'SectionsData';
        $configPageInstance->$propertyName = $configPageHomeInstance->$propertyName;

        ConfigHelper::update($configKey, $configPageInstance);
    }

    /**
     * 禁用指定页面指定方位的的自定义样式，将从首页继承
     *
     * @param $themeName
     * @param $pageName
     * @param $sectionType
     */
    public function disableSectionType($themeName, $pageName, $sectionType)
    {
        if ($pageName == 'Home') {
            throw new ServiceException('首页不支持此功能！');
        }

        $configKey = 'Theme.' . $themeName . '.Page.' . $pageName;
        $configPageInstance = Be::getConfig($configKey);

        $propertyName = $sectionType . 'Sections';
        unset($configPageInstance->$propertyName);

        $propertyName = $sectionType . 'SectionsData';
        unset($configPageInstance->$propertyName);

        ConfigHelper::update($configKey, $configPageInstance);
    }

    /**
     * 新增组件
     *
     * @param $themeName
     * @param $pageName
     * @param $sectionType
     * @param $sectionName
     */
    public function addSection($themeName, $pageName, $sectionType, $sectionName)
    {
        $configKey = 'Theme.' . $themeName . '.Page.' . $pageName;
        $configInstance = Be::getConfig($configKey);

        $configSectionKey = 'Theme.' . $themeName . '.Section.' . $sectionName;
        $configSectionInstance = Be::getConfig($configSectionKey);
        $sectionData = get_object_vars($configSectionInstance);

        $propertyName = $sectionType . 'Sections';
        $configInstance->$propertyName[] = $sectionName;

        $propertyName = $sectionType . 'SectionsData';
        $configInstance->$propertyName[] = $sectionData;

        ConfigHelper::update($configKey, $configInstance);
    }

    /**
     * 新增组件
     *
     * @param $themeName
     * @param $pageName
     * @param $sectionType
     * @param $sectionName
     */
    public function deleteSection($themeName, $pageName, $sectionType, $sectionKey)
    {
        $configKey = 'Theme.' . $themeName . '.Page.' . $pageName;
        $configInstance = Be::getConfig($configKey);

        foreach (['Sections', 'SectionsData'] as $key) {
            $propertyName = $sectionType . $key;
            if (isset($configInstance->$propertyName[$sectionKey])) {
                unset($configInstance->$propertyName[$sectionKey]);
                $configInstance->$propertyName = array_values($configInstance->$propertyName);
            }
        }

        ConfigHelper::update($configKey, $configInstance);
    }

    /**
     * 组件排序
     *
     * @param $themeName
     * @param $pageName
     * @param $sectionType
     * @param $oldIndex
     * @param $newIndex
     */
    public function sortSection($themeName, $pageName, $sectionType, $oldIndex, $newIndex)
    {
        $configKey = 'Theme.' . $themeName . '.Page.' . $pageName;
        $configInstance = Be::getConfig($configKey);

        foreach (['Sections', 'SectionsData'] as $key) {
            $propertyName = $sectionType . $key;
            if (!isset($configInstance->$propertyName[$oldIndex]) || !isset($configInstance->$propertyName[$newIndex])) {
                throw new ServiceException('组件排序出错：索引超出数据范围');
            }

            $tmpData = $configInstance->$propertyName[$oldIndex];
            unset($configInstance->$propertyName[$oldIndex]);
            $arr = array_slice($configInstance->$propertyName, 0, $newIndex);
            $arr[] = $tmpData;
            $arr = array_merge($arr, array_slice($configInstance->$propertyName, $newIndex));
            $configInstance->$propertyName = array_values($arr);
        }

        ConfigHelper::update($configKey, $configInstance);
    }

    /**
     * 新增子项目
     *
     * @param $themeName
     * @param $pageName
     * @param $sectionType
     * @param $sectionKey
     * @param $itemName
     */
    public function addSectionItem($themeName, $pageName, $sectionType, $sectionKey, $itemName)
    {
        $configKey = 'Theme.' . $themeName . '.Page.' . $pageName;
        $configInstance = Be::getConfig($configKey);

        $propertyName = $sectionType . 'Sections';
        $sectionName = $configInstance->$propertyName[$sectionKey];

        $propertyName = $sectionType . 'SectionsData';
        $sectionData = $configInstance->$propertyName[$sectionKey];

        $configItemInstance = Be::getConfig('Theme.' . $themeName . '.Section.' . $sectionName . '.' . $itemName);

        if (!isset($sectionData['items'])) {
            $sectionData['items'] = [];
        }

        $sectionData['items'][] = [
            'name' => $itemName,
            'data' => get_object_vars($configItemInstance),
        ];

        $propertyName = $sectionType . 'SectionsData';
        $configInstance->$propertyName[$sectionKey] = $sectionData;

        ConfigHelper::update($configKey, $configInstance);
    }

    /**
     * 新增子组件
     *
     * @param $themeName
     * @param $pageName
     * @param $sectionType
     * @param $sectionName
     */
    public function deleteSectionItem($themeName, $pageName, $sectionType, $sectionKey, $itemKey)
    {
        $configKey = 'Theme.' . $themeName . '.Page.' . $pageName;
        $configInstance = Be::getConfig($configKey);

        $propertyName = $sectionType . 'SectionsData';
        if (isset($configInstance->$propertyName[$sectionKey]['items'][$itemKey])) {
            unset($configInstance->$propertyName[$sectionKey]['items'][$itemKey]);
            $configInstance->$propertyName[$sectionKey]['items'] = array_values($configInstance->$propertyName[$sectionKey]['items']);
        }

        ConfigHelper::update($configKey, $configInstance);
    }

    /**
     * 保存配置信息
     *
     * @param $themeName
     * @param $pageName
     * @param $sectionType
     * @param $sectionKey
     * @param $itemKey
     * @param $formData
     */
    public function saveSectionItem($themeName, $pageName, $sectionType, $sectionKey, $itemKey, $formData)
    {

        $configKey = null;
        $configInstance = null;

        // 配置主题 Theme 信息
        if ($sectionType === '') {
            $configKey = 'Theme.' . $themeName . '.Theme';
            $configInstance = Be::getConfig($configKey);

            $className = '\\Be\\Theme\\' . $themeName . '\\Config\\Theme';
            $newValues = $this->submitFormData($className, $formData, get_object_vars($configInstance));

            foreach ($newValues as $key => $val) {
                $configInstance->$key = $val;
            }

        } else {
            $configKey = 'Theme.' . $themeName . '.Page.' . $pageName;
            $configInstance = Be::getConfig($configKey);

            $propertyName = $sectionType . 'Sections';
            $sectionName = $configInstance->$propertyName[$sectionKey];

            $propertyName = $sectionType . 'SectionsData';
            $sectionData = $configInstance->$propertyName[$sectionKey];

            // 配置组件信息
            if ($itemKey === '') {
                $className = '\\Be\\Theme\\' . $themeName . '\\Config\\Section\\' . $sectionName;
                $newValues = $this->submitFormData($className, $formData, $sectionData);

                foreach ($newValues as $key => $val) {
                    $sectionData[$key] = $val;
                }

                $propertyName = $sectionType . 'SectionsData';
                $configInstance->$propertyName[$sectionKey] = $sectionData;
            } else {
                // 配置子组件信息
                $itemData = $sectionData['items'][$itemKey];
                $className = '\\Be\\Theme\\' . $themeName . '\\Config\\Section\\' . $sectionName . '\\' . $itemData['name'];
                $newValues = $this->submitFormData($className, $formData, $itemData['data']);

                $sectionData['items'][$itemKey] = [
                    'name' => $itemData['name'],
                    'data' => $newValues
                ];

                $propertyName = $sectionType . 'SectionsData';
                $configInstance->$propertyName[$sectionKey] = $sectionData;
            }
        }

        ConfigHelper::update($configKey, $configInstance);
    }

    private function submitFormData($className, $formData, $oldValue)
    {
        $originalConfigInstance = new $className();

        $newValues = [];
        $reflection = new \ReflectionClass($className);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $itemName = $property->getName();

            if ($itemName == 'items') {
                continue;
            }

            if (!isset($formData[$itemName])) {
                throw new ServiceException('参数 (' . $itemName . ') 缺失！');
            }

            $itemComment = $property->getDocComment();
            $parseItemComments = \Be\Util\Annotation::parse($itemComment);

            $configItem = null;
            if (isset($parseItemComments['BeConfigItem'][0])) {
                $annotation = new BeConfigItem($parseItemComments['BeConfigItem'][0]);
                $configItem = $annotation->toArray();
                if (isset($configItem['value'])) {
                    $configItem['label'] = $configItem['value'];
                    unset($configItem['value']);
                }
            } else {
                $fn = '_' . $itemName;
                if (is_callable([$originalConfigInstance, $fn])) {
                    $configItem = $originalConfigInstance->$fn($itemName);
                }
            }

            if ($configItem) {
                $configItem['name'] = $itemName;

                $driverClass = null;
                if (isset($configItem['driver'])) {
                    if (substr($configItem['driver'], 0, 8) == 'FormItem') {
                        $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $configItem['driver'];
                    } else {
                        $driverClass = $configItem['driver'];
                    }
                } else {
                    $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
                }

                if (isset($oldValue[$itemName])) {
                    $configItem['value'] = $oldValue[$itemName];
                }

                $driver = new $driverClass($configItem);
                $driver->submit($formData);

                $newValues[$itemName] = $driver->newValue;
            }
        }

        return $newValues;
    }

    /**
     * 组件排序
     *
     * @param $themeName
     * @param $pageName
     * @param $sectionType
     * @param $sectionKey
     * @param $oldIndex
     * @param $newIndex
     */
    public function sortSectionItem($themeName, $pageName, $sectionType, $sectionKey, $oldIndex, $newIndex)
    {
        $configKey = 'Theme.' . $themeName . '.Page.' . $pageName;
        $configInstance = Be::getConfig($configKey);

        $propertyName = $sectionType . 'SectionsData';
        if (!isset($configInstance->$propertyName[$sectionKey]['items'][$oldIndex]) ||
            !isset($configInstance->$propertyName[$sectionKey]['items'][$newIndex])) {
            throw new ServiceException('子组件排序出错：索引超出数据范围' . $propertyName . '-' . $sectionKey . '-' . $oldIndex . '-' . $newIndex);
        }

        $tmpData = $configInstance->$propertyName[$sectionKey]['items'][$oldIndex];
        unset($configInstance->$propertyName[$sectionKey]['items'][$oldIndex]);
        $arr = array_slice($configInstance->$propertyName[$sectionKey]['items'], 0, $newIndex);
        $arr[] = $tmpData;
        $arr = array_merge($arr, array_slice($configInstance->$propertyName[$sectionKey]['items'], $newIndex));
        $configInstance->$propertyName[$sectionKey]['items'] = array_values($arr);

        ConfigHelper::update($configKey, $configInstance);
    }

}

