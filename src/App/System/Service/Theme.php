<?php

namespace Be\App\System\Service;

use Be\App\ServiceException;
use Be\Be;

class Theme
{


    /**
     * 获取页面
     *
     * @param string $themeType 主题类型
     * @param string $themeName 主题名
     * @param string $route 页面路由
     * @return \stdClass
     */
    public function getPageConfig(string $themeType, string $themeName, string $route): object
    {
        $pageConfig = clone Be::getConfig($themeType . '.' . $themeName . '.Page.' . $route);
        foreach (['north', 'middle', 'west', 'center', 'east', 'south'] as $position) {
            if ($pageConfig->$position !== 0) {
                $property = $position . 'Sections';
                if (isset($pageConfig->$property) && is_array($pageConfig->$property) && count($pageConfig->$property) > 0) {
                    $sections = [];
                    foreach ($pageConfig->$property as $sectionIndex => $sectionData) {
                        $sectionConfig = $sectionData['config'] ?? null;
                        try {
                            $section = $this->getSection($route, $sectionData['name'], $sectionConfig, $position, $sectionIndex);
                            $section->key = $sectionData['name'];
                            $section->name = $sectionData['name'];
                            $sections[] = $section;
                        } catch (\Throwable $t) {
                        }
                    }
                    $pageConfig->$property = $sections;
                } else {
                    $pageConfig->$property = [];
                }
            }
        }

        $pageConfig->spacingMobile = $pageConfig->spacingMobile ?? '';
        $pageConfig->spacingTablet = $pageConfig->spacingTablet ?? '';
        $pageConfig->spacingDesktop = $pageConfig->spacingDesktop ?? '';

        return $pageConfig;
    }

    /**
     * 获取部件
     *
     * @param string $route 页面路由
     * @param string $sectionName 部件名
     * @param array|null $sectionConfig 部件配置数据
     * @param string $position 方位
     * @param int $sectionIndex 部件索引编号
     * @return object
     */
    private function getSection(string $route, string $sectionName, ?array $sectionConfig, string $position, int $sectionIndex): object
    {
        $parts = explode('.', $sectionName);
        $type = array_shift($parts);
        $name = array_shift($parts);
        $classPart = implode('\\', $parts);

        $class = '\\Be\\' . $type . '\\' . $name . '\\Section\\' . $classPart . '\\Template';
        if (!class_exists($class)) {
            throw new ServiceException('Section template (' . $sectionName . '.Template) does not exist!');
        }

        $section = new \stdClass();
        $section->id = 'be-section-' . $position . '-' . $sectionIndex;
        $section->position = $position;
        $section->route = $route;

        $template = new $class();
        $template->id = $section->id;
        $template->position = $position;
        $template->route = $route;

        if ($sectionConfig === null) {
            $class = '\\Be\\' . $type . '\\' . $name . '\\Section\\' . $classPart . '\\Config';
            if (!class_exists($class)) {
                throw new ServiceException('Section config (' . $sectionName . '.Config) does not exist!');
            }

            $sectionConfig = new $class();

            if (isset($sectionConfig->items)) {
                if (count($sectionConfig->items) > 0) {
                    foreach ($sectionConfig->items as &$item) {
                        if (!isset($item['config'])) {
                            $class = '\\Be\\' . $type . '\\' . $name . '\\Section\\' . $classPart . '\\Item\\' . $item['name'];
                            if (!class_exists($class)) {
                                throw new ServiceException('Section item config (' . $sectionName . '.Item.' . $item['name'] . ') does not exist!');
                            }

                            $item['config'] = new $class();
                        }
                    }
                    unset($item);
                }
            }
        } else {
            $sectionConfig = (object)$sectionConfig;

            if (isset($sectionConfig->items)) {
                if (count($sectionConfig->items) > 0) {
                    foreach ($sectionConfig->items as &$item) {
                        if (!isset($item['config'])) {
                            $class = '\\Be\\' . $type . '\\' . $name . '\\Section\\' . $classPart . '\\Item\\' . $item['name'];
                            if (!class_exists($class)) {
                                throw new ServiceException('Section item config  (' . $sectionName . '.Item.' . $item['name'] . ') does not exist!');
                            }

                            $item['config'] = new $class();
                        } else {
                            $item['config'] = (object)$item['config'];
                        }
                    }
                    unset($item);
                }
            }
        }

        $template->config = $sectionConfig;

        $section->template = $template;

        return $section;
    }

}
