<?php
namespace Be\Theme\System\Section\ImageWithText;

use Be\Theme\Section;

class Template extends Section
{
    public array $positions = ['middle', 'center'];

    public function display()
    {

        if ($this->config->enable) {

            echo '<style type="text/css">';

            echo $this->getCssBackgroundColor('image-with-text');
            echo $this->getCssPadding('image-with-text');

            echo '#' . $this->id . ' .image-with-text-content {';
            echo 'background-color: ' . $this->config->contentBackgroundColor . ';';
            echo '}';

            // 手机端 图像 和 内容 均 100%
            echo '@media only screen and (max-width: 768px) {';
            echo '#' . $this->id . ' image-with-text {';
            echo '}';
            echo '#' . $this->id . ' .image-with-text-iamge {';
            echo 'width: 100%;';
            echo '}';
            echo '#' . $this->id . ' .image-with-text-content {';
            echo 'width: 100%;';
            echo '}';
            echo '#' . $this->id . ' .image-with-text-content-wrap {';
            echo 'padding: 25px 15px 30px;';
            echo '}';
            echo '}';

            // 电脑版，图像 50%, 内容 50%
            echo '@media only screen and (min-width: 769px) {';
            echo '#' . $this->id . ' .image-with-text-container {';
            echo 'display: flex;';
            if ($this->config->imagePosition === 'right') { // 图像居右
                echo 'flex-direction: row-reverse !important;';
            }
            echo '}';
            echo '#' . $this->id . ' .image-with-text-image {';
            echo 'flex: 0 0 50%;';
            echo '}';
            echo '#' . $this->id . ' .image-with-text-content {';
            echo 'flex: 0 0 50%;';
            echo 'position: relative;';
            echo '}';
            echo '#' . $this->id . ' .image-with-text-content-wrap {';
            echo 'position: absolute;';
            echo 'top: 50%;';
            echo 'transform: translateY(-50%);';
            echo 'width: 80%;';
            echo 'left: 10%;';
            echo '}';
            echo '}';

            echo '#' . $this->id . ' .image-with-text-image img {';
            echo 'width: 100%;';
            echo '}';

            echo '#' . $this->id . ' .image-with-text-image .no-image {';
            echo 'width: 100%;';
            echo 'height: 300px;';
            echo 'line-height: 300px;';
            echo 'color: #fff;';
            echo 'font-size: 24px;';
            echo 'text-align: center;';
            echo 'text-shadow:  5px 5px 5px #999;';
            echo 'background-color: rgba(35, 35, 35, 0.2);';
            echo '}';

            echo '#' . $this->id . ' .image-with-text-title {';
            echo 'text-align: center;';
            echo 'font-size: ' . $this->config->contentTitleFontSize . 'px;';
            echo 'color: ' . $this->config->contentTitleColor . ';';
            echo '}';

            echo '#' . $this->id . ' .image-with-text-description {';
            echo 'text-align: center;';
            echo 'font-size: ' . $this->config->contentDescriptionFontSize . 'px;';
            echo 'color: ' . $this->config->contentDescriptionColor . ';';
            echo 'margin-bottom: 35px;';
            echo '}';

            echo '#' . $this->id . ' .image-with-text-button {';
            echo 'text-align: center;';
            echo '}';

            echo '</style>';

            echo '<div class="image-with-text">';

            if ($this->position === 'middle' && $this->config->width === 'default') {
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

            if ($this->position === 'middle' && $this->config->width === 'default') {
                echo '</div>';
            }
            echo '</div>';
        }
    }
}

