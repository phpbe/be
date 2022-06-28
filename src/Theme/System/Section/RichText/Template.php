<?php
namespace Be\Theme\System\Section\RichText;

use Be\Theme\Section;

class Template extends Section
{
    protected array $position = ['*'];

    public function display()
    {
        if ($this->config->enable) {

            echo '<style type="text/css">';

            echo '#rich-text-' . $this->id . ' {';
            echo 'background-color: ' . $this->config->backgroundColor . ';';
            echo '}';

            // 手机端
            echo '@media (max-width: 768px) {';
            echo '#rich-text-' . $this->id . ' {';
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
            echo '#rich-text-' . $this->id . ' {';
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
            echo '#rich-text-' . $this->id . ' {';
            if ($this->config->paddingTopDesktop) {
                echo 'padding-top: ' . $this->config->paddingTopDesktop . 'px;';
            }
            if ($this->config->paddingBottomDesktop) {
                echo 'padding-bottom: ' . $this->config->paddingBottomDesktop . 'px;';
            }
            echo '}';
            echo '}';

            echo '</style>';

            echo '<div id="rich-text-' . $this->id . '">';
            echo $this->config->content;
            echo '</div>';
        }
    }
}

