<?php

namespace Be\Template;

use Be\Be;

/**
 * 模板基类
 */
class Driver
{
    public string $title = ''; // 标题
    public string $metaKeywords = ''; // meta keywords
    public string $metaDescription = '';  // meta description

    public array $_tags = []; // 可用的标签

    /**
     * @var object
     */
    public object $_page; // 页面配置信息对象

    public function get(string $key, $default = null)
    {
        if (isset($this->$key)) return $this->$key;
        return $default;
    }

    public function display()
    {
        $this->html();
    }

    public function html()
    {
        ?>
<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no,viewport-fit=cover">
        <title><?php echo $this->title; ?></title>
        <meta name="keywords" content="<?php echo $this->metaKeywords ?? ''; ?>">
        <meta name="description" content="<?php echo $this->metaDescription ?? ''; ?>">
        <meta name="applicable-device" content="pc,mobile">
        <base href="<?php echo beUrl(); ?>/">
        <link rel="icon" href="favicon.ico" type="image/x-icon"/>
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
        <script src="https://libs.baidu.com/jquery/2.0.3/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdn.phpbe.com/scss/be.css"/>
        <?php $this->head(); ?>
    </head>
<body>
    <?php $this->body(); ?>
</body>
</html>
        <?php
    }


    public function head()
    {
    }

    public function body()
    {
        $this->north();
        $this->middle();
        $this->south();
    }

    public function north()
    {
        if ($this->_page->north !== 0) {
            echo $this->tag0('be-north');
            if (count($this->_page->northSections)) {
                foreach ($this->_page->northSections as $section) {
                    echo '<div class="be-section" id="' . $section->id . '">';
                    $section->template->pageTemplate = $this;
                    $section->template->display();
                    echo '</div>';
                }
            }
            echo $this->tag1('be-north');
        }
    }

    public function middle()
    {
        if ($this->_page->middle !== 0 || $this->_page->west !== 0 || $this->_page->east !== 0 || $this->_page->center !== 0) {

            echo $this->tag0('be-middle');
            if ($this->_page->middle !== 0) {
                if (count($this->_page->middleSections)) {
                    foreach ($this->_page->middleSections as $section) {
                        echo '<div class="be-section" id="' . $section->id . '">';

                        $section->template->pageTemplate = $this;

                        if ($section->key === 'be-page-title') {
                            $section->template->before();
                            $this->pageTitle();
                            $section->template->after();
                        } else if ($section->key === 'be-page-content') {
                            $section->template->before();
                            $this->pageContent();
                            $section->template->after();
                        } else {
                            $section->template->display();
                        }

                        echo '</div>';
                    }
                }
            } else {

                $spacingMobile = $this->_page->spacingMobile ?? '';
                $spacingTablet = $this->_page->spacingTablet ?? '';
                $spacingDesktop = $this->_page->spacingDesktop ?? '';

                echo '<div class="be-container">';

                echo '<style type="text/css">';
                echo '.middle-container{display: flex; justify-content: space-between;}';

                if ($spacingMobile !== '' || $spacingTablet !== '' || $spacingDesktop !== '') {
                    // 手机端
                    if ($spacingMobile !== '') {
                        echo '@media (max-width: 768px) {';
                        echo '.middle-container {';
                        echo 'margin: ' . $spacingMobile. ' 0;';
                        echo '}';
                        echo '}';
                    }

                    // 平析端
                    if ($spacingTablet !== '') {
                        echo '@media (min-width: 768px) {';
                        echo '.middle-container {';
                        echo 'margin: ' . $spacingTablet . ' 0;';
                        echo '}';
                        echo '}';
                    }

                    // 电脑端
                    if ($spacingDesktop !== '') {
                        echo '@media (min-width: 992px) {';
                        echo '.middle-container {';
                        echo 'margin: ' . $spacingDesktop . ' 0;';
                        echo '}';
                        echo '}';
                    }
                }

                echo '.west-container,.center-container,.east-container{flex:0 0 0%; overflow:hidden;}';
                echo '@media (max-width: 992px) {';
                echo '.west-container{display: none;}';
                echo '.center-container{flex-basis:100%;}';
                echo '.east-container{display: none;}';
                echo '}';

                $cols = 0;
                $totalWidth = 0;
                if ($this->_page->west !== 0) {
                    $totalWidth += abs($this->_page->west);
                    $cols++;
                }

                if ($this->_page->center !== 0) {
                    $totalWidth += abs($this->_page->center);
                    $cols++;
                }

                if ($this->_page->east !== 0) {
                    $totalWidth += abs($this->_page->east);
                    $cols++;
                }

                $calcStyle = '';
                if ($spacingDesktop !== '' && $cols > 1) {
                    $calcStyle = '(100% - ' . $spacingDesktop . ' * ' . ($cols - 1) . ')';
                }

                echo '@media (min-width: 992px) {';
                if ($this->_page->west !== 0) {
                    $widthRatio = (abs($this->_page->west) / $totalWidth);
                    if ($calcStyle !== '') {
                        $widthStyle = 'calc(' . $calcStyle . ' * ' . $widthRatio . ')';
                    } else {
                        $widthStyle = $widthRatio * 100 . '%';
                    }

                    echo '.west-container{flex-basis:'.$widthStyle.';}';
                }

                if ($this->_page->center !== 0) {
                    $widthRatio = (abs($this->_page->center) / $totalWidth);
                    if ($calcStyle !== '') {
                        $widthStyle = 'calc(' . $calcStyle . ' * ' . $widthRatio . ')';
                    } else {
                        $widthStyle = $widthRatio * 100 . '%';
                    }
                    echo '.center-container{flex-basis:'.$widthStyle.';}';
                }

                if ($this->_page->east !== 0) {
                    $widthRatio = (abs($this->_page->east) / $totalWidth);
                    if ($calcStyle !== '') {
                        $widthStyle = 'calc(' . $calcStyle . ' * ' . $widthRatio . ')';
                    } else {
                        $widthStyle = $widthRatio * 100 . '%';
                    }
                    echo '.east-container{flex-basis:'.$widthStyle.';}';
                }
                echo '}';
                echo '</style>';

                echo '<div class="middle-container">';
                if ($this->_page->west !== 0) {
                    echo '<div class="west-container">';
                    $this->west();
                    echo '</div>';
                }

                if ($this->_page->center !== 0) {
                    echo '<div class="center-container">';
                    $this->center();
                    echo '</div>';
                }

                if ($this->_page->east !== 0) {
                    echo '<div class="east-container">';
                    $this->east();
                    echo '</div>';
                }

                echo '</div>';
                echo '</div>';
            }
            echo $this->tag1('be-middle');

        }
    }

    public function west()
    {
        if ($this->_page->west !== 0) {
            echo $this->tag0('be-west');
            if (count($this->_page->westSections)) {
                foreach ($this->_page->westSections as $section) {
                    echo '<div class="be-section" id="' . $section->id . '">';
                    $section->template->pageTemplate = $this;
                    $section->template->display();
                    echo '</div>';
                }
            }
            echo $this->tag1('be-west');
        }
    }

    public function center()
    {
        if ($this->_page->center !== 0) {
            echo $this->tag0('be-center');
            if (count($this->_page->centerSections)) {
                foreach ($this->_page->centerSections as $section) {
                    echo '<div class="be-section" id="' . $section->id . '">';

                    $section->template->pageTemplate = $this;

                    if ($section->key === 'be-page-title') {
                        $section->template->before();
                        $this->pageTitle();
                        $section->template->after();
                    } else if ($section->key === 'be-page-content') {
                        $section->template->before();
                        $this->pageContent();
                        $section->template->after();
                    } else {
                        $section->template->display();
                    }

                    echo '</div>';
                }
            }
            echo $this->tag1('be-center');
        }
    }

    public function east()
    {
        if ($this->_page->east !== 0) {
            echo $this->tag0('be-east');
            if (count($this->_page->eastSections)) {
                foreach ($this->_page->eastSections as $section) {
                    echo '<div class="be-section" id="' . $section->id . '">';
                    $section->template->pageTemplate = $this;
                    $section->template->display();
                    echo '</div>';
                }
            }
            echo $this->tag1('be-east');
        }
    }

    public function south()
    {
        if ($this->_page->south !== 0) {
            echo $this->tag0('be-south');
            if (count($this->_page->southSections)) {
                foreach ($this->_page->southSections as $section) {
                    echo '<div class="be-section" id="' . $section->id . '">';
                    $section->template->pageTemplate = $this;
                    $section->template->display();
                    echo '</div>';
                }
            }
            echo $this->tag1('be-south');
        }
    }

    public function pageTitle() {
        echo $this->tag0('be-page-title');
        echo $this->title;
        echo $this->tag1('be-page-title');
    }

    public function pageContent() {
    }

    /**
     * 标签 - 封装内容
     *
     * @param string $tagName 标签名
     * @param string $content 要封装的内容
     * @return string
     */
    public function tag(string $tagName, string $content): string
    {
        if (isset($this->_tags[$tagName]) && is_array($this->_tags[$tagName]) && count($this->_tags[$tagName]) >= 2) {
            return $this->_tags[$tagName][0] . $content . $this->_tags[$tagName][1];
        }
        return $content;
    }

    /**
     * 标签 - 获取前半部分内容
     *
     * @param string $tagName 标签名
     * @return string
     */
    public function tag0(string $tagName): string
    {
        if (isset($this->_tags[$tagName]) && is_array($this->_tags[$tagName]) && count($this->_tags[$tagName]) >= 1) {
            return $this->_tags[$tagName][0];
        }
        return '';
    }

    /**
     * 标签 - 获取后半部分内容
     *
     * @param string $tagName 标签名
     * @return string
     */
    public function tag1(string $tagName): string
    {
        if (isset($this->_tags[$tagName]) && is_array($this->_tags[$tagName]) && count($this->_tags[$tagName]) >= 2) {
            return $this->_tags[$tagName][1];
        }
        return '';
    }
}
