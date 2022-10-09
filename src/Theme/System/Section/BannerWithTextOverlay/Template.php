<?php

namespace Be\Theme\System\Section\BannerWithTextOverlay;

use Be\Theme\Section;

class Template extends Section
{
    public array $positions = ['middle', 'center'];

    public function display()
    {
        if ($this->config->enable) {

            echo '<style type="text/css">';

            echo $this->getCssBackgroundColor('banner-with-text-overlay');
            echo $this->getCssPadding('banner-with-text-overlay');
            echo $this->getCssMargin('banner-with-text-overlay');;

            // 手机版，电脑版上传不同的图片
            echo '@media (max-width: 768px) {';
            echo '#' . $this->id . ' .banner-with-text-overlay-image {';
            echo 'display:none;';
            echo '}';
            echo '#' . $this->id . ' .banner-with-text-overlay-image-mobile {';
            echo 'display:block;';
            echo '}';
            echo '}';
            // 手机版，电脑版上传不同的图片
            echo '@media (min-width: 768px) {';
            echo '#' . $this->id . ' .banner-with-text-overlay-image {';
            echo 'display:block;';
            echo '}';
            echo '#' . $this->id . ' .banner-with-text-overlay-image-mobile {';
            echo 'display:none;';
            echo '}';
            echo '}';

            echo '#' . $this->id . ' .banner-with-text-overlay-image img {';
            echo 'width: 100%;';
            echo 'min-width: 1024px;';
            echo '}';

            echo '#' . $this->id . ' .banner-with-text-overlay-image-mobile img {';
            echo 'width: 100%;';
            echo '}';

            echo '#' . $this->id . ' .banner-with-text-overlay-image .no-image,';
            echo '#' . $this->id . ' .banner-with-text-overlay-image-mobile .no-image {';
            echo 'width: 100%;';
            echo 'height: 400px;';
            echo 'line-height: 400px;';
            echo 'color: #fff;';
            echo 'font-size: 24px;';
            echo 'text-align: center;';
            echo 'text-shadow:  5px 5px 5px #999;';
            echo 'background-color: rgba(35, 35, 35, 0.2);';
            echo '}';

            echo '#' . $this->id . ' .banner-with-text-overlay-container {';
            echo 'position: relative;';
            echo 'overflow: hidden;';
            echo '}';

            if ($this->position === 'middle' && $this->config->width === 'fullWidth') {
                echo '#' . $this->id . ' .banner-with-text-overlay-content-container {';
                echo 'position: absolute;';
                echo 'padding-left: 0.75rem;';
                echo 'padding-right: 0.75rem;';
                echo 'width: 100%;';
                echo 'z-index; 2;';
                echo 'top: 0;';
                echo 'bottom: 0;';
                echo '}';
                echo '@media (max-width: 768px) {';
                echo '#' . $this->id . ' .banner-with-text-overlay-content-container {';
                echo '}';
                echo '}';
                echo '@media (min-width: 768px) {';
                echo '#' . $this->id . ' .banner-with-text-overlay-content-container {';
                echo 'max-width: 720px;';
                echo 'left: calc((100% - 720px) / 2);';
                echo '}';
                echo '}';
                echo '@media (min-width: 992px) {';
                echo '#' . $this->id . ' .banner-with-text-overlay-content-container {';
                echo 'max-width: 960px;';
                echo 'left: calc((100% - 960px) / 2);';
                echo '}';
                echo '}';
                echo '@media (min-width: 1200px) {';
                echo '#' . $this->id . ' .banner-with-text-overlay-content-container {';
                echo 'max-width: 1140px;';
                echo 'left: calc((100% - 1140px) / 2);';
                echo '}';
                echo '}';
                echo '@media (min-width: 1400px) {';
                echo '#' . $this->id . ' .banner-with-text-overlay-content-container {';
                echo 'max-width: 1320px;';
                echo 'left: calc((100% - 1320px) / 2);';
                echo '}';
                echo '}';
            }

            echo '#' . $this->id . ' .banner-with-text-overlay-content {';
            echo 'position: absolute;';
            echo 'max-width: ' . $this->config->contentWidth . 'px;';
            echo '}';

            // 手机端 默认居中
            echo '@media only screen and (max-width: 768px) {';
            echo '#' . $this->id . ' .banner-with-text-overlay-content {';
            echo 'width: 80%;';
            echo 'left: 50%;';
            echo 'top: 50%;';
            echo 'transform: translate(-50%, -50%);';
            echo '}';
            echo '}';

            // 电脑端
            echo '@media only screen and (min-width: 769px) {';
            echo '#' . $this->id . ' .banner-with-text-overlay-content {';
            echo 'width: ' . $this->config->contentWidth . 'px;';
            if ($this->config->contentPosition === 'custom') {
                if ($this->config->contentPositionLeft >= 0) {
                    echo 'left: ' . $this->config->contentPositionLeft . 'px;';
                }
                if ($this->config->contentPositionRight >= 0) {
                    echo 'right: ' . $this->config->contentPositionRight . 'px;';
                }
                if ($this->config->contentPositionTop >= 0) {
                    echo 'top: ' . $this->config->contentPositionTop . 'px;';
                }
                if ($this->config->contentPositionBottom >= 0) {
                    echo 'bottom: ' . $this->config->contentPositionBottom . 'px;';
                }
            } else {
                echo 'top: 50%;';
                if ($this->config->contentPosition === 'left') {
                    echo 'transform: translateY(-50%);';
                    echo 'left: 5%;';
                } elseif ($this->config->contentPosition === 'center') {
                    echo 'left: 50%;';
                    echo 'transform: translate(-50%, -50%);';
                } elseif ($this->config->contentPosition === 'right') {
                    echo 'transform: translateY(-50%);';
                    echo 'right: 5%;';
                }
            }
            echo '}';
            echo '}';

            echo '#' . $this->id . ' .banner-with-text-overlay-title {';
            echo 'text-align: center;';
            echo 'font-size: ' . $this->config->contentTitleFontSize . 'px;';
            echo 'color: ' . $this->config->contentTitleColor . ';';
            echo '}';

            echo '#' . $this->id . ' .banner-with-text-overlay-description {';
            echo 'text-align: center;';
            echo 'font-size: ' . $this->config->contentDescriptionFontSize . 'px;';
            echo 'color: ' . $this->config->contentDescriptionColor . ';';
            echo 'margin-bottom: 35px;';
            echo '}';

            echo '#' . $this->id . ' .banner-with-text-overlay-button {';
            echo 'text-align: center;';
            echo '}';

            echo '#' . $this->id . ' .banner-with-text-overlay-button .be-btn {';
            echo 'background-color: transparent;';
            echo 'color: ' . $this->config->contentButtonColor . ';';
            echo 'border-color: ' . $this->config->contentButtonColor . ';';
            echo '}';

            echo '#' . $this->id . ' .banner-with-text-overlay-button .be-btn:hover {';
            echo 'background-color: ' . $this->config->contentButtonColor . ';';
            echo 'color: #333;';
            echo '}';

            echo '</style>';

            echo '<div class="banner-with-text-overlay">';
            if ($this->position === 'middle' && $this->config->width === 'default') {
                echo '<div class="be-container">';
            }
            echo '<div class="banner-with-text-overlay-container">';

            echo '<div class="banner-with-text-overlay-image">';
            if (!$this->config->image) {
                echo '<div class="no-image">1200X400px+</div>';
            } else {
                echo '<img src="' . $this->config->image . '">';
            }
            echo '</div>';
            echo '<div class="banner-with-text-overlay-image-mobile">';
            if (!$this->config->imageMobile) {
                echo '<div class="no-image">720X400px+</div>';
            } else {
                echo '<img src="' . $this->config->imageMobile . '">';
            }
            echo '</div>';

            if ($this->position === 'middle' && $this->config->width === 'fullWidth') {
                echo '<div class="banner-with-text-overlay-content-container">';
            }
            echo '<div class="banner-with-text-overlay-content">';
            echo '<h2 class="banner-with-text-overlay-title">' . $this->config->contentTitle . '</h2>';
            echo '<div class="banner-with-text-overlay-description">' . $this->config->contentDescription . '</div>';
            if ($this->config->contentButton !== '') {
                echo '<div class="banner-with-text-overlay-button">';
                echo '<a href="' . $this->config->contentButtonLink . '" class="be-btn be-btn-large">' . $this->config->contentButton . '</a>';
                echo '</div>';
            }
            echo '</div>';
            if ($this->position === 'middle' && $this->config->width === 'fullWidth') {
                echo '</div>';
            }

            echo '</div>';
            if ($this->position === 'middle' && $this->config->width === 'default') {
                echo '</div>';
            }
            echo '</div>';
        }
    }
}