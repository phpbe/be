<?php
namespace Be\Theme\System\Section\Banner;

use Be\Theme\Section;

class Template extends Section
{
    protected array $position = ['Middle', 'Center'];

    public function display()
    {
        if ($this->config->enable) {

            echo '<style type="text/css">';

            echo '#' . $this->id . ' {';
            echo 'background-color: ' . $this->config->backgroundColor . ';';
            echo '}';

            // 手机端
            echo '@media (max-width: 768px) {';
            echo '#' . $this->id . ' {';
            if ($this->config->paddingTopMobile) {
                echo 'padding-top: ' . $this->config->paddingTopMobile . 'px;';
            }
            if ($this->config->paddingBottomMobile) {
                echo 'padding-bottom: ' . $this->config->paddingBottomMobile . 'px;';
            }
            echo '}';
            echo '}';

            // 平析端
            echo '@media (min-width: 768px) {';
            echo '#' . $this->id . ' {';
            if ($this->config->paddingTopTablet) {
                echo 'padding-top: ' . $this->config->paddingTopTablet . 'px;';
            }
            if ($this->config->paddingBottomTablet) {
                echo 'padding-bottom: ' . $this->config->paddingBottomTablet . 'px;';
            }
            echo '}';
            echo '}';

            // 电脑端
            echo '@media (min-width: 992px) {';
            echo '#' . $this->id . ' {';
            if ($this->config->paddingTopDesktop) {
                echo 'padding-top: ' . $this->config->paddingTopDesktop . 'px;';
            }
            if ($this->config->paddingBottomDesktop) {
                echo 'padding-bottom: ' . $this->config->paddingBottomDesktop . 'px;';
            }
            echo '}';
            echo '}';

            // 手机版，电脑版上传不同的图片
            echo '@media (max-width: 768px) {';
            echo '#' . $this->id . ' .banner-image {';
            echo 'display:none;';
            echo '}';
            echo '#' . $this->id . ' .banner-mobile-image {';
            echo 'display:block;';
            echo '}';
            echo '}';
            // 手机版，电脑版上传不同的图片
            echo '@media (min-width: 768px) {';
            echo '#' . $this->id . ' .banner-image {';
            echo 'display:block;';
            echo '}';
            echo '#' . $this->id . ' .banner-mobile-image {';
            echo 'display:none;';
            echo '}';
            echo '}';

            echo '#' . $this->id . ' .banner-image img,';
            echo '#' . $this->id . ' .banner-mobile-image img {';
            echo 'width: 100%;';
            echo '}';

            echo '#' . $this->id . ' .banner-image .no-image,';
            echo '#' . $this->id . ' .banner-mobile-image .no-image {';
            echo 'width: 100%;';
            echo 'height: 400px;';
            echo 'line-height: 400px;';
            echo 'color: #fff;';
            echo 'font-size: 24px;';
            echo 'text-align: center;';
            echo 'text-shadow:  5px 5px 5px #999;';
            echo 'background-color: rgba(35, 35, 35, 0.2);';
            echo '}';

            echo '</style>';

            echo '<div id="' . $this->id . '">';
            if ($this->config->width === 'default') {
                echo '<div class="be-container">';
            }

            echo '<div class="banner-image">';
            if (!$this->config->image) {
                echo '<div class="banner-no-image">1200X400px+</div>';
            } else {
                if ($this->config->link) {
                    echo '<a href="' . $this->config->link . '">';
                }
                echo '<img src="' . $this->config->image . '">';
                if ($this->config->link) {
                    echo '</a>';
                }
            }
            echo '</div>';
            echo '<div class="banner-mobile-image">';
            if (!$this->config->imageMobile) {
                echo '<div class="banner-no-image">720X400px+</div>';
            } else {
                if ($this->config->link) {
                    echo '<a href="' . $this->config->link . '">';
                }
                echo '<img src="' . $this->config->imageMobile . '">';
                if ($this->config->link) {
                    echo '</a>';
                }
            }
            echo '</div>';

            if ($this->config->width === 'default') {
                echo '</div>';
            }
            echo '</div>';
        }
    }
}

