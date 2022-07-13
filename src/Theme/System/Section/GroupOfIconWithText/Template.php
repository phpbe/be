<?php
namespace Be\Theme\System\Section\GroupOfIconWithText;

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

            echo $this->getCssBackgroundColor('group-of-icon-with-text');
            echo $this->getCssPadding('group-of-icon-with-text');
            echo $this->getCssMargin('group-of-icon-with-text');

            $itemWidthMobile = '100%';
            $itemWidthTablet = $count > 1 ? '50%' : '50%';
            if ($count > 2) {
                $itemWidthDesktop = (100 / 3) . '%';
            } elseif ($count === 2) {
                $itemWidthDesktop = '50%';
            } else {
                $itemWidthDesktop = '100%';
            }

            echo $this->getCssSpacing('group-of-icon-with-text-items', 'group-of-icon-with-text-item', $itemWidthMobile, $itemWidthTablet, $itemWidthDesktop);


            echo '#' . $this->id . ' .group-of-icon-with-text-item-bg {';
            echo 'background-color: ' . $this->config->itemBackgroundColor . ';';
            echo 'padding: 1.5rem 1rem;';
            echo '}';


            echo '#' . $this->id . ' .group-of-icon-with-text-item-container {';
            echo 'display: flex;';
            echo 'align-items:center;';
            echo '}';

            echo '#' . $this->id . ' .group-of-icon-with-text-item-icon {';
            echo 'flex-grow: 1;';
            echo 'flex-shrink: 0;';
            echo 'flex-basis: 4rem;';
            echo 'text-align: center;';
            echo '}';


            echo '#' . $this->id . ' .group-of-icon-with-text-item-icon svg,';
            echo '#' . $this->id . ' .group-of-icon-with-text-item-icon img {';
            echo 'width: 2.5rem;';
            echo 'height: 2.5rem;';
            echo '}';

            echo '#' . $this->id . ' .group-of-icon-with-text-item-content {';
            echo 'flex-grow: 1;';
            echo 'flex-shrink: 1;';
            echo 'flex-basis: 60%;';
            echo 'overflow: hidden;';
            echo '}';

            echo '#' . $this->id . ' .group-of-icon-with-text-item-title {';
            echo 'color: ' . $this->config->itemTitleColor . ';';
            echo 'overflow: hidden;';
            echo 'text-overflow: ellipsis;';
            echo 'white-space: nowrap;';
            echo '}';

            echo '#' . $this->id . ' .group-of-icon-with-text-item-link {';
            echo 'margin-top: 0.5rem;';
            echo 'color: ' . $this->config->itemLinkColor . ';';
            echo '}';

            echo '#' . $this->id . ' .group-of-icon-with-text-item-link a {';
            echo 'color: ' . $this->config->itemLinkColor . ';';
            echo '}';

            echo '</style>';

            echo '<div class="group-of-icon-with-text">';

            if ($this->position === 'middle' && $this->config->width === 'default') {
                echo '<div class="be-container">';
            }

            if (isset($this->config->items) && is_array($this->config->items) && count($this->config->items) > 0) {
                echo '<div class="group-of-icon-with-text-items">';
                foreach ($this->config->items as $item) {
                    $itemConfig = $item['config'];
                    if ($itemConfig->enable) {
                        echo '<div class="group-of-icon-with-text-item">';
                        echo '<div class="group-of-icon-with-text-item-bg">';
                        switch ($item['name']) {
                            case 'IconWithText':
                                echo '<div class="group-of-icon-with-text-item-container">';

                                echo '<div class="group-of-icon-with-text-item-icon">';
                                switch ($itemConfig->iconType) {
                                    case 'svg':
                                        echo $itemConfig->iconSvg;
                                        break;
                                    case 'image':
                                        echo '<img src="' . $itemConfig->iconImage  . '" />';
                                        break;

                                }
                                echo '</div>';

                                echo '<div class="group-of-icon-with-text-item-content">';
                                if ($itemConfig->title) {
                                    echo '<div class="group-of-icon-with-text-item-title">';
                                    echo $itemConfig->title;
                                    echo '</div>';
                                }
                                if ($itemConfig->linkText) {
                                    echo '<div class="group-of-icon-with-text-item-link">';
                                    if ($itemConfig->linkUrl) {
                                        echo '<a href="' . $itemConfig->linkUrl . '">';
                                    }
                                    echo $itemConfig->linkText;
                                    if ($itemConfig->linkUrl) {
                                        echo '</a>';
                                    }
                                    echo '</div>';
                                }
                                echo '</div>';

                                echo '</div>';

                                break;
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                }
                echo '</div>';
            }

            if ($this->position === 'middle' && $this->config->width === 'default') {
                echo '</div>';
            }

            echo '</div>';
        }
    }
}


