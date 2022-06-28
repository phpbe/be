<?php
namespace Be\Theme\System\Section\GroupOfIconWithText;

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

            echo '#' . $this->id . ' .group-of-icon-with-text-items {';
            echo 'display: flex;';
            echo 'flex-wrap: wrap;';
            echo 'justify-content: space-between;';
            echo '}';

            // 手机端
            echo '@media (max-width: 768px) {';
            echo '#' . $this->id . ' .group-of-icon-with-text-items {';
            if ($this->config->spacingMobile) {
                echo 'margin-bottom: -' . $this->config->spacingMobile . 'px;';
                echo 'overflow: hidden;';
            }
            echo '}';
            echo '}';

            // 平析端
            echo '@media (min-width: 768px) {';
            echo '#' . $this->id . ' .group-of-icon-with-text-items {';
            if ($this->config->spacingTablet) {
                echo 'margin-bottom: -' . $this->config->spacingTablet . 'px;';
                echo 'overflow: hidden;';
            }
            echo '}';
            echo '}';

            // 电脑端
            echo '@media (min-width: 992px) {';
            echo '#' . $this->id . ' .group-of-icon-with-text-items {';
            if ($this->config->spacingDesktop) {
                echo 'margin-bottom: -' . $this->config->spacingDesktop . 'px;';
                echo 'overflow: hidden;';
            }
            echo '}';
            echo '}';

            $counter = 0;
            foreach ($this->config->items as $item) {
                if ($item['data']['enable']) {
                    $counter++;
                }
            }
            $cols = $counter > 4 ? 4 : $counter;

            echo '#' . $this->id . ' .group-of-icon-with-text-item {';
            echo 'background-color: ' . $this->config->itemBackgroundColor . ';';
            echo 'height: 86px;';
            echo 'line-height: 86px;';
            echo 'text-align: center;';
            echo 'flex: 0 1 auto;';
            echo 'overflow: hidden;';
            echo '}';

            // 手机端
            echo '@media (max-width: 768px) {';
            echo '#' . $this->id . ' .group-of-icon-with-text-item {';
            $width = $cols === 1 ? '100%;' : ('calc((100% - ' . $this->config->spacingMobile . 'px)/2)');
            echo 'width: ' . $width . ';';
            if ($this->config->spacingMobile) {
                echo 'margin-bottom: ' . $this->config->spacingMobile . 'px;';
            }
            echo '}';
            echo '}';

            // 手机端小于 512px 时, 100% 宽度
            echo '@media (max-width: 512px) {';
            echo '#' . $this->id . ' .group-of-icon-with-text-item {';
            echo 'width: 100% !important;';
            echo '}';
            echo '}';

            // 平析端
            echo '@media (min-width: 768px) {';
            echo '#' . $this->id . ' .group-of-icon-with-text-item {';
            $width = $cols === 1 ? '100%;' : ('calc((100% - ' . ($this->config->spacingTablet * 1) . 'px)/2)');
            echo 'width: ' . $width . ';';
            if ($this->config->spacingTablet) {
                echo 'margin-bottom: ' . $this->config->spacingTablet . 'px;';
            }
            echo '}';
            echo '}';

            // 电脑端
            echo '@media (min-width: 992px) {';
            echo '#' . $this->id . ' .group-of-icon-with-text-item {';
            $width = $cols === 1 ? '100%;' : ('calc((100% - ' . ($this->config->spacingDesktop * ($cols - 1)) . 'px)/' . $cols . ')');
            echo 'width: ' . $width . ';';
            if ($this->config->spacingDesktop) {
                echo 'margin-bottom: ' . $this->config->spacingDesktop . 'px;';
            }
            echo '}';
            echo '}';


            echo '#' . $this->id . ' .group-of-icon-with-text-item-container {';
            echo 'display: inline-block;';
            echo '}';

            echo '#' . $this->id . ' .group-of-icon-with-text-item-icon {';
            echo 'display: inline-block;';
            echo 'vertical-align: middle;';
            echo 'margin-right: 14px;';
            echo '}';

            echo '#' . $this->id . ' .group-of-icon-with-text-item-icon i {';
            echo 'font-size: 30px;';
            echo '}';

            echo '#' . $this->id . ' .group-of-icon-with-text-item-icon svg,';
            echo '#' . $this->id . ' .group-of-icon-with-text-item-icon img {';
            echo 'width: 30px;';
            echo 'height: 30px;';
            echo '}';

            echo '#' . $this->id . ' .group-of-icon-with-text-item-content {';
            echo 'display: inline-block;';
            echo 'text-align: left;';
            echo 'vertical-align: middle;';
            echo '}';

            echo '#' . $this->id . ' .group-of-icon-with-text-item-title {';
            echo 'font-size: 14px;';
            echo 'line-height: 16px;';
            echo '}';

            echo '#' . $this->id . ' .group-of-icon-with-text-item-link {';
            echo 'font-size: 14px;';
            echo 'line-height: 16px;';
            echo 'margin-top: 4px;';
            echo 'color: #666666;';
            echo '}';

            echo '#' . $this->id . ' .group-of-icon-with-text-item-link a {';
            echo 'color: #666666;';
            echo '}';

            echo '</style>';

            echo '<div id="' . $this->id . '">';
            echo '<div class="be-container">';
            if (isset($this->config->items) && is_array($this->config->items) && count($this->config->items) > 0) {
                echo '<div class="group-of-icon-with-text-items">';
                foreach ($this->config->items as $item) {
                    if ($item['data']['enable']) {
                        echo '<div class="group-of-icon-with-text-item">';
                        switch ($item['name']) {
                            case 'IconWithText':
                                echo '<div class="group-of-icon-with-text-item-container">';

                                echo '<div class="group-of-icon-with-text-item-icon">';
                                switch ($item['data']['icon']) {
                                    case 'name':
                                        echo '<i class="' . $item['data']['iconName'] . '"></i>';
                                        break;
                                    case 'svg':
                                        echo $item['data']['iconSvg'];
                                        break;
                                    case 'image':
                                        echo '<img src="' . $item['data']['iconImage']  . '" />';
                                        break;

                                }
                                echo '</div>';

                                echo '<div class="group-of-icon-with-text-item-content">';
                                if ($item['data']['title']) {
                                    echo '<div class="group-of-icon-with-text-item-title">';
                                    echo $item['data']['title'];
                                    echo '</div>';
                                }
                                if ($item['data']['linkText']) {
                                    echo '<div class="group-of-icon-with-text-item-link">';
                                    if ($item['data']['linkUrl']) {
                                        echo '<a href="' . $item['data']['linkUrl'] . '">';
                                    }
                                    echo $item['data']['linkText'];
                                    if ($item['data']['linkUrl']) {
                                        echo '</a>';
                                    }
                                    echo '</div>';
                                }
                                echo '</div>';

                                echo '</div>';

                                break;
                        }
                        echo '</div>';
                    }
                }
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
        }
    }
}


