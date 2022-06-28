<?php
namespace Be\Theme\System\Section\Images;

use Be\Theme\Section;

class Template extends Section
{
    protected array $position = ['Middle', 'Center'];

    public function display()
    {

        if ($this->config->enable) {

            echo '<style type="text/css">';

            echo '#images-' . $this->id . ' {';
            echo 'background-color: ' . $this->config->backgroundColor . ';';
            echo '}';

            // 手机端
            echo '@media (max-width: 768px) {';
            echo '#images-' . $this->id . ' {';
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
            echo '#images-' . $this->id . ' {';
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
            echo '#images-' . $this->id . ' {';
            if ($this->config->paddingTopDesktop) {
                echo 'padding-top: ' . $this->config->paddingTopDesktop . 'px;';
            }
            if ($this->config->paddingBottomDesktop) {
                echo 'padding-bottom: ' . $this->config->paddingBottomDesktop . 'px;';
            }
            echo '}';
            echo '}';

            echo '#images-' . $this->id . ' img,';
            echo '#images-' . $this->id . ' a img {';
            echo 'width: 100%;';
            echo 'transition: all 0.7s ease;';
            echo '}';

            if ($this->config->hoverEffect !== 'none') {
                switch ($this->config->hoverEffect) {
                    case 'scale':
                        echo '#images-' . $this->id . ' a:hover img {';
                        echo 'transform: scale(1.1);';
                        echo '}';
                        break;
                    case 'rotateScale':
                        echo '#images-' . $this->id . ' a:hover img {';
                        echo 'transform: rotate(3deg) scale(1.1);';
                        echo '}';
                        break;
                }
            }

            echo '#images-' . $this->id . ' .images-items {';
            echo 'display: flex;';
            echo 'flex-wrap: wrap;';
            echo 'justify-content: space-between;';
            echo '}';

            // 手机端
            if ($this->config->spacingMobile) {
                echo '@media (max-width: 768px) {';
                echo '#images-' . $this->id . ' .images-items {';
                echo 'margin-bottom: -' . $this->config->spacingMobile . 'px;';
                echo 'overflow: hidden;';
                echo '}';
                echo '}';
            }

            // 平析端
            if ($this->config->spacingTablet) {
                echo '@media (min-width: 768px) {';
                echo '#images-' . $this->id . ' .images-items {';
                echo 'margin-bottom: -' . $this->config->spacingTablet . 'px;';
                echo 'overflow: hidden;';
                echo '}';
                echo '}';
            }

            // 电脑端
            if ($this->config->spacingDesktop) {
                echo '@media (min-width: 992px) {';
                echo '#images-' . $this->id . ' .images-items {';
                echo 'margin-bottom: -' . $this->config->spacingDesktop . 'px;';
                echo 'overflow: hidden;';
                echo '}';
                echo '}';
            }

            $counter = 0;
            foreach ($this->config->items as $item) {
                if ($item['data']['enable']) {
                    $counter++;
                }
            }
            $cols = $counter > 3 ? 3 : $counter;

            echo '#images-' . $this->id . ' .images-item {';
            echo 'flex: 0 1 auto;';
            echo 'overflow: hidden;';
            echo '}';

            // 手机端
            echo '@media (max-width: 768px) {';
            echo '#images-' . $this->id . ' .images-item {';
            $width = $cols === 1 ? '100%;' : ('calc((100% - ' . $this->config->spacingMobile . 'px)/2)');
            echo 'width: ' . $width . ';';
            if ($this->config->spacingMobile) {
                echo 'margin-bottom: ' . $this->config->spacingMobile . 'px;';
            }
            echo '}';
            echo '}';

            // 手机端小于 512px 时, 100% 宽度
            echo '@media (max-width: 512px) {';
            echo '#images-' . $this->id . ' .images-item {';
            echo 'width: 100% !important;';
            echo '}';
            echo '}';

            // 平析端
            echo '@media (min-width: 768px) {';
            echo '#images-' . $this->id . ' .images-item {';
            $width = $cols === 1 ? '100%;' : ('calc((100% - ' . ($this->config->spacingTablet * ($cols - 1)) . 'px)/' . $cols . ')');
            echo 'width: ' . $width . ';';
            if ($this->config->spacingTablet) {
                echo 'margin-bottom: ' . $this->config->spacingTablet . 'px;';
            }
            echo '}';
            echo '}';

            // 电脑端
            echo '@media (min-width: 992px) {';
            echo '#images-' . $this->id . ' .images-item {';
            $width = $cols === 1 ? '100%;' : ('calc((100% - ' . ($this->config->spacingDesktop * ($cols - 1)) . 'px)/' . $cols . ')');
            echo 'width: ' . $width . ';';
            if ($this->config->spacingDesktop) {
                echo 'margin-bottom: ' . $this->config->spacingDesktop . 'px;';
            }
            echo '}';
            echo '}';


            echo '#images-' . $this->id . ' .images-item .no-image {';
            echo 'width: 100%;';
            echo 'height: 200px;';
            echo 'line-height: 200px;';
            echo 'color: #fff;';
            echo 'font-size: 24px;';
            echo 'text-align: center;';
            echo 'text-shadow:  5px 5px 5px #999;';
            echo 'background-color: rgba(35, 35, 35, 0.2);';
            echo '}';


            echo '</style>';

            echo '<div id="images-' . $this->id . '">';
            echo '<div class="be-container">';
            if (isset($this->config->items) && is_array($this->config->items) && count($this->config->items) > 0) {
                echo '<div class="images-items">';
                foreach ($this->config->items as $item) {
                    if ($item['data']['enable']) {
                        echo '<div class="images-item">';
                        switch ($item['name']) {
                            case 'Image':
                                if (!$item['data']['image']) {
                                    echo '<div class="no-image">400X200px+</div>';
                                } else {
                                    if ($item['data']['link']) {
                                        echo '<a href="' . $item['data']['link'] . '">';
                                    }
                                    echo '<img src="' . $item['data']['image'] . '" />';

                                    if ($item['data']['link']) {
                                        echo '</a>';
                                    }
                                }
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

