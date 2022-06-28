<?php

namespace Be\App\System\Service;

use Be\App\ServiceException;
use Be\Be;
use Be\Util\Str\CaseConverter;

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

        $configPage = Be::getConfig($themeType . '.' . $themeName . '.' . $route);
        foreach (['north', 'middle', 'west', 'center', 'east', 'south'] as $position) {
            $page->$position = $configPage->$position;
            if ($configPage->$position === 1) {
                $fieldSections = $position . 'Sections';
                $fieldSectionConfigs = $position . 'SectionConfigs';
                if (count($configPage->$fieldSections)) {
                    $sectionConfigs = $configPage->$fieldSectionConfigs ?? null;
                    $sections = [];
                    $i = 0;
                    foreach ($configPage->$fieldSections as $sectionName) {

                        $sectionConfig = null;
                        if ($sectionConfigs !== null) {
                            $sectionConfig = $sectionConfigs[$i];
                        }

                        $sections[] = $this->getSection($sectionName, $sectionConfig, $i);
                        $i++;
                    }
                    $page->$fieldSections = $sections;
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
     * @param int $index 部件索引编号
     * @return object
     */
    private function getSection(string $sectionName, array $sectionConfig = null, int $index): object
    {
        $parts = explode('.', $sectionName);

        $section = new \stdClass();
        $id = '';
        foreach ($parts as $part) {
            $id .= CaseConverter::camel2Hyphen($part) . '-';
        }
        $id .= $index;
        $section->id = $id;

        $themeType = array_shift($parts);
        $themeName = array_shift($parts);
        $class = '\\Be\\' . $themeType . '\\' . $themeName . '\\Section\\' . implode('\\', $parts) . '\\Template';
        if (!class_exists($class)) {
            throw new ServiceException('Theme section template (' . $sectionName . '.Template) does not exist!');
        }
        $template = new $class();
        $template->setId($id);

        if ($sectionConfig === null) {
            $class = '\\Be\\' . $themeType . '\\' . $themeName . '\\Section\\' . implode('\\', $parts) . '\\Config';
            if (!class_exists($class)) {
                throw new ServiceException('Theme section config (' . $sectionName . '.Config) does not exist!');
            }

            $sectionConfig = new $class();

            if (isset($sectionConfig->items)) {
                if (count($sectionConfig->items) > 0) {
                    foreach ($sectionConfig->items as $item) {
                        if (!isset($item['config'])) {
                            $class = '\\Be\\' . $themeType . '\\' . $themeName . '\\Section\\' . implode('\\', $parts) . '\\Item\\' . $item['name'];
                            if (!class_exists($class)) {
                                throw new ServiceException('Theme section config item (' . $sectionName . '.Item.' . $item['name'] . ') does not exist!');
                            }

                            $item['config'] = get_object_vars(new $class());
                        }
                    }
                }
            }
        }

        $template->setConfig($sectionConfig);

        $section->template = $template;

        return $section;
    }

}
