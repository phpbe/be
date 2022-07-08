<?php
namespace Be\Theme\System\Section\Images;

use Be\Theme\Section;

class Template extends Section
{
    public array $positions = ['middle', 'center'];

    public function display()
    {
        if ($this->config->enable) {
            $count = 0;
            foreach ($this->config->items as $item) {
                if ($item['config']->enable) {
                    $count++;
                }
            }

            if ($count === 0) {
                return;
            }

            echo '<style type="text/css">';

            echo $this->getCssBackgroundColor('images');
            echo $this->getCssPadding('images');

            $itemWidthMobile = '100%';
            $itemWidthTablet = $count > 1 ? '50%' : '50%';
            if ($count > 2) {
                $itemWidthDesktop = (100 / 3) . '%';
            } elseif ($count === 2) {
                $itemWidthDesktop = '50%';
            } else {
                $itemWidthDesktop = '100%';
            }

            echo $this->getCssSpacing('images-items', 'images-item', $itemWidthMobile, $itemWidthTablet, $itemWidthDesktop);


            echo '#' . $this->id . ' img,';
            echo '#' . $this->id . ' a img {';
            echo 'width: 100%;';
            echo 'transition: all 0.7s ease;';
            echo '}';

            if ($this->config->hoverEffect !== 'none') {
                switch ($this->config->hoverEffect) {
                    case 'scale':
                        echo '#' . $this->id . ' a:hover img {';
                        echo 'transform: scale(1.1);';
                        echo '}';
                        break;
                    case 'rotateScale':
                        echo '#' . $this->id . ' a:hover img {';
                        echo 'transform: rotate(3deg) scale(1.1);';
                        echo '}';
                        break;
                }
            }

            echo '#' . $this->id . ' .images-item .no-image {';
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

            echo '<div class="images">';
            echo '<div class="be-container">';
            if (isset($this->config->items) && is_array($this->config->items) && count($this->config->items) > 0) {
                echo '<div class="images-items">';
                foreach ($this->config->items as $item) {
                    $itemConfig = $item['config'];
                    if ($itemConfig->enable) {
                        echo '<div class="images-item">';
                        switch ($item['name']) {
                            case 'Image':
                                if (!$itemConfig->image) {
                                    echo '<div class="no-image">400X200px+</div>';
                                } else {
                                    if ($itemConfig->link) {
                                        echo '<a href="' . $itemConfig->link . '">';
                                    }
                                    echo '<img src="' . $itemConfig->image . '" />';

                                    if ($itemConfig->link) {
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

