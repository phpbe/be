<?php

namespace Be\Theme\System\Section\Footer;

use Be\Theme\Section;

class Template extends Section
{
    public array $positions = ['south'];

    public function css()
    {
        echo '<style type="text/css">';
        echo $this->getCssPadding('footer');
        echo $this->getCssMargin('footer');

        echo '#' . $this->id . ' .footer {';
        echo 'background-color: var(--minor-color);';
        echo 'color: ' . $this->config->fontColor . ';';
        echo '}';

        echo '#' . $this->id . ' .footer a {';
        echo 'color: ' . $this->config->linkColor . ';';
        echo '}';

        echo '#' . $this->id . ' .footer a:hover {';
        echo 'color: ' . $this->config->linkHoverColor . ';';
        echo '}';

        echo '#' . $this->id . ' .footer-menu-lv1 {';
        echo 'font-size: 1.25rem;';
        echo '}';

        echo '#' . $this->id . ' .footer-menu-lv2 {';
        echo 'margin-top: 1rem';
        echo '}';

        echo '#' . $this->id . ' .footer-menu-lv2-item {';
        echo 'margin-top: 0.25rem';
        echo '}';


        echo '#' . $this->id . ' .footer .link-hover:before {';
        echo 'background-color: #b7ecec;';
        echo '}';

        echo '#' . $this->id . ' .footer-copyright {';
        echo 'margin-top: 2rem;';
        echo 'padding-top: 1rem;';
        echo 'border-top: 1px solid #41566d;';
        echo 'font-size: .9rem;';
        echo '}';


        echo '</style>';
    }


    public function display()
    {
        if ($this->config->enable) {

            $this->css();

            echo '<div class="footer">';
            echo '<div class="be-container">';
            echo '<div class="be-row">';

            if (isset($this->config->items) && is_array($this->config->items) && count($this->config->items) > 0) {
                foreach ($this->config->items as $item) {
                    $itemConfig = $item['config'];
                    if (!$itemConfig->enable) {
                        continue;
                    }

                    echo '<div class="be-col-24 be-md-col-' . ($itemConfig->cols * 6) . '">';
                    echo '<div class="be-p-100">';
                    switch ($item['name']) {
                        case 'Menu':
                            $menu = \Be\Be::getMenu('South');
                            $menuTree = $menu->getTree();

                            $i = 0;
                            echo '<div class="be-row">';
                            foreach ($menuTree as $menuItem) {
                                if (!isset($menuItem->subItems) || !is_array($menuItem->subItems) || count($menuItem->subItems) === 0) {
                                    continue;
                                }

                                echo '<div class="be-col-24 be-md-col-' . intval(24 / $itemConfig->quantity) . '">';
                                $url = 'javascript:void(0);';
                                if ($menuItem->route) {
                                    $url = beUrl($menuItem->route, $menuItem->params);
                                } else {
                                    if ($menuItem->url) {
                                        $url = $menuItem->url;
                                    }
                                }

                                echo '<div class="footer-menu-lv1"><a class="link-hover" href="' . $url . '"';
                                if ($menuItem->target === '_blank') {
                                    echo ' target="_blank"';
                                }
                                echo '>' . $menuItem->label . '</a></div>';


                                echo '<div class="footer-menu-lv2">';
                                foreach ($menuItem->subItems as $subMenuItem) {
                                    $url = 'javascript:void(0);';
                                    if ($subMenuItem->route) {
                                        $url = beUrl($subMenuItem->route, $subMenuItem->params);
                                    } else {
                                        if ($subMenuItem->url) {
                                            $url = $subMenuItem->url;
                                        }
                                    }

                                    echo '<div class="footer-menu-lv2-item"><a class="link-hover" href="' . $url . '"';
                                    if ($subMenuItem->target === '_blank') {
                                        echo ' target="_blank"';
                                    }
                                    echo '>' . $subMenuItem->label . '</a></div>';
                                }
                                echo '</div>';

                                echo '</div>';

                                $i++;
                                if ($i >= $itemConfig->quantity) {
                                    break;
                                }
                            }
                            echo '</div>';

                            break;
                        case 'RichText':
                            echo '<div class="be-fs-bold">' . $itemConfig->title . '</div>';
                            echo '<div class="be-mt-100">' . $itemConfig->content . '</div>';
                            break;
                        case 'Image':
                            echo '<div style="text-align:' . $itemConfig->align . '">';
                            echo '<img src="' . $itemConfig->image . '" style="width:' . $itemConfig->width . '">';
                            echo '</div>';
                            break;
                        case 'Copyright':
                            echo '<div style="text-align:' . $itemConfig->align . '">';
                            echo $itemConfig->content;
                            echo '</div>';
                            break;
                    }
                    echo '</div>';
                    echo '</div>';
                }
            }

            echo '</div>';
            echo '</div>';
            echo '</div>';

        }
    }
}
