<?php

namespace Be\Template;

use Be\Be;
use Be\Runtime\RuntimeException;
use Be\Util\Str\CaseConverter;

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
        $fileTheme = $themeProperty->getPath() . '/' . $themeName . '.php';
        if (!file_exists($fileTheme)) {
            throw new RuntimeException($themeType . ' ' . $themeName . ' does not exist!');
        }

        $parts = explode('.', $templateName);
        $type = array_shift($parts);
        $name = array_shift($parts);

        $path = Be::getRuntime()->getRootPath() . '/data/Runtime/' . $templateType . '/' . $themeName . '/' . $type . '/' . $name . '/' . implode('/', $parts) . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        $contentTheme = file_get_contents($fileTheme);

        $tags = [];
        foreach (['be-body', 'be-north', 'be-middle', 'be-west', 'be-center', 'be-east', 'be-south', 'be-page-title', 'be-page-content', 'be-section', 'be-section-title', 'be-section-content'] as $key) {
            $wrapperPath = $themeProperty->getPath() . '/Tag/' . $key . '.php';
            if (file_exists($wrapperPath)) {
                $wrapperContent = file_get_contents($wrapperPath);
                $wrapperParts = explode('|||', $wrapperContent);
                if (count($wrapperParts) === 2) {
                    $tags[$key] = $wrapperParts;
                    continue;
                }
            }

            $tags[$key] = ['<div class="' . $key . '">', '</div>'];
        }

        $codeHtml = '';

        $codeFunctions = [];

        $extends = '\\Be\\Template\\Driver';

        $property = Be::getProperty($type . '.' . $name);
        $fileTemplate = $property->getPath() . '/Template/' . implode('/', $parts) . '.php';
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
                        $fileInclude = $tmpProperty->getPath() . '/' . $templateType . '/' . implode('/', $includes) . '.php';
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
            } else {

                if (preg_match($pattern, $contentTheme, $matches)) {
                    $codeHtml = trim($matches[1]);

                    $pattern = '/<be-head>(.*?)<\/be-head>/s';
                    if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 head
                        $codeHtml = preg_replace($pattern, '<?php $this->head(); ?>', $codeHtml);
                        $codeFunctions['head'] = $matches[1];
                    }

                    $pattern = '/<be-body>(.*?)<\/be-body>/s';
                    if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 body
                        $codeHtml = preg_replace($pattern, '<?php $this->body(); ?>', $codeHtml);
                        $codeFunctions['body'] = $matches[1];
                    } else {

                        $pattern = '/<be-north>(.*?)<\/be-north>/s';
                        if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 north
                            $codeHtml = preg_replace($pattern, '<?php $this->north(); ?>', $codeHtml);
                            $codeFunctions['north'] = $matches[1];
                        }

                        $pattern = '/<be-middle>(.*?)<\/be-middle>/s';
                        if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 north
                            $codeHtml = preg_replace($pattern, '<?php $this->middle(); ?>', $codeHtml);
                            $codeFunctions['middle'] = $matches[1];
                        } else {
                            $pattern = '/<be-west>(.*?)<\/be-west>/s';
                            if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 west
                                $codeHtml = preg_replace($pattern, '<?php $this->west(); ?>', $codeHtml);
                                $codeFunctions['west'] = $matches[1];
                            }

                            $pattern = '/<be-center>(.*?)<\/be-center>/s';
                            if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 center
                                $codeHtml = preg_replace($pattern, '<?php $this->center(); ?>', $codeHtml);
                                $codeFunctions['center'] = $matches[1];;
                            } else {
                                $pattern = '/<be-page-title>(.*?)<\/be-page-title>/s';
                                if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 page-title
                                    $codeHtml = preg_replace($pattern, '<?php $this->pageTitle(); ?>', $codeHtml);
                                    $codeFunctions['page-title'] = $matches[1];
                                }

                                $pattern = '/<be-page-content>(.*?)<\/be-page-content>/s';
                                if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 page-content
                                    $codeHtml = preg_replace($pattern, '<?php $this->pageContent(); ?>', $codeHtml);
                                    $codeFunctions['page-content'] = $matches[1];
                                }
                            }

                            $pattern = '/<be-east>(.*?)<\/be-east>/s';
                            if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 east
                                $codeHtml = preg_replace($pattern, '<?php $this->east(); ?>', $codeHtml);
                                $codeFunctions['east'] = $matches[1];
                            }
                        }

                        $pattern = '/<be-south>(.*?)<\/be-south>/s';
                        if (preg_match($pattern, $contentTemplate, $matches)) { // 查找替换 north
                            $codeHtml = preg_replace($pattern, '<?php $this->south(); ?>', $codeHtml);
                            $codeFunctions['south'] = $matches[1];
                        }
                    }
                }
            }

        } else {
            $pattern = '/<be-html>(.*?)<\/be-html>/s';

            // 无 template 的页面，直接使用 theme
            if (preg_match($pattern, $contentTheme, $matches)) {
                $codeHtml = trim($matches[1]);
            }
        }

        foreach (['be-section', 'be-section-title', 'be-section-content'] as $key) {
            $replace = '<be' . $key . '>';
            if (strpos($codeHtml, $replace) !== false) {
                $codeHtml = str_replace($replace, '<?php $this->tag0(\'' . $key . '\'); ?>', $codeHtml);
            }

            $replace = '</' . $key . '>';
            if (strpos($codeHtml, $replace) !== false) {
                $codeHtml = str_replace($replace, '<?php $this->tag1(\'' . $key . '\'); ?>', $codeHtml);
            }
        }

        foreach (['page-title', 'page-content', 'west', 'east', 'north', 'south', 'center', 'middle', 'body', 'head', 'html',] as $key) {
            $pattern = '/<be-' . $key . '>(.*?)<\/be-' . $key . '>/s';
            if (preg_match($pattern, $codeHtml, $matches)) {
                $codeHtml = preg_replace($pattern, '<?php $this->' . CaseConverter::Hyphen2CamelLcFirst($key) . '(); ?>', $codeHtml);

                $codeBody = $matches[1];
                $codeBody = trim($codeBody);
                if ($codeBody !== '') {
                    $codeFunctions[$key] = $codeBody;
                }
            }
        }

        $className = array_pop($parts);

        $namespace = 'Be\\Data\\Runtime\\' . $templateType . '\\' . $themeName . '\\' . $type . '\\' . $name;
        if (count($parts) > 0) {
            $namespace .= '\\' . implode('\\', $parts);
        }

        $codePhp = '<?php' . "\n";
        $codePhp .= 'namespace ' . $namespace . ";\n\n";
        $codePhp .= "\n";
        $codePhp .= 'class ' . $className . ' extends ' . $extends . "\n";
        $codePhp .= '{' . "\n";
        $codePhp .= '  public array $_tags = ' . var_export($tags, true) . ';' . "\n";
        $codePhp .= '  public function html()' . "\n";
        $codePhp .= '  {' . "\n";
        $codePhp .= '    ?>' . "\n";
        $codePhp .= $codeHtml . "\n";
        $codePhp .= '    <?php' . "\n";
        $codePhp .= '  }' . "\n\n";

        if (count($codeFunctions) > 0) {
            foreach ($codeFunctions as $key => $codeFunction) {
                $codeFunction = trim($codeFunction);

                switch ($key) {
                    case 'head':
                        $codePhp .= 'public function ' . $key . '()' . "\n";
                        $codePhp .= '{' . "\n";
                        $codePhp .= '    ?>' . "\n";
                        $codePhp .= $codeFunction . "\n";
                        $codePhp .= '    <?php' . "\n";
                        $codePhp .= '}' . "\n\n";
                        break;
                    case 'body':
                        $codePhp .= 'public function ' . $key . '()' . "\n";
                        $codePhp .= '{' . "\n";
                        $codePhp .= '    echo $this->tag0(\'be-' . $key . '\');' . "\n";
                        $codePhp .= '    ?>' . "\n";
                        $codePhp .= $codeFunction . "\n";
                        $codePhp .= '    <?php' . "\n";
                        $codePhp .= '    echo $this->tag1(\'be-' . $key . '\');' . "\n";
                        $codePhp .= '}' . "\n\n";
                        break;
                    case 'middle':
                        $codePhp .= 'public function ' . $key . '()' . "\n";
                        $codePhp .= '{' . "\n";
                        $codePhp .= '  if ($this->_page->middle !== 0 || $this->_page->west !== 0 || $this->_page->east !== 0 || $this->_page->center !== 0) {' . "\n";
                        $codePhp .= '    echo $this->tag0(\'be-' . $key . '\');' . "\n";
                        $codePhp .= '    ?>' . "\n";
                        $codePhp .= $codeFunction . "\n";
                        $codePhp .= '    <?php' . "\n";
                        $codePhp .= '    echo $this->tag1(\'be-' . $key . '\');' . "\n";
                        $codePhp .= '  }' . "\n";
                        $codePhp .= '}' . "\n\n";
                        break;
                    case 'north':
                    case 'west':
                    case 'center':
                    case 'east':
                    case 'south':
                        $codePhp .= 'public function ' . $key . '()' . "\n";
                        $codePhp .= '{' . "\n";
                        $codePhp .= '  if ($this->_page->' . $key . ' !== 0) {' . "\n";
                        $codePhp .= '    echo $this->tag0(\'be-' . $key . '\');' . "\n";
                        $codePhp .= '    ?>' . "\n";
                        $codePhp .= $codeFunction . "\n";
                        $codePhp .= '    <?php' . "\n";
                        $codePhp .= '    echo $this->tag1(\'be-' . $key . '\');' . "\n";
                        $codePhp .= '  }' . "\n";
                        $codePhp .= '}' . "\n\n";
                        break;
                    case 'page-title':
                    case 'page-content':
                        $codePhp .= 'public function ' . CaseConverter::Hyphen2CamelLcFirst($key) . '()' . "\n";
                        $codePhp .= '{' . "\n";
                        $codePhp .= '    echo $this->tag0(\'be-' . $key . '\');' . "\n";
                        $codePhp .= '    ?>' . "\n";
                        $codePhp .= $codeFunction . "\n";
                        $codePhp .= '    <?php' . "\n";
                        $codePhp .= '    echo $this->tag1(\'be-' . $key . '\');' . "\n";
                        $codePhp .= '}' . "\n\n";
                        break;
                }
            }
        }

        $codePhp .= '}' . "\n\n";

        $pattern = '/\s*\?>\s+<\?php\s*/s';
        $codePhp = preg_replace($pattern, "\n", $codePhp);

        file_put_contents($path, $codePhp, LOCK_EX);
        chmod($path, 0777);
    }

    /**
     * 文件是否有改动
     *
     * @param string $templateName 模析名
     * @param string $themeName 主题名
     * @param bool $admin 是否后台模板
     * @return bool
     * @throws \Exception
     */
    public static function isModified($templateName, $themeName, $admin = false): bool
    {
        $themeType = $admin ? 'AdminTheme' : 'Theme';
        $templateType = $admin ? 'AdminTemplate' : 'Template';

        $parts = explode('.', $templateName);
        $type = array_shift($parts);
        $name = array_shift($parts);
        $compiledFile = Be::getRuntime()->getRootPath() . '/data/Runtime/' . $templateType . '/' . $themeName . '/' . $type . '/' . $name . '/' . implode('/', $parts) . '.php';

        $property = Be::getProperty($type . '.' . $name);
        $templateFile = $property->getPath() . '/Template/' . implode('/', $parts) . '.php';

        $themeProperty = Be::getProperty($themeType . '.' . $themeName);
        $themeFile = $themeProperty->getPath() . '/' . $themeName . '.php';

        $compileTime = file_exists($compiledFile) ? filemtime($compiledFile) : 0;
        $templateModifyTime = file_exists($templateFile) ? filemtime($templateFile) : 0;
        $themeModifyTime = file_exists($themeFile) ? filemtime($themeFile) : 0;

        return $templateModifyTime > $compileTime || $themeModifyTime > $compileTime;
    }

}


