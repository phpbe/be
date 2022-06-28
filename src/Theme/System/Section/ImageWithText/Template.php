<?php
namespace Be\Theme\System\Section\ImageWithText;

use Be\Theme\Section;

class Template extends Section
{
    protected array $position = ['Middle', 'Center'];

    public function display()
    {

        if ($this->config->enable) {

            echo '<style type="text/css">';

            echo '#image-with-text-' . $this->id . ' {';
            echo 'background-color: ' . $this->config->backgroundColor . ';';
            echo '}';

            // 手机端
            echo '@media (max-width: 768px) {';
            echo '#image-with-text-' . $this->id . ' {';
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
            echo '#image-with-text-' . $this->id . ' {';
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
            echo '#image-with-text-' . $this->id . ' {';
            if ($this->config->paddingTopDesktop) {
                echo 'padding-top: ' . $this->config->paddingTopDesktop . 'px;';
            }
            if ($this->config->paddingBottomDesktop) {
                echo 'padding-bottom: ' . $this->config->paddingBottomDesktop . 'px;';
            }
            echo '}';
            echo '}';

            echo '#image-with-text-' . $this->id . ' .image-with-text-content {';
            echo 'background-color: ' . $this->config->contentBackgroundColor . ';';
            echo '}';

            // 手机端 图像 和 内容 均 100%
            echo '@media only screen and (max-width: 768px) {';
            echo '#image-with-text-' . $this->id . ' {';
            echo '}';
            echo '#image-with-text-' . $this->id . ' .image-with-text-iamge {';
            echo 'width: 100%;';
            echo '}';
            echo '#image-with-text-' . $this->id . ' .image-with-text-content {';
            echo 'width: 100%;';
            echo '}';
            echo '#image-with-text-' . $this->id . ' .image-with-text-content-wrap {';
            echo 'padding: 25px 15px 30px;';
            echo '}';
            echo '}';

            // 电脑版，图像 50%, 内容 50%
            echo '@media only screen and (min-width: 769px) {';
            echo '#image-with-text-' . $this->id . ' .image-with-text-container {';
            echo 'display: flex;';
            if ($this->config->imagePosition === 'right') { // 图像居右
                echo 'flex-direction: row-reverse !important;';
            }
            echo '}';
            echo '#image-with-text-' . $this->id . ' .image-with-text-image {';
            echo 'flex: 0 0 50%;';
            echo '}';
            echo '#image-with-text-' . $this->id . ' .image-with-text-content {';
            echo 'flex: 0 0 50%;';
            echo 'position: relative;';
            echo '}';
            echo '#image-with-text-' . $this->id . ' .image-with-text-content-wrap {';
            echo 'position: absolute;';
            echo 'top: 50%;';
            echo 'transform: translateY(-50%);';
            echo 'width: 80%;';
            echo 'left: 10%;';
            echo '}';
            echo '}';

            echo '#image-with-text-' . $this->id . ' .image-with-text-image img {';
            echo 'width: 100%;';
            echo '}';

            echo '#image-with-text-' . $this->id . ' .image-with-text-image .no-image {';
            echo 'width: 100%;';
            echo 'height: 300px;';
            echo 'line-height: 300px;';
            echo 'color: #fff;';
            echo 'font-size: 24px;';
            echo 'text-align: center;';
            echo 'text-shadow:  5px 5px 5px #999;';
            echo 'background-color: rgba(35, 35, 35, 0.2);';
            echo '}';

            echo '#image-with-text-' . $this->id . ' .image-with-text-title {';
            echo 'text-align: center;';
            echo 'font-size: ' . $this->config->contentTitleFontSize . 'px;';
            echo 'color: ' . $this->config->contentTitleColor . ';';
            echo '}';

            echo '#image-with-text-' . $this->id . ' .image-with-text-description {';
            echo 'text-align: center;';
            echo 'font-size: ' . $this->config->contentDescriptionFontSize . 'px;';
            echo 'color: ' . $this->config->contentDescriptionColor . ';';
            echo 'margin-bottom: 35px;';
            echo '}';

            echo '#image-with-text-' . $this->id . ' .image-with-text-button {';
            echo 'text-align: center;';
            echo '}';

            echo '</style>';

            echo '<div id="image-with-text-' . $this->id . '">';

            if ($this->config->width === 'default') {
                echo '<div class="be-container">';
            }

            echo '<div class="image-with-text-container">';
            echo '<div class="image-with-text-image">';
            if (!$this->config->image) {
                echo '<div class="no-image">600X300px+</div>';
            } else {
                echo '<img src="' . $this->config->image . '">';
            }
            echo '</div>';

            echo '<div class="image-with-text-content">';
            echo '<div class="image-with-text-content-wrap">';
            echo '<h2 class="image-with-text-title">' . $this->config->contentTitle . '</h2>';
            echo '<div class="image-with-text-description">' . $this->config->contentDescription . '</div>';
            echo '<div class="image-with-text-button">';
            echo '<a href="' . $this->config->contentButtonLink . '" class="be-btn">' . $this->config->contentButton . '</a>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';

            if ($this->config->width === 'default') {
                echo '</div>';
            }
            echo '</div>';
        }
    }
}

