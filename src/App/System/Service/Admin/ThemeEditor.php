<?php

namespace Be\App\System\Service\Admin;

use Be\Be;
use Be\App\ServiceException;
use Be\Config\Annotation\BeConfig;
use Be\Config\Annotation\BeConfigItem;
use Be\Config\ConfigHelper;
use Be\Runtime\RuntimeException;
use Be\Util\Annotation;
use Be\Util\File\Dir;
use Be\Util\Str\CaseConverter;

abstract class ThemeEditor
{

    protected $themeType = 'Theme';

    private $themes = null;

    public function getThemes()
    {
        if ($this->themes === null) {
            $themes = [];
            $configTheme = Be::getConfig('App.System.' . $this->themeType);
            foreach ($configTheme->available as $name) {
                $propertyClass = '\\Be\\' . $this->themeType . '\\' . $name . '\\Property';
                if (!class_exists($propertyClass)) continue;
                $themProperty = Be::getProperty($this->themeType . '.' . $name);
                $themes[] = (object)[
                    'name' => $name,
                    'label' => $themProperty->getLabel(),
                    'path' => $themProperty->getPath(),
                    'relativePath' => $themProperty->getRelativePath(),
                    'previewImageUrl' => $themProperty->getPreviewImageUrl(),
                    'is_default' => $name === $configTheme->default ? '1' : '0',
                ];
            }

            $this->themes = $themes;
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
     * 发现
     *
     * @return int
     * @throws ServiceException
     */
    public function discover()
    {
        $themes = [];
        $rootPath = Be::getRuntime()->getRootPath();

        $vendorDirs = scandir($rootPath . '/vendor');
        foreach ($vendorDirs as $vendorDir) {
            if ($vendorDir === '.' || $vendorDir === '..') {
                continue;
            }

            $vendorPath = $rootPath . '/vendor/' . $vendorDir;
            if (!is_dir($vendorPath)) {
                continue;
            }

            $subVendorDirs = scandir($vendorPath);
            foreach ($subVendorDirs as $subVendorDir) {

                if ($subVendorDir === '.' || $subVendorDir === '..') {
                    continue;
                }

                if (!is_dir($vendorPath . '/' . $subVendorDir)) {
                    continue;
                }

                // 应用中包含的 AdminTheme 或 Theme
                $themePath = $vendorPath . '/' . $subVendorDir . '/' . $this->themeType;
                if (!is_dir($themePath)) {
                    $themePath = $vendorPath . '/' . $subVendorDir . '/' . strtolower($this->themeType);
                }

                if (!is_dir($themePath)) {
                    // 有 src 目录的情况
                    if (is_dir($vendorPath . '/' . $subVendorDir . '/src')) {
                        $themePath = $vendorPath . '/' . $subVendorDir . '/src/' . $this->themeType;
                        if (!is_dir($themePath)) {
                            $themePath = $vendorPath . '/' . $subVendorDir . '/src/' . strtolower($this->themeType);
                        }
                    }
                }

                if (is_dir($themePath)) {
                    $dirs = scandir($themePath);
                    foreach ($dirs as $dir) {

                        if ($dir === '.' || $dir === '..') {
                            continue;
                        }

                        if (!is_dir($themePath . '/' . $dir)) {
                            continue;
                        }

                        $propertyPath = $themePath . '/' . $dir . '/Property.php';
                        if (file_exists($propertyPath)) {
                            $content = file_get_contents($propertyPath);
                            preg_match('/namespace\s+Be\\\\' . $this->themeType . '\\\\(\w+)/i', $content, $matches);
                            if (isset($matches[1])) {
                                $themes[] = $matches[1];
                            }
                        }
                    }
                    continue;
                }


                // vendor 子目录直接是主题的情况
                $propertyPath = $vendorPath . '/' . $subVendorDir . '/Property.php';
                if (!file_exists($propertyPath)) {
                    if (is_dir($vendorPath . '/' . $subVendorDir . '/src')) {
                        $propertyPath = $vendorPath . '/' . $subVendorDir . '/src/Property.php';
                    }
                }

                if (file_exists($propertyPath)) {
                    $content = file_get_contents($propertyPath);
                    preg_match('/namespace\s+Be\\\\' . $this->themeType . '\\\\(\w+)/i', $content, $matches);
                    if (isset($matches[1])) {
                        $themes[] = $matches[1];
                    }
                }
            }
        }


        $themePath = $rootPath . '/' . $this->themeType;
        if (!is_dir($themePath)) {
            $themePath = $rootPath . '/' . strtolower($this->themeType);
        }

        if (!is_dir($themePath)) {
            if (is_dir($rootPath . '/src')) {
                $themePath = $rootPath . '/src/' . $this->themeType;
                if (!is_dir($themePath)) {
                    $themePath = $rootPath . '/src/' . strtolower($this->themeType);
                }
            }
        }

        if (is_dir($themePath)) {
            $dirs = scandir($themePath);
            foreach ($dirs as $dir) {
                if ($dir === '.' || $dir === '..') {
                    continue;
                }

                if (!is_dir($themePath . '/' . $dir)) {
                    continue;
                }

                $propertyPath = $themePath . '/' . $dir . '/Property.php';
                if (file_exists($propertyPath)) {
                    $content = file_get_contents($propertyPath);
                    preg_match('/namespace\s+Be\\\\' . $this->themeType . '\\\\(\w+)/i', $content, $matches);
                    if (isset($matches[1])) {
                        $themes[] = $matches[1];
                    }
                }
            }
        }

        $class = '\\Be\\App\\System\\Config\\' . $this->themeType;
        $originalConfigTheme = new $class();

        $configATheme = Be::getConfig('App.System.' . $this->themeType);
        $configATheme->exclude = $originalConfigTheme->exclude;

        // 有效主题，先移除要排除的
        $availableThemes = [];
        foreach ($themes as $x) {
            if (!in_array($x, $configATheme->exclude)) {
                $availableThemes[] = $x;
            }
        }

        // 移除已经不可用的主题
        $available = [];
        foreach ($configATheme->available as $x) {
            if (in_array($x, $availableThemes)) {
                $available[] = $x;
            }
        }
        $configATheme->available = $available;

        // 检测新增的主题
        $n = 0;
        foreach ($availableThemes as $x) {
            if (!in_array($x, $configATheme->available)) {
                $configATheme->available[] = $x;
                $n++;
            }

            $this->updateWww($this->themeType, $x);
        }

        if (!in_array($configATheme->default, $configATheme->available)) {
            $configATheme->default = reset($configATheme->available);
        }

        ConfigHelper::update('App.System.' . $this->themeType, $configATheme);
        return $n;
    }

    /**
     * 搞贝 www 目录
     * @param $appName
     * @return void
     */
    public function updateWww($themeType, $themeName)
    {
        // 如果写入外部CDN，需要较多时间
        set_time_limit(600);

        $property = Be::getProperty($themeType . '.' . $themeName);
        $src = $property->getPath() . '/www';
        if (is_dir($src)) {
            $dst = Be::getRuntime()->getRootPath() . '/www/' . CaseConverter::camel2Hyphen($themeType) . '/' . CaseConverter::camel2Hyphen($themeName);
            Dir::copy($src, $dst, true);

            $configWww = Be::getConfig('App.System.Www');
            if ($configWww->cdnWrite === 1) {
                $configStorage = Be::getConfig('App.System.Storage');
                if ($configStorage->driver !== 'LocalDisk') {
                    $dst = '/' . CaseConverter::camel2Hyphen($themeType) . '/' . CaseConverter::camel2Hyphen($themeName);
                    Be::getStorage()->uploadDir($dst, $src, true);
                }
            }
        }
    }

    /**
     * 设为墨认主题
     *
     * @param string $themeName 主题名 应应用名
     * @return bool
     * @throws ServiceException
     */
    public function toggleDefault($themeName)
    {
        $configTheme = Be::getConfig('App.System.' . $this->themeType);

        if ($configTheme->default !== $themeName) {
            $configTheme->default = $themeName;
            ConfigHelper::update('App.System.' . $this->themeType, $configTheme);
        }
        return true;
    }

    /**
     * 获取主题
     *
     * @param string $themeName 主题名
     * @return object
     * @throws \Be\Runtime\RuntimeException
     */
    public function getTheme(string $themeName): object
    {
        $property = Be::getProperty($this->themeType . '.' . $themeName);

        $theme = new \stdClass();
        $theme->name = $themeName;
        $theme->label = $property->getLabel();
        $theme->url = beAdminUrl('System.' . $this->themeType . '.editTheme', ['themeName' => $themeName]);
        return $theme;
    }

    /**
     * 获取页面列表
     *
     * @return array
     */
    public function getPageTree($themeName): array
    {
        $pageTree = [];

        $pageTree[] = [
            'value' => 'default',
            'label' => '公共页面',
            'url' => beAdminUrl('System.' . $this->themeType . '.setting', ['themeName' => $themeName, 'pageName' => 'default']),
        ];

        // 后台功能不南要针对项磁的配置
        if ($this->themeType === 'Theme') {
            $menuPickers = Be::getService('App.System.Admin.Menu')->getMenuPickers();
            foreach ($menuPickers as $appMenuPicker) {
                $children = [];
                foreach ($appMenuPicker['menuPickers'] as $menuPicker) {
                    $children[] = [
                        'value' => $menuPicker['route'],
                        'label' => $menuPicker['label'],
                        'url' => beAdminUrl('System.' . $this->themeType . '.setting', ['themeName' => $themeName, 'pageName' => $menuPicker['route']]),
                    ];
                }

                $pageTree[] = [
                    'value' => $appMenuPicker['app']->name,
                    'label' => $appMenuPicker['app']->label,
                    'icon' => $appMenuPicker['app']->icon,
                    'children' => $children,
                ];
            }
        }

        return $pageTree;
    }

    /**
     * 获取指定的页面
     *
     * @param $themeName
     * @param $pageName
     * @return object
     * @throws ServiceException
     */
    public function getPage($themeName, $pageName): object
    {
        $page = new \stdClass();
        $page->name = $pageName;
        //$page->label = '';

        // ------------------------------------------------------------------------------------------------------------- 生成页面预览网址
        if ($this->themeType === 'Theme') {
            $route = $pageName === 'default' ? 'System.Preview.page' : $pageName;

            $params = [];
            $menuPicker = Be::getService('App.System.Admin.Menu')->getMenuPicker($route);
            if (isset($menuPicker['annotation'])) {
                $picker = $menuPicker['annotation']->picker;
                if ($picker) {
                    $table = Be::getTable($picker['table']);
                    if (isset($picker['grid']['filter'])) {
                        foreach ($picker['grid']['filter'] as $filter) {
                            $table->where($filter);
                        }
                    }

                    if ($table->count() === 0) {
                        throw new ServiceException('暂时无法配置此页面，请先添加内容！');
                    }

                    $name = $picker['name'];
                    $field = $picker['field'] ?? $name;
                    $value = $table->getValue($field);
                    $params[$name] = $value;
                }
            }

            $desktopPreviewUrl = beUrl($route, array_merge($params, ['be-theme' => $themeName]));
            $mobilePreviewUrl = beUrl($route, array_merge($params, ['be-theme' => $themeName, 'be-is-mobile' => 1]));
        } else {
            $route = $pageName === 'default' ? 'System.Preview.page' : $pageName;

            $desktopPreviewUrl = beAdminUrl($route, ['be-theme' => $themeName]);
            $mobilePreviewUrl = beAdminUrl($route, ['be-theme' => $themeName, 'be-is-mobile' => 1]);
        }
        $page->desktopPreviewUrl = $desktopPreviewUrl;
        $page->mobilePreviewUrl = $mobilePreviewUrl;
        // ------------------------------------------------------------------------------------------------------------- 生成页面预览网址


        // ------------------------------------------------------------------------------------------------------------- 加载部件（sections）数据
        // 获取当前页数的配置信息
        if ($pageName === 'default') {
            $configPage = Be::getConfig($this->themeType . '.' . $themeName . '.Page');
        } else {
            $configPage = Be::getConfig($this->themeType . '.' . $themeName . '.Page.' . $route);
        }

        foreach (['north', 'middle', 'west', 'center', 'east', 'south'] as $position) {
            $page->$position = $configPage->$position;
            $property = $position . 'Sections';
            if ($configPage->$position !== 0) {
                $sections = [];
                if (isset($configPage->$property) && count($configPage->$property)) {
                    foreach ($configPage->$property as $sectionIndex => $sectionData) {

                        try {
                            if (!isset($sectionData['config'])) {
                                $sectionConfig = $this->getSectionConfig($sectionData['name']);
                            } else {
                                $sectionConfig = $sectionData['config'];
                            }

                            $section = $this->getSection($themeName, $pageName, $position, $sectionIndex, $sectionData['name'], $sectionConfig);
                        } catch (\Throwable $t) {
                            Be::getLog()->error($t);
                            continue;
                        }

                        $sections[] = $section;
                    }
                }
                $page->$property = $sections;

                $availableSections = $this->getAvailableSections($route, $position);
                $property2 = $position . 'AvailableSections';
                $page->$property2 = $availableSections;
            }
        }
        // ------------------------------------------------------------------------------------------------------------- 加载部件（sections）数据

        return $page;
    }

    /**
     * 获取方位中文描述
     *
     * @param string $position 位置
     * @return string
     */
    public function getPositionDescription(string $position): string
    {
        switch ($position) {
            case 'north':
                return '页面项部（North）';
            case 'middle':
                return '页面中部（Middle）';
            case 'south':
                return '页面底部（South）';
            case 'west':
                return '页面中部 - 左（North）';
            case 'center':
                return '页面中部 - 中（Center）';
            case 'east':
                return '页面中部 - 右（East）';
        }
        return '';
    }

    /**
     * 获取指定方位可用的部件列表
     *
     * @param string $position 位置
     * @return array[]
     */
    public function getAvailableSections(string $route, string $position): array
    {
        $appSections = [];

        $apps = Be::getService('App.System.Admin.App')->getApps();
        foreach ($apps as $app) {

            $sections = [];
            $path = $app->path . '/Section';
            if (file_exists($path) && is_dir($path)) {
                $sections = $this->makeAvailableSections($route, $position, 'App.' . $app->name, $path);
            }

            if (count($sections) > 0) {

                usort($sections, function ($a, $b) {
                    if ($a->ordering > $b->ordering) {
                        return 1;
                    } elseif ($a->ordering === $b->ordering) {
                        return 0;
                    } else {
                        return -1;
                    }
                });

                $appSections[] = [
                    'app' => $app,
                    'sections' => $sections,
                ];
            }
        }

        $themeSections = [];
        $themes = $this->getThemes();
        foreach ($themes as $theme) {

            $sections = [];
            $path = $theme->path . '/Section';
            if (file_exists($path) && is_dir($path)) {
                $sections = $this->makeAvailableSections($route, $position, $this->themeType . '.' . $theme->name, $path);
            }

            if (count($sections) > 0) {

                usort($sections, function ($a, $b) {
                    if ($a->ordering > $b->ordering) {
                        return 1;
                    } elseif ($a->ordering === $b->ordering) {
                        return 0;
                    } else {
                        return -1;
                    }
                });

                $themeSections[] = [
                    'theme' => $theme,
                    'sections' => $sections,
                ];
            }
        }

        return [
            'appSections' => $appSections,
            'themeSections' => $themeSections,
        ];
    }


    private function makeAvailableSections(string $route, string $position, string $namespace, string $path): array
    {
        $sections = [];
        if (file_exists($path) && is_dir($path)) {
            // 分析目录
            $items = scandir($path);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;

                $itemNamespace = $namespace . '.' . $item;
                $itemPath = $path . '/' . $item;

                if (!is_dir($itemPath)) continue;

                $pathConfig = $itemPath . '/Config.php';
                $pathTemplate = $itemPath . '/Template.php';
                if (file_exists($pathConfig) && file_exists($pathTemplate)) {
                    try {
                        $section = $this->getSectionSummary($itemNamespace);
                    } catch (\Throwable $t) {
                        Be::getLog()->error($t);
                        continue;
                    }

                    if (count($section->positions) > 0 && (in_array($position, $section->positions) || in_array('*', $section->positions))) {
                        if (count($section->routes) > 0 && (in_array($route, $section->routes) || in_array('*', $section->routes))) {
                            $sections[] = $section;
                        }
                    }
                } else {
                    $sections2 = $this->makeAvailableSections($route, $position, $itemNamespace, $itemPath);
                    if (count($sections2) > 0) {
                        $sections = array_merge($sections, $sections2);
                    }
                }
            }
        }

        return $sections;
    }


    /**
     * 获取指定的部件摘要
     *
     * @param string $sectionName 部件名称
     * @return object
     */
    public function getSectionSummary(string $sectionName): object
    {
        $parts = explode('.', $sectionName);
        $type = array_shift($parts);
        $name = array_shift($parts);
        $classPart = implode('\\', $parts);

        $configClass = '\\Be\\' . $type . '\\' . $name . '\\Section\\' . $classPart . '\\Config';
        if (!class_exists($configClass)) {
            throw new ServiceException('部件配置文件（' . $sectionName . '.Config）不存在!');
        }

        $templateClass = '\\Be\\' . $type . '\\' . $name . '\\Section\\' . $classPart . '\\Template';
        if (!class_exists($templateClass)) {
            throw new ServiceException('部件主题文件（' . $sectionName . '.Template）不存在!');
        }

        $sectionConfigAnnotation = $this->getConfigAnnotation($configClass, false);
        $sectionTemplateInstance = new $templateClass();

        $section = new \stdClass();
        $section->name = $sectionName;
        $section->label = $sectionConfigAnnotation['label'];
        $section->ordering = $sectionConfigAnnotation['ordering'] ?? 100;
        $section->positions = $sectionTemplateInstance->positions;
        $section->routes = $sectionTemplateInstance->routes;

        $icon = null;
        if (isset($sectionConfigAnnotation['icon'])) {
            $icon = $sectionConfigAnnotation['icon'];
        } else {
            $sectionConfigInstance = new $configClass();
            if (is_callable([$sectionConfigInstance, '__icon'])) {
                $icon = $sectionConfigInstance->__icon();
            }
        }
        if ($icon === null) {
            $icon = 'el-icon-menu';
        }
        if (strpos($icon, '<') === false) {
            $icon = '<i class="' . $icon . '"></i>';
        }
        $section->icon = $icon;

        return $section;
    }

    /**
     * 获取指定的线上的部件
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     * @param string $position 位置 方位
     * @param int $sectionIndex 部件索引 部件键名
     * @param string $sectionName 部件名称
     * @param object $sectionConfig 部件配置数据
     * @return object
     */
    public function getSection(string $themeName, string $pageName, string $position, int $sectionIndex, string $sectionName, object $sectionConfig): object
    {
        $parts = explode('.', $sectionName);
        $type = array_shift($parts);
        $name = array_shift($parts);
        $classPart = implode('\\', $parts);

        $configClass = '\\Be\\' . $type . '\\' . $name . '\\Section\\' . $classPart . '\\Config';
        if (!class_exists($configClass)) {
            throw new ServiceException('部件配置文件（' . $sectionName . '.Config）不存在!');
        }

        $templateClass = '\\Be\\' . $type . '\\' . $name . '\\Section\\' . $classPart . '\\Template';
        if (!class_exists($templateClass)) {
            throw new ServiceException('部件主题文件（' . $sectionName . '.Template）不存在!');
        }

        $sectionConfigInstance = new $configClass();
        $sectionTemplateInstance = new $templateClass();

        // 部件配置文件注解
        if (isset($sectionConfigInstance->items)) {
            // 包含子项的部件
            $sectionConfigAnnotation = $this->getConfigAnnotation($configClass, true);
        } else {
            $sectionConfigAnnotation = $this->getConfigAnnotation($configClass, false);
        }

        $section = new \stdClass();
        $section->name = $sectionName;
        $section->label = $sectionConfigAnnotation['label'];
        $section->ordering = $sectionConfigAnnotation['ordering'] ?? 100;
        $section->positions = $sectionTemplateInstance->positions;
        $section->routes = $sectionTemplateInstance->routes;

        $icon = null;
        if (isset($sectionConfigAnnotation['icon'])) {
            $icon = $sectionConfigAnnotation['icon'];
        } else {
            if (is_callable([$sectionConfigInstance, '__icon'])) {
                $icon = $sectionConfigInstance->__icon();
            }
        }
        if ($icon === null) {
            $icon = 'el-icon-menu';
        }
        if (strpos($icon, '<') === false) {
            $icon = '<i class="' . $icon . '"></i>';
        }
        $section->icon = $icon;

        $section->url = beAdminUrl('System.' . $this->themeType . '.editSection', ['themeName' => $themeName, 'pageName' => $pageName, 'position' => $position, 'sectionIndex' => $sectionIndex]);

        // 包含子项的部件
        if (isset($sectionConfigInstance->items)) {

            $items = [];

            $sectionConfigAnnotationItems = $sectionConfigAnnotation['configItems'];

            $annotationItems = [];
            foreach ($sectionConfigAnnotationItems as $sectionConfigAnnotationItem) {
                if ($sectionConfigAnnotationItem['name'] === 'items') {
                    $annotationItems = $sectionConfigAnnotationItem;
                    break;
                }
            }

            $itemDriverClasses = [];
            if (isset($annotationItems['items'])) {
                foreach ($annotationItems['items'] as $driverClass) {
                    $name = substr($driverClass, strrpos($driverClass, '\\') + 1);
                    $itemDriverClasses[$name] = [
                        'class' => $driverClass,
                        'annotation' => $this->getConfigAnnotation($driverClass, false)
                    ];
                }
            }

            $existItems = [];
            if (isset($sectionConfig->items)) {
                foreach ($sectionConfig->items as $itemIndex => $item) {
                    if (isset($itemDriverClasses[$item['name']])) {

                        $itemDriver = $itemDriverClasses[$item['name']];
                        $itemDriverClass = $itemDriver['class'];
                        $itemInstance = new $itemDriverClass();

                        $existItem = new \stdClass();
                        $existItem->name = $item['name'];
                        $existItem->label = $itemDriver['annotation']['label'];

                        /*
                        foreach ($item['config'] as $k => $v) {
                            $itemInstance->$k = $v;
                        }
                        */

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

                        $existItem->icon = $icon;

                        $existItem->url = beAdminUrl('System.' . $this->themeType . '.editSectionItem', ['themeName' => $themeName, 'pageName' => $pageName, 'position' => $position, 'sectionIndex' => $sectionIndex, 'itemIndex' => $itemIndex]);

                        $existItems[] = $existItem;
                    }
                }
            }
            $items['existItems'] = $existItems;

            if (!isset($annotationItems['lock']) || !$annotationItems['lock']) {
                $newItems = [];
                foreach ($itemDriverClasses as $itemDriver) {

                    $newItem = new \stdClass();
                    $newItem->name = $itemDriver['annotation']['name'];
                    $newItem->label = $itemDriver['annotation']['label'];

                    $icon = null;
                    if (isset($itemDriver['annotation']['icon'])) {
                        $icon = $itemDriver['annotation']['icon'];
                    } else {
                        $icon = 'el-icon-full-screen';
                    }

                    if (strpos($icon, '<') === false) {
                        $icon = '<i class="' . $icon . '"></i>';
                    }

                    $newItem->icon = $icon;

                    $newItem->url = beAdminUrl('System.' . $this->themeType . '.addSectionItem', ['themeName' => $themeName, 'pageName' => $pageName, 'position' => $position, 'sectionIndex' => $sectionIndex, 'itemName' => $itemDriver['annotation']['name']]);

                    $newItems[] = $newItem;
                }

                $items['newItems'] = $newItems;
            }

            $section->items = $items;
        }

        return $section;
    }

    /**
     * 获取部件默认配置
     *
     * @param string $sectionName 部件名
     * @return array | object
     * @throws ServiceException
     */
    public function getSectionConfig(string $sectionName)
    {
        $parts = explode('.', $sectionName);
        $type = array_shift($parts);
        $name = array_shift($parts);
        $classPart = implode('\\', $parts);

        $class = '\\Be\\' . $type . '\\' . $name . '\\Section\\' . $classPart . '\\Config';
        if (!class_exists($class)) {
            throw new ServiceException('部件配置文件（' . $sectionName . '.Config）不存在!');
        }

        $sectionConfig = new $class();
        if (isset($sectionConfig->items)) {
            if (count($sectionConfig->items) > 0) {
                $items = [];
                foreach ($sectionConfig->items as $item) {
                    if (!isset($item['config'])) {
                        $class = '\\Be\\' . $type . '\\' . $name . '\\Section\\' . $classPart . '\\Item\\' . $item['name'];
                        if (!class_exists($class)) {
                            throw new ServiceException('部件子项配置文件（' . $sectionName . '.Item.' . $item['name'] . '）不存在!');
                        }
                        $itemInstance = new $class();
                        $items[] = [
                            'name' => $item['name'],
                            'config' => $itemInstance,
                        ];
                    } else {
                        $name = $item['name'];
                        $config = $item['config'];
                        if (is_array($config)) {
                            $config = (object)$config;
                        }

                        $items[] = [
                            'name' => $name,
                            'config' => $config
                        ];
                    }
                }

                $sectionConfig->items = $items;
            }
        }

        return $sectionConfig;
    }

    /**
     * 获取部件子项默认配置
     *
     * @param string $sectionName 部件名
     * @param string $itemName 部件子项名称
     * @param string $format 格式 array | object
     * @return array | object
     * @throws ServiceException
     */
    public function getSectionItemConfig(string $sectionName, string $itemName)
    {
        $parts = explode('.', $sectionName);
        $type = array_shift($parts);
        $name = array_shift($parts);
        $classPart = implode('\\', $parts);

        $class = '\\Be\\' . $type . '\\' . $name . '\\Section\\' . $classPart . '\\Item\\' . $itemName;
        if (!class_exists($class)) {
            throw new ServiceException('部件子项配置文件（' . $sectionName . '.Item.' . $itemName . '）不存在!');
        }
        return new $class();
    }


    /**
     * 获取主题 编辑表单驱动
     *
     * @param string $themeName 主题名
     * @return array
     */
    public function getThemeDrivers(string $themeName): array
    {
        $className = '\\Be\\' . $this->themeType . '\\' . $themeName . '\\Config\\Theme';
        $configAnnotation = $this->getConfigAnnotation($className, true);
        if ($configAnnotation['configItems']) {
            $configItemDrivers = [];
            $configInstance = Be::getConfig($this->themeType . '.' . $themeName . '.Theme');
            foreach ($configAnnotation['configItems'] as $configItem) {

                $itemName = $configItem['name'];
                if (isset($configInstance->$itemName)) {
                    $configItem['value'] = $configInstance->$itemName;
                }

                $driverClass = null;
                if (isset($configItem['driver'])) {
                    if (substr($configItem['driver'], 0, 8) === 'FormItem') {
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

    /**
     * 获取页面 编辑表单驱动
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     * @return array
     */
    public function getPageDrivers(string $themeName, string $pageName): array
    {
        if ($pageName === 'default') {
            $configInstance = Be::getConfig($this->themeType . '.' . $themeName . '.Page');
            $class = '\\Be\\' . $this->themeType . '\\' . $themeName . '\\Config\\Page';
        } else {
            $configInstance = Be::getConfig($this->themeType . '.' . $themeName . '.Page.' . $pageName);
            $class = '\\Be\\' . $this->themeType . '\\' . $themeName . '\\Config\\Page\\' . str_replace('.', '\\', $pageName);
            if (!class_exists($class)) {
                $parts = explode('.', $pageName);
                $class = '\\Be\\App\\' . $parts[0] . '\\Config\\Page\\' . ($this->themeType === 'AdminTheme' ? 'Admin\\' : '') . implode('\\', array_slice($parts, 1));
            }
        }

        try {
            $configAnnotation = $this->getConfigAnnotation($class, true);
            if ($configAnnotation['configItems']) {
                $configItemDrivers = [];

                foreach ($configAnnotation['configItems'] as $configItem) {

                    $itemName = $configItem['name'];
                    if (isset($configInstance->$itemName)) {
                        $configItem['value'] = $configInstance->$itemName;
                    }

                    $driverClass = null;
                    if (isset($configItem['driver'])) {
                        if (substr($configItem['driver'], 0, 8) === 'FormItem') {
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
        } catch (\Throwable $t) {
            return [];
        }

        return [];
    }

    /**
     * 获取部件 编辑表单驱动
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     * @param string $position 位置
     * @param int $sectionIndex 部件索引
     * @return array
     * @throws ServiceException
     */
    public function getSectionDrivers(string $themeName, string $pageName, string $position, int $sectionIndex): array
    {
        if ($pageName === 'default') {
            $configPage = Be::getConfig($this->themeType . '.' . $themeName . '.Page');
        } else {
            $configPage = Be::getConfig($this->themeType . '.' . $themeName . '.Page.' . $pageName);
        }

        $property = $position . 'Sections';
        $sectionData = $configPage->$property[$sectionIndex];

        if (!isset($sectionData['config'])) {
            $sectionConfig = $this->getSectionConfig($sectionData['name']);
        } else {
            $sectionConfig = $sectionData['config'];
        }

        $parts = explode('.', $sectionData['name']);
        $type = array_shift($parts);
        $name = array_shift($parts);
        $classPart = implode('\\', $parts);

        $class = '\\Be\\' . $type . '\\' . $name . '\\Section\\' . $classPart . '\\Config';
        $configAnnotation = $this->getConfigAnnotation($class, true);

        if ($configAnnotation['configItems']) {
            $configItemDrivers = [];
            foreach ($configAnnotation['configItems'] as $configItem) {

                if ($configItem['name'] === 'items') {
                    continue;
                }

                $configItemName = $configItem['name'];
                if (isset($sectionConfig->$configItemName)) {
                    $configItem['value'] = $sectionConfig->$configItemName;
                }

                $driverClass = null;
                if (isset($configItem['driver'])) {
                    if (substr($configItem['driver'], 0, 8) === 'FormItem') {
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

    /**
     * 获取部件 编辑表单驱动
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     * @param string $position 位置
     * @param int $sectionIndex 部件索引
     * @param int $itemIndex 部件子项索引
     * @return array
     * @throws ServiceException
     */
    public function getSectionItemDrivers(string $themeName, string $pageName, string $position, int $sectionIndex, int $itemIndex): array
    {
        if ($pageName === 'default') {
            $configPage = Be::getConfig($this->themeType . '.' . $themeName . '.Page');
        } else {
            $configPage = Be::getConfig($this->themeType . '.' . $themeName . '.Page.' . $pageName);
        }

        $property = $position . 'Sections';
        $sectionData = $configPage->$property[$sectionIndex];

        if (!isset($sectionData['config'])) {
            $sectionConfig = $this->getSectionConfig($sectionData['name']);
        } else {
            $sectionConfig = $sectionData['config'];
        }

        $sectionItemData = $sectionConfig->items[$itemIndex];
        $sectionItemName = $sectionItemData['name'];
        if (!isset($sectionItemData['config'])) {
            $sectionItemConfig = $this->getSectionItemConfig($sectionData['name'], $sectionItemName);
        } else {
            $sectionItemConfig = $sectionItemData['config'];
        }

        $parts = explode('.', $sectionData['name']);
        $type = array_shift($parts);
        $name = array_shift($parts);
        $classPart = implode('\\', $parts);

        $class = '\\Be\\' . $type . '\\' . $name . '\\Section\\' . $classPart . '\\Item\\' . $sectionItemName;
        $configAnnotation = $this->getConfigAnnotation($class, true);

        if ($configAnnotation['configItems']) {
            $configItemDrivers = [];
            foreach ($configAnnotation['configItems'] as $configItem) {

                $configItemName = $configItem['name'];
                if (isset($sectionItemConfig->$configItemName)) {
                    $configItem['value'] = $sectionItemConfig->$configItemName;
                }

                $driverClass = null;
                if (isset($configItem['driver'])) {
                    if (substr($configItem['driver'], 0, 8) === 'FormItem') {
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
     * 保存配置信息
     *
     * @param string $themeName 主题名
     * @param array $formData 表单数据
     */
    public function editTheme(string $themeName, array $formData)
    {
        $configKey = $this->themeType . '.' . $themeName . '.Theme';
        $configInstance = Be::getConfig($configKey);

        $className = '\\Be\\' . $this->themeType . '\\' . $themeName . '\\Config\\Theme';
        $newValues = $this->submitFormData($className, $formData, $configInstance);

        foreach ($newValues as $key => $val) {
            $configInstance->$key = $val;
        }

        ConfigHelper::update($configKey, $configInstance);
    }

    /**
     * 重置配置信息
     *
     * @param string $themeName 主题名
     */
    public function resetTheme(string $themeName)
    {
        ConfigHelper::reset($this->themeType . '.' . $themeName . '.Theme');
    }

    /**
     * 保存页面配置信息
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     * @param array $formData 表单数据
     */
    public function editPage(string $themeName, string $pageName, array $formData)
    {
        if ($pageName === 'default') {
            $configKey = $this->themeType . '.' . $themeName . '.Page';
            $class = '\\Be\\' . $this->themeType . '\\' . $themeName . '\\Config\\Page';
        } else {
            $configKey = $this->themeType . '.' . $themeName . '.Page.' . $pageName;
            $class = '\\Be\\' . $this->themeType . '\\' . $themeName . '\\Config\\Page\\' . str_replace('.', '\\', $pageName);
            if (!class_exists($class)) {
                $parts = explode('.', $pageName);
                $class = '\\Be\\App\\' . $parts[0] . '\\Config\\Page\\' . ($this->themeType === 'AdminTheme' ? 'Admin\\' : '') . implode('\\', array_slice($parts, 1));
            }
        }

        $configPage = Be::getConfig($configKey);

        $newValues = $this->submitFormData($class, $formData, $configPage);

        foreach ($newValues as $key => $val) {
            $configPage->$key = $val;
        }

        $this->saveConfigPage($configKey, $configPage);
    }

    /**
     * 重置页面配置信息
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     */
    public function resetPage(string $themeName, string $pageName)
    {
        if ($pageName === 'default') {
            $configKey = $this->themeType . '.' . $themeName . '.Page';
        } else {
            $configKey = $this->themeType . '.' . $themeName . '.Page.' . $pageName;
        }

        ConfigHelper::reset($configKey);
    }

    /**
     * 指定页面指定方位配置
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     * @param string $position 位置
     */
    public function editPosition(string $themeName, string $pageName, string $position, array $formData)
    {
        // 获取当前页数的配置信息
        if ($pageName === 'default') {
            $configKey = $this->themeType . '.' . $themeName . '.Page';
        } else {
            $configKey = $this->themeType . '.' . $themeName . '.Page.' . $pageName;
        }

        $configPage = Be::getConfig($configKey);

        if (!isset($formData['enable']) || !is_numeric($formData['enable'])) {
            throw new ServiceException('参数（enable）无效！');
        }

        $formData['enable'] = (int)$formData['enable'];

        if (!in_array($formData['enable'], [-1, 0, 1])) {
            throw new ServiceException('参数（enable）无效！');
        }

        if (!isset($formData['width']) || !is_numeric($formData['width'])) {
            $formData['width'] = 1;
        }

        $formData['width'] = (int)$formData['width'];
        if ($formData['width'] < 1) {
            $formData['width'] = 1;
        }

        if ($formData['width'] > 100) {
            $formData['width'] = 100;
        }

        if (in_array($position, ['west', 'center', 'east'])) {
            if ($formData['enable'] === -1) {
                $configPage->$position = -1;
            } elseif ($formData['enable'] === 0) {
                $configPage->$position = 0;
            } else {
                $configPage->$position = $formData['width'];
                $configPage->middle = 0;
            }
        } else {
            $configPage->$position = $formData['enable'];
            if ($position === 'middle' && $formData['enable'] === 1) {
                $configPage->west = 0;
                $configPage->center = 0;
                $configPage->east = 0;
            }
        }

        $property = $position . 'Sections';
        if ($configPage->$position > 0) {
            if (!isset($configPage->$property) || !is_array($configPage->$property)) {
                $configPage->$property = [];
                if ($pageName !== 'default') {
                    $configPageDefault = Be::getConfig($this->themeType . '.' . $themeName . '.Page');
                    if (isset($configPageDefault->$property) && is_array($configPageDefault->$property)) {
                        $configPage->$property = $configPageDefault->$property;
                    }
                }
            }
        } else {
            unset($configPage->$property);
        }

        $this->saveConfigPage($configKey, $configPage);
    }

    /**
     * 重置方位配置信息
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     * @param string $position 位置
     */
    public function resetPosition(string $themeName, string $pageName, string $position)
    {
        if ($pageName === 'default') {
            $configKey = $this->themeType . '.' . $themeName . '.Page';
            $classOriginal = '\\Be\\' . $this->themeType . '\\' . $themeName . '\\Config\\Page';
            $class = '\\Be\\Data\\' . $this->themeType . '\\' . $themeName . '\\Config\\Page';
        } else {
            $configKey = $this->themeType . '.' . $themeName . '.Page.' . $pageName;
            $classOriginal = '\\Be\\' . $this->themeType . '\\' . $themeName . '\\Config\\Page\\' . str_replace('.', '\\', $pageName);
            $class = '\\Be\\Data\\' . $this->themeType . '\\' . $themeName . '\\Config\\Page\\' . str_replace('.', '\\', $pageName);
        }

        if (!class_exists($class)) {
            return;
        }

        $instanceOriginal = new $classOriginal();
        $configPage = Be::getConfig($configKey);

        $property = $position . 'Sections';

        // 重置部件配置
        if (isset($configPage->$property)) {
            if (isset($instanceOriginal->$property)) {
                $configPage->$property = $instanceOriginal->$property;
            } else {
                unset($configPage->$property);
            }
        }

        $this->saveConfigPage($configKey, $configPage);
    }

    /**
     * 保存配置信息
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     * @param string $position 位置
     * @param int $sectionIndex 部件索引
     * @param array $formData 表单数据
     */
    public function editSection(string $themeName, string $pageName, string $position, int $sectionIndex, array $formData)
    {
        if ($pageName === 'default') {
            $configKey = $this->themeType . '.' . $themeName . '.Page';
        } else {
            $configKey = $this->themeType . '.' . $themeName . '.Page.' . $pageName;
        }

        $configPage = Be::getConfig($configKey);

        $property = $position . 'Sections';

        $sectionData = $configPage->$property[$sectionIndex];

        if (!isset($sectionData['config'])) {
            $sectionConfig = $this->getSectionConfig($sectionData['name']);
        } else {
            $sectionConfig = $sectionData['config'];
        }

        $parts = explode('.', $sectionData['name']);
        $type = array_shift($parts);
        $name = array_shift($parts);
        $classPart = implode('\\', $parts);

        // 配置部件信息
        $class = '\\Be\\' . $type . '\\' . $name . '\\Section\\' . $classPart . '\\Config';
        $newValues = $this->submitFormData($class, $formData, $sectionConfig);
        foreach ($newValues as $key => $val) {
            $sectionConfig->$key = $val;
        }
        $configPage->$property[$sectionIndex]['config'] = $sectionConfig;

        $this->saveConfigPage($configKey, $configPage);
    }

    /**
     * 新增部件
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     * @param string $position 位置
     * @param string $sectionName
     */
    public function addSection(string $themeName, string $pageName, string $position, string $sectionName)
    {
        if ($pageName === 'default') {
            $configKey = $this->themeType . '.' . $themeName . '.Page';
        } else {
            $configKey = $this->themeType . '.' . $themeName . '.Page.' . $pageName;
        }

        $configPage = Be::getConfig($configKey);

        $property = $position . 'Sections';
        $configPage->$property[] = [
            'name' => $sectionName,
        ];

        $this->saveConfigPage($configKey, $configPage);
    }

    /**
     * 新增部件
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     * @param string $position 位置
     * @param int $sectionIndex 部件索引
     */
    public function deleteSection(string $themeName, string $pageName, string $position, int $sectionIndex)
    {
        if ($pageName === 'default') {
            $configKey = $this->themeType . '.' . $themeName . '.Page';
        } else {
            $configKey = $this->themeType . '.' . $themeName . '.Page.' . $pageName;
        }

        $configPage = Be::getConfig($configKey);

        $property = $position . 'Sections';
        if (isset($configPage->$property[$sectionIndex])) {
            unset($configPage->$property[$sectionIndex]);
            $configPage->$property = array_values($configPage->$property);
        }

        $this->saveConfigPage($configKey, $configPage);
    }

    /**
     * 部件排序
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     * @param string $position 位置
     * @param int $oldIndex
     * @param int $newIndex
     */
    public function sortSection(string $themeName, string $pageName, string $position, int $oldIndex, int $newIndex)
    {
        if ($pageName === 'default') {
            $configKey = $this->themeType . '.' . $themeName . '.Page';
        } else {
            $configKey = $this->themeType . '.' . $themeName . '.Page.' . $pageName;
        }

        $configPage = Be::getConfig($configKey);

        $property = $position . 'Sections';

        if (!isset($configPage->$property[$oldIndex]) || !isset($configPage->$property[$newIndex])) {
            throw new ServiceException('部件排序出错：索引超出数据范围');
        }

        $tmpData = $configPage->$property[$oldIndex];
        unset($configPage->$property[$oldIndex]);
        $arr = array_slice($configPage->$property, 0, $newIndex);
        $arr[] = $tmpData;
        $arr = array_merge($arr, array_slice($configPage->$property, $newIndex));
        $configPage->$property = array_values($arr);

        $this->saveConfigPage($configKey, $configPage);
    }

    /**
     * 重置配置信息
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     * @param string $position 位置
     * @param int $sectionIndex 部件索引
     */
    public function resetSection(string $themeName, string $pageName, string $position, int $sectionIndex)
    {
        if ($pageName === 'default') {
            $configKey = $this->themeType . '.' . $themeName . '.Page';
            $class = '\\Be\\Data\\' . $this->themeType . '\\' . $themeName . '\\Config\\Page';
        } else {
            $configKey = $this->themeType . '.' . $themeName . '.Page.' . $pageName;
            $class = '\\Be\\Data\\' . $this->themeType . '\\' . $themeName . '\\Config\\Page\\' . str_replace('.', '\\', $pageName);
        }

        if (!class_exists($class)) {
            return;
        }

        $configPage = Be::getConfig($configKey);

        $property = $position . 'Sections';
        if (isset($configPage->$property[$sectionIndex]['config'])) {
            unset($configPage->$property[$sectionIndex]['config']);
        }

        $this->saveConfigPage($configKey, $configPage);
    }

    /**
     * 保存配置信息
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     * @param string $position 位置
     * @param int $sectionIndex 部件索引
     * @param int $itemIndex 部件子项索引
     * @param array $formData 表单数据
     */
    public function editSectionItem(string $themeName, string $pageName, string $position, int $sectionIndex, int $itemIndex, array $formData)
    {
        if ($pageName === 'default') {
            $configKey = $this->themeType . '.' . $themeName . '.Page';
        } else {
            $configKey = $this->themeType . '.' . $themeName . '.Page.' . $pageName;
        }

        $configPage = Be::getConfig($configKey);

        $property = $position . 'Sections';

        $sectionData = $configPage->$property[$sectionIndex];

        if (!isset($sectionData['config'])) {
            $sectionConfig = $this->getSectionConfig($sectionData['name']);
            $configPage->$property[$sectionIndex]['config'] = $sectionConfig;
        } else {
            $sectionConfig = $sectionData['config'];
        }

        $parts = explode('.', $sectionData['name']);
        $type = array_shift($parts);
        $name = array_shift($parts);
        $classPart = implode('\\', $parts);

        // 配置子部件信息
        $sectionItemData = $sectionConfig->items[$itemIndex];
        $sectionItemName = $sectionItemData['name'];
        if (!isset($sectionItemData['config'])) {
            $sectionItemConfig = $this->getSectionItemConfig($sectionData['name'], $sectionItemName);
        } else {
            $sectionItemConfig = $sectionItemData['config'];
        }

        $class = '\\Be\\' . $type . '\\' . $name . '\\Section\\' . $classPart . '\\Item\\' . $sectionItemName;
        $newValues = $this->submitFormData($class, $formData, $sectionItemConfig);
        foreach ($newValues as $key => $val) {
            $sectionItemConfig->$key = $val;
        }
        $configPage->$property[$sectionIndex]['config']->items[$itemIndex]['config'] = $sectionItemConfig;

        $this->saveConfigPage($configKey, $configPage);
    }

    /**
     * 新增子项目
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     * @param string $position 位置
     * @param int $sectionIndex 部件索引
     * @param string $itemName 部件子项名称
     */
    public function addSectionItem(string $themeName, string $pageName, string $position, int $sectionIndex, string $itemName)
    {
        if ($pageName === 'default') {
            $configKey = $this->themeType . '.' . $themeName . '.Page';
        } else {
            $configKey = $this->themeType . '.' . $themeName . '.Page.' . $pageName;
        }

        $configPage = Be::getConfig($configKey);

        $property = $position . 'Sections';

        $sectionData = $configPage->$property[$sectionIndex];

        if (!isset($sectionData['config'])) {
            $sectionConfig = $this->getSectionConfig($sectionData['name']);
            $configPage->$property[$sectionIndex]['config'] = $sectionConfig;
        } else {
            $sectionConfig = $sectionData['config'];
        }

        if (isset($sectionConfig->items) && is_array($sectionConfig->items)) {
            $sectionConfigItems = $sectionConfig->items;
        } else {
            $sectionConfigItems = [];
        }

        $sectionConfigItems[] = [
            'name' => $itemName,
        ];

        $configPage->$property[$sectionIndex]['config']->items = $sectionConfigItems;

        $this->saveConfigPage($configKey, $configPage);
    }

    /**
     * 新增子部件
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     * @param string $position 位置
     * @param int $sectionIndex 部件索引
     * @param int $itemIndex 部件子项索引
     */
    public function deleteSectionItem(string $themeName, string $pageName, string $position, int $sectionIndex, int $itemIndex)
    {
        if ($pageName === 'default') {
            $configKey = $this->themeType . '.' . $themeName . '.Page';
        } else {
            $configKey = $this->themeType . '.' . $themeName . '.Page.' . $pageName;
        }

        $configPage = Be::getConfig($configKey);

        $property = $position . 'Sections';

        $sectionData = $configPage->$property[$sectionIndex];

        if (!isset($sectionData['config'])) {
            $sectionConfig = $this->getSectionConfig($sectionData['name']);
            $configPage->$property[$sectionIndex]['config'] = $sectionConfig;
        } else {
            $sectionConfig = $sectionData['config'];
        }

        if (isset($sectionConfig->items[$itemIndex])) {
            unset($sectionConfig->items[$itemIndex]);
            $configPage->$property[$sectionIndex]['config']->items = array_values($sectionConfig->items);
        }

        $this->saveConfigPage($configKey, $configPage);
    }

    /**
     * 重置配置信息
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     * @param string $position 位置
     * @param int $sectionIndex 部件索引
     * @param int $itemIndex 部件子项索引
     */
    public function resetSectionItem(string $themeName, string $pageName, string $position, int $sectionIndex, int $itemIndex)
    {
        if ($pageName === 'default') {
            $configKey = $this->themeType . '.' . $themeName . '.Page';
            $class = '\\Be\\Data\\' . $this->themeType . '\\' . $themeName . '\\Config\\Page';
        } else {
            $configKey = $this->themeType . '.' . $themeName . '.Page.' . $pageName;
            $class = '\\Be\\Data\\' . $this->themeType . '\\' . $themeName . '\\Config\\Page\\' . str_replace('.', '\\', $pageName);
        }

        if (!class_exists($class)) {
            return;
        }

        $configPage = Be::getConfig($configKey);

        $property = $position . 'Sections';
        // 重置部件子项配置
        if (isset($configPage->$property[$sectionIndex]['config']->items[$itemIndex]['config'])) {
            unset($configPage->$property[$sectionIndex]['config']->items[$itemIndex]['config']);
        }

        $this->saveConfigPage($configKey, $configPage);
    }

    /**
     * 部件排序
     *
     * @param string $themeName 主题名
     * @param string $pageName 页面名
     * @param string $position 位置
     * @param int $sectionIndex 部件索引
     * @param int $oldIndex 旧索引
     * @param int $newIndex 新索引
     */
    public function sortSectionItem(string $themeName, string $pageName, string $position, int $sectionIndex, int $oldIndex, int $newIndex)
    {
        if ($pageName === 'default') {
            $configKey = $this->themeType . '.' . $themeName . '.Page';
        } else {
            $configKey = $this->themeType . '.' . $themeName . '.Page.' . $pageName;
        }

        $configPage = Be::getConfig($configKey);

        $property = $position . 'Sections';

        $sectionData = $configPage->$property[$sectionIndex];

        if (!isset($sectionData['config'])) {
            $sectionConfig = $this->getSectionConfig($sectionData['name']);
            $configPage->$property[$sectionIndex]['config'] = $sectionConfig;
        } else {
            $sectionConfig = $sectionData['config'];
        }

        $sectionConfigItems = $sectionConfig->items;

        if (!isset($sectionConfigItems[$oldIndex]) ||
            !isset($sectionConfigItems[$newIndex])) {
            throw new ServiceException('子部件排序出错：索引超出数据范围' . $property . '-' . $sectionIndex . '-' . $oldIndex . '-' . $newIndex);
        }

        $tmpData = $sectionConfigItems[$oldIndex];
        unset($sectionConfigItems[$oldIndex]);
        $arr = array_slice($sectionConfigItems, 0, $newIndex);
        $arr[] = $tmpData;
        $arr = array_merge($arr, array_slice($sectionConfigItems, $newIndex));

        $configPage->$property[$sectionIndex]['config']->items = array_values($arr);

        $this->saveConfigPage($configKey, $configPage);
    }

    /**
     * 保存页面配置
     *
     * @param string $configKey
     * @param object $configPage
     * @return void
     */
    private function saveConfigPage(string $configKey, object $configPage)
    {
        if ($configPage->middle !== 0) {
            $configPage->west = 0;
            $configPage->center = 0;
            $configPage->east = 0;
            unset($configPage->westSections, $configPage->centerSections, $configPage->eastSections);
        }

        if ($configPage->west !== 0 || $configPage->center !== 0 || $configPage->east !== 0) {
            $configPage->middle = 0;
            unset($configPage->middleSections);
        }

        foreach (['north', 'middle', 'west', 'center', 'east', 'south'] as $position) {
            if ($configPage->$position <= 0) {
                $property = $position . 'Sections';
                unset($configPage->$property);
            }
        }

        ConfigHelper::update($configKey, $configPage);
    }

    /**
     * 指定类名的配置项表单提交
     *
     * @param string $className 类名
     * @param array $formData 表单数据
     * @param object $config 配置数据
     * @return array
     * @throws ServiceException
     * @throws \Be\AdminPlugin\AdminPluginException
     * @throws \ReflectionException
     */
    private function submitFormData(string $className, array $formData, object $config): array
    {
        $originalConfigInstance = new $className();

        $newValues = [];
        $reflection = new \ReflectionClass($className);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $itemName = $property->getName();

            if ($itemName === 'items') {
                continue;
            }

            if (!isset($formData[$itemName])) {
                continue;
                //throw new ServiceException('参数 (' . $itemName . ') 缺失！');
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
                    if (substr($configItem['driver'], 0, 8) === 'FormItem') {
                        $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $configItem['driver'];
                    } else {
                        $driverClass = $configItem['driver'];
                    }
                } else {
                    $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
                }

                if (isset($config->$itemName)) {
                    $configItem['value'] = $config->$itemName;
                }

                $driver = new $driverClass($configItem);
                $driver->submit($formData);

                $newValues[$itemName] = $driver->newValue;
            }
        }

        return $newValues;
    }

}

