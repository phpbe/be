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
    public function getPage(string $themeType, string $themeName, string $route): object
    {
        $page = new \stdClass();

        $configPage = Be::getConfig($themeType . '.' . $themeName . '.Page.' . $route);
        foreach (['north', 'middle', 'west', 'center', 'east', 'south'] as $position) {
            $page->$position = $configPage->$position;
            if ($configPage->$position !== 0) {
                $property = $position . 'Sections';
                if (isset($configPage->$property) && is_array($configPage->$property) && count($configPage->$property) > 0) {
                    $sections = [];
                    foreach ($configPage->$property as $sectionIndex => $sectionData) {

                        $sectionName = $sectionData['name'];
                        if ($sectionData['name'] === 'be-page-title') {
                            $sectionName = $configPage->pageTitleSection ?? ($themeType . '.System.PageTitle');
                        } elseif ($sectionData['name'] === 'be-page-content') {
                            $sectionName = $configPage->pageContentSection ?? ($themeType . '.System.PageContent');
                        }

                        $sectionConfig = $sectionData['config'] ?? null;

                        $section = $this->getSection($sectionName, $sectionConfig, $position, $sectionIndex);
                        $section->key = $sectionData['name'];
                        $section->name = $sectionName;

                        $sections[] = $section;
                    }
                    $page->$property = $sections;
                } else {
                    $page->$property = [];
                }
            }
        }

        return $page;
    }

    /**
     * 获取部件
     *
     * @param string $themeType 主题类型
     * @param string $themeName 主题名
     * @param string $sectionName 部件名
     * @param object $sectionConfig 部件配置数据
     * @param int $sectionIndex 部件索引编号
     * @return object
     */
    private function getSection(string $sectionName, array $sectionConfig = null, string $position, int $sectionIndex): object
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

        $template = new $class();
        $template->id = $section->id;
        $template->position = $position;

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
