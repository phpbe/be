<?php

namespace Be\Template;

use Be\Be;

/**
 * 模板基类
 */
class Driver
{
    public string $title = ''; // 标题（<head>中）
    public string $metaKeywords = ''; // meta keywords
    public string $metaDescription = '';  // meta description

    public ?string $pageTitle = null; // 页面标题，未设置时取 title，

    public array $_tags = []; // 可用的标签

    /**
     * @var object
     */
    public object $pageConfig; // 页面配置信息对象

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
        <title><?php echo $this->title ?? ''; ?></title>
        <meta name="keywords" content="<?php echo $this->metaKeywords ?? ''; ?>">
        <meta name="description" content="<?php echo $this->metaDescription ?? ''; ?>">
        <meta name="applicable-device" content="pc,mobile">
        <base href="<?php echo beUrl(); ?>/">
        <link rel="icon" href="favicon.ico" type="image/x-icon"/>
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
        <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
        <link rel="stylesheet" href="https://cdn.phpbe.com/ui/be.css"/>
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
        if ($this->pageConfig->north !== 0) {
            echo $this->tag0('be-north');
            if (count($this->pageConfig->northSections)) {
                foreach ($this->pageConfig->northSections as $section) {
                    echo '<div class="be-section" id="' . $section->id . '">';
                    $section->template->page = $this;
                    $section->template->display();
                    echo '</div>';
                }
            }
            echo $this->tag1('be-north');
        }
    }

    public function middle()
    {
        if ($this->pageConfig->middle !== 0 || $this->pageConfig->west !== 0 || $this->pageConfig->east !== 0 || $this->pageConfig->center !== 0) {

            echo $this->tag0('be-middle');
            if ($this->pageConfig->middle !== 0) {
                if (count($this->pageConfig->middleSections)) {
                    foreach ($this->pageConfig->middleSections as $section) {
                        echo '<div class="be-section" id="' . $section->id . '">';
                        $section->template->page = $this;
                        $section->template->display();
                        echo '</div>';
                    }
                }
            } else {

                $spacingMobile = $this->pageConfig->spacingMobile ?? '';
                $spacingTablet = $this->pageConfig->spacingTablet ?? '';
                $spacingDesktop = $this->pageConfig->spacingDesktop ?? '';

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

                echo '.west-container,.center-container,.east-container{flex:0 0 0%;min-width:0;}';
                echo '@media (max-width: 992px) {';
                echo '.west-container{display: none;}';
                echo '.center-container{flex-basis:100%;}';
                echo '.east-container{display: none;}';
                echo '}';

                $cols = 0;
                $totalWidth = 0;
                if ($this->pageConfig->west !== 0) {
                    $totalWidth += abs($this->pageConfig->west);
                    $cols++;
                }

                if ($this->pageConfig->center !== 0) {
                    $totalWidth += abs($this->pageConfig->center);
                    $cols++;
                }

                if ($this->pageConfig->east !== 0) {
                    $totalWidth += abs($this->pageConfig->east);
                    $cols++;
                }

                $calcStyle = '';
                if ($spacingDesktop !== '' && $cols > 1) {
                    $calcStyle = '(100% - ' . $spacingDesktop . ' * ' . ($cols - 1) . ')';
                }

                echo '@media (min-width: 992px) {';
                if ($this->pageConfig->west !== 0) {
                    $widthRatio = (abs($this->pageConfig->west) / $totalWidth);
                    if ($calcStyle !== '') {
                        $widthStyle = 'calc(' . $calcStyle . ' * ' . $widthRatio . ')';
                    } else {
                        $widthStyle = $widthRatio * 100 . '%';
                    }

                    echo '.west-container{flex-basis:'.$widthStyle.';}';
                }

                if ($this->pageConfig->center !== 0) {
                    $widthRatio = (abs($this->pageConfig->center) / $totalWidth);
                    if ($calcStyle !== '') {
                        $widthStyle = 'calc(' . $calcStyle . ' * ' . $widthRatio . ')';
                    } else {
                        $widthStyle = $widthRatio * 100 . '%';
                    }
                    echo '.center-container{flex-basis:'.$widthStyle.';}';
                }

                if ($this->pageConfig->east !== 0) {
                    $widthRatio = (abs($this->pageConfig->east) / $totalWidth);
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
                if ($this->pageConfig->west !== 0) {
                    echo '<div class="west-container">';
                    $this->west();
                    echo '</div>';
                }

                if ($this->pageConfig->center !== 0) {
                    echo '<div class="center-container">';
                    $this->center();
                    echo '</div>';
                }

                if ($this->pageConfig->east !== 0) {
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
        if ($this->pageConfig->west !== 0) {
            echo $this->tag0('be-west');
            if (count($this->pageConfig->westSections)) {
                foreach ($this->pageConfig->westSections as $section) {
                    echo '<div class="be-section" id="' . $section->id . '">';
                    $section->template->page = $this;
                    $section->template->display();
                    echo '</div>';
                }
            }
            echo $this->tag1('be-west');
        }
    }

    public function center()
    {
        if ($this->pageConfig->center !== 0) {
            echo $this->tag0('be-center');
            if (count($this->pageConfig->centerSections)) {
                foreach ($this->pageConfig->centerSections as $section) {
                    echo '<div class="be-section" id="' . $section->id . '">';
                    $section->template->page = $this;
                    $section->template->display();
                    echo '</div>';
                }
            }
            echo $this->tag1('be-center');
        }
    }

    public function east()
    {
        if ($this->pageConfig->east !== 0) {
            echo $this->tag0('be-east');
            if (count($this->pageConfig->eastSections)) {
                foreach ($this->pageConfig->eastSections as $section) {
                    echo '<div class="be-section" id="' . $section->id . '">';
                    $section->template->page = $this;
                    $section->template->display();
                    echo '</div>';
                }
            }
            echo $this->tag1('be-east');
        }
    }

    public function south()
    {
        if ($this->pageConfig->south !== 0) {
            echo $this->tag0('be-south');
            if (count($this->pageConfig->southSections)) {
                foreach ($this->pageConfig->southSections as $section) {
                    echo '<div class="be-section" id="' . $section->id . '">';
                    $section->template->page = $this;
                    $section->template->display();
                    echo '</div>';
                }
            }
            echo $this->tag1('be-south');
        }
    }

    public function pageTitle() {
        echo $this->tag0('be-page-title');
        echo $this->pageTitle === null ? $this->title : $this->pageTitle;
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
