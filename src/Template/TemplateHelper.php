<?php

namespace Be\Template;

use Be\Be;
use Be\Runtime\RuntimeException;

class TemplateHelper
{

    /**
     * 更新模板
     *
     * @param string $templateName 模析名
     * @param string $themeName 主题名
     * @param bool $admin 是否后台模板
     * @throws \Exception
     */
    public static function update($templateName, $themeName, $admin = false)
    {
        $themeType = $admin ? 'AdminTheme' : 'Theme';
        $templateType = $admin ? 'AdminTemplate' : 'Template';

        $themeProperty = Be::getProperty($themeType . '.' . $themeName);
        $runtime = Be::getRuntime();
        $fileTheme = $runtime->getRootPath() . $themeProperty->getPath() . '/' . $themeName . '.php';
        if (!file_exists($fileTheme)) {
            throw new RuntimeException($themeType . ' ' . $themeName . ' does not exist!');
        }

        $parts = explode('.', $templateName);
        $type = array_shift($parts);
        $name = array_shift($parts);

        $path = $runtime->getRootPath() . '/data/Runtime/' . $templateType . '/' . $themeName . '/' . $type . '/' . $name . '/' . implode('/', $parts) . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        $contentTheme = file_get_contents($fileTheme);
        $tags = [];
        foreach (['be-body', 'be-north', 'be-middle', 'be-west', 'be-center', 'be-east', 'be-south', 'be-page-title', 'be-page-content'] as $key) {
            $wrapperPath = Be::getRuntime()->getRootPath() . $themeProperty->getPath() . '/Tag/' . $key . '.php';
            if (file_exists($wrapperPath)) {
                $wrapperContent = file_get_contents($wrapperPath);
                $wrapperParts = explode('|||', $wrapperContent);
                if (count($wrapperParts) === 2) {
                    $tags[$key] = $wrapperParts;
                    continue;
                }
            }

            $beginWrapper = null;
            $endWrapper = null;
            $pattern = '/<' . $key . '-begin>(.*)<\/' . $key . '-begin>/s';
            if (preg_match($pattern, $contentTheme, $matches)) {
                $beginWrapper = $matches[1];
                $contentTheme = preg_replace($pattern, '<' . $key . '>', $contentTheme);
            } else {
                $beginWrapper = '<div class="' . $key . '">';
            }

            $pattern = '/<' . $key . '-end>(.*)<\/' . $key . '-end>/s';
            if (preg_match($pattern, $contentTheme, $matches)) {
                $endWrapper = $matches[1];
                $contentTheme = preg_replace($pattern, '</' . $key . '>', $contentTheme);
            } else {
                $endWrapper = '</div>';
            }

            $tags[$key] = [$beginWrapper, $endWrapper];
        }

        $codeUse = '';
        $pattern = '/use\s+(.+);/';
        $uses = null;
        if (preg_match_all($pattern, $contentTheme, $matches)) {
            $uses = $matches[1];
            foreach ($matches[1] as $m) {
                $codeUse .= 'use ' . $m . ';' . "\n";
            }
        }

        $codePre = '';
        $pattern = '/<\?php(.*?)\?>\s+<be-html>/s';
        if (preg_match($pattern, $contentTheme, $matches)) {
            $codePreTheme = trim($matches[1]);
            $codePreTheme = preg_replace('/use\s+(.+);/', '', $codePreTheme);
            $codePreTheme = preg_replace('/\s+$/m', '', $codePreTheme);
            $codePre = $codePreTheme . "\n";
        }

        $codeHtml = '';

        $extends = '\\Be\\Template\\Driver';

        $property = Be::getProperty($type . '.' . $name);
        $fileTemplate = $runtime->getRootPath() . $property->getPath() . '/Template/' . implode('/', $parts) . '.php';
        if (file_exists($fileTemplate)) {
            $contentTemplate = file_get_contents($fileTemplate);
            $contentTemplate = str_replace('\\\\', '\\\\\\\\', $contentTemplate);

            // 模板继承了其它模板
            if (preg_match('/<be-extends>(.*?)<\/be-extends>/s', $contentTemplate, $matches)) {
                $extends = trim($matches[1]);
                self::update($extends, $themeName);
                $contentTemplate = str_replace($matches[0], '', $contentTemplate);
            }

            // 模板中包含了其它文件
            if (preg_match_all('/<be-include>(.*?)<\/be-include>/s', $contentTemplate, $matches)) {
                $i = 0;
                foreach ($matches[1] as $m) {
                    $includes = explode('.', $m);
                    if (count($includes) > 2) {
                        $tmpType = array_shift($includes);
                        $tmpName = array_shift($includes);

                        $tmpProperty = Be::getProperty($tmpType . '.' . $tmpName);
                        $fileInclude = $runtime->getRootPath() . $tmpProperty->getPath() . '/' . $templateType . '/' . implode('/', $includes) . '.php';
                        if (!file_exists($fileInclude)) {
                            // 模板中包含的文件 $m 不存在
                            throw new RuntimeException($templateType . ' include file ' . $m . ' does not exist!');
                        }

                        $contentInclude = file_get_contents($fileInclude);
                        $contentTemplate = str_replace($matches[0][$i], $contentInclude, $contentTemplate);
                    }
                    $i++;
                }
            }

            $pattern = '/<be-html>(.*?)<\/be-html>/s';
            if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 html
                $codeHtml = trim($matches[1]);

                if (preg_match_all('/use\s+(.+);/', $contentTemplate, $matches)) {
                    foreach ($matches[1] as $m) {
                        $codeUse .= 'use ' . $m . ';' . "\n";
                    }
                }

                $pattern = '/<\?php(.*?)\?>\s*<be-html>/s';
                if (preg_match($pattern, $contentTemplate, $matches)) {
                    $codePre = trim($matches[1]);
                    $codePre = preg_replace('/use\s+(.+);/', '', $codePre);
                    $codePre = preg_replace('/\s+$/m', '', $codePre);
                }

            } else {

                if (preg_match($pattern, $contentTheme, $matches)) {
                    $codeHtml = trim($matches[1]);

                    $templateNameNoTags = true;
                    $pattern = '/<be-head>(.*?)<\/be-head>/s';
                    if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 head
                        $codeHead = $matches[1];
                        $codeHtml = preg_replace($pattern, $codeHead, $codeHtml);
                        $templateNameNoTags = false;
                    }

                    $pattern = '/<be-body>(.*?)<\/be-body>/s';
                    if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 body
                        $codeBody = $matches[1];
                        $codeHtml = preg_replace($pattern, $tags['be-body'][0] . $codeBody . $tags['be-body'][1], $codeHtml);
                        $templateNameNoTags = false;
                    } else {

                        $pattern = '/<be-north>(.*?)<\/be-north>/s';
                        if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 north
                            $codeNorth = $matches[1];
                            $codeHtml = preg_replace($pattern, $tags['be-north'][0] . $codeNorth . $tags['be-north'][1], $codeHtml);
                            $templateNameNoTags = false;
                        }

                        $pattern = '/<be-middle>(.*?)<\/be-middle>/s';
                        if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 north
                            $codeMiddle = $matches[1];
                            $codeHtml = preg_replace($pattern, $tags['be-middle'][0] . $codeMiddle . $tags['be-middle'][1], $codeHtml);
                            $templateNameNoTags = false;
                        } else {
                            $pattern = '/<be-west>(.*?)<\/be-west>/s';
                            if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 west
                                $codeWest = $matches[1];
                                $codeHtml = preg_replace($pattern, $tags['be-west'][0] . $codeWest . $tags['be-west'][1], $codeHtml);
                                $templateNameNoTags = false;
                            }

                            $pattern = '/<be-center>(.*?)<\/be-center>/s';
                            if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 center
                                $codeCenter = $matches[1];
                                $codeHtml = preg_replace($pattern, $tags['be-center'][0] . $codeCenter . $tags['be-center'][1], $codeHtml);
                                $templateNameNoTags = false;
                            } else {
                                $pattern = '/<be-page-title>(.*?)<\/be-page-title>/s';
                                if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 center
                                    $codeCenterTitle = $matches[1];
                                    $codeHtml = preg_replace($pattern, $tags['be-page-title'][0] . $codeCenterTitle . $tags['be-page-title'][1], $codeHtml);
                                    $templateNameNoTags = false;
                                }

                                $pattern = '/<be-page-content>(.*?)<\/be-page-content>/s';
                                if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 content
                                    $codeCenterBody = $matches[1];
                                    $codeHtml = preg_replace($pattern, $tags['be-page-content'][0] . $codeCenterBody . $tags['be-page-content'][1], $codeHtml);
                                    $templateNameNoTags = false;
                                }
                            }

                            $pattern = '/<be-east>(.*?)<\/be-east>/s';
                            if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 east
                                $codeEast = $matches[1];
                                $codeHtml = preg_replace($pattern, $tags['be-east'][0] . $codeEast . $tags['be-east'][1], $codeHtml);
                                $templateNameNoTags = false;
                            }
                        }

                        $pattern = '/<be-south>(.*?)<\/be-south>/s';
                        if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 north
                            $codeSouth = $matches[1];
                            $codeHtml = preg_replace($pattern, $tags['be-south'][0] . $codeSouth . $tags['be-south'][1], $codeHtml);
                            $templateNameNoTags = false;
                        }
                    }

                    // 没有指定标签，所有内容放放 content
                    if ($templateNameNoTags) {
                        $pattern = '/<be-page-content>(.*?)<\/be-page-content>/s';
                        $codeHtml = preg_replace($pattern, $tags['be-page-content'][0] . $contentTemplate . $tags['be-page-content'][1], $codeHtml);
                    }
                }

                $pattern = '/use\s+(.+);/';
                if (preg_match_all($pattern, $contentTemplate, $matches)) {
                    foreach ($matches[1] as $m) {
                        if ($uses !== null && !in_array($m, $uses)) {
                            $codeUse .= 'use ' . $m . ';' . "\n";
                        }
                    }
                }

                $pattern = '/<\?php(.*?)\?>\s+<be-(?:html|head|body|north|middle|west|center|east|south|page-title|page-content)>/s';
                if (preg_match($pattern, $contentTemplate, $matches)) {
                    $codePreTemplate = trim($matches[1]);
                    $codePreTemplate = preg_replace('/use\s+(.+);/', '', $codePreTemplate);
                    $codePreTemplate = preg_replace('/\s+$/m', '', $codePreTemplate);

                    $codePre .= $codePreTemplate . "\n";
                }
            }

        } else {

            $pattern = '/<be-html>(.*?)<\/be-html>/s';

            // 无 template 的页面，直接使用 theme
            if (preg_match($pattern, $contentTheme, $matches)) {
                $codeHtml = trim($matches[1]);
            }
        }

        foreach (['be-html', 'be-head'] as $key) {
            if (strpos($codeHtml, '<' . $key . '>') !== false) {
                $codeHtml = str_replace('<' . $key . '>', '', $codeHtml);
            }

            if (strpos($codeHtml, '</' . $key . '>') !== false) {
                $codeHtml = str_replace('</' . $key . '>', '', $codeHtml);
            }
        }

        foreach (['be-body', 'be-north', 'be-middle', 'be-west', 'be-center', 'be-east', 'be-south', 'be-page-title', 'be-page-content',] as $key) {
            if (strpos($codeHtml, '<' . $key . '>') !== false) {
                $codeHtml = str_replace('<' . $key . '>', $tags[$key][0], $codeHtml);
            }

            if (strpos($codeHtml, '</' . $key . '>') !== false) {
                $codeHtml = str_replace('</' . $key . '>', $tags[$key][1], $codeHtml);
            }
        }

        $codeVars = '';

        $className = array_pop($parts);

        $namespace = 'Be\\Data\\Runtime\\' . $templateType . '\\' . $themeName . '\\' . $type . '\\' . $name;
        if (count($parts) > 0) {
            $namespace .= '\\' . implode('\\', $parts);
        }

        $codePhp = '<?php' . "\n";
        $codePhp .= 'namespace ' . $namespace . ";\n\n";
        $codePhp .= $codeUse;
        $codePhp .= "\n";
        $codePhp .= 'class ' . $className . ' extends ' . $extends . "\n";
        $codePhp .= '{' . "\n";
        $codePhp .= $codeVars;
        $codePhp .= "\n";
        $codePhp .= '  public function display()' . "\n";
        $codePhp .= '  {' . "\n";
        $codePhp .= $codePre;
        $codePhp .= '    ?>' . "\n";
        $codePhp .= $codeHtml . "\n";
        $codePhp .= '    <?php' . "\n";
        $codePhp .= '  }' . "\n";
        $codePhp .= '}' . "\n";
        $codePhp .= "\n";

        file_put_contents($path, $codePhp, LOCK_EX);
        chmod($path, 0777);
    }


}


