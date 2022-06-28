<?php

namespace Be\Theme\System\Section\Footer;

use Be\Theme\Section;

class Template extends Section
{
    protected array $position = ['South'];

    public function css()
    {
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

        echo '#' . $this->id . ' .footer-menu-lv1 {';
        echo 'font-size: 1.25rem;';
        echo '}';

        echo '#' . $this->id . ' .footer-menu-lv2 {';
        echo 'margin-top: 1rem';
        echo '}';

        echo '#' . $this->id . ' .footer-menu-lv2-item {';
        echo 'margin-top: 0.25rem';
        echo '}';

        echo '</style>';
    }


    public function display()
    {
        if ($this->config->enable) {

            $this->css();

            echo '<div id="' . $this->id . '">';
            echo '<div class="be-container">';
            echo '<div class="be-row">';

            //print_r($this->config->items);
            if (isset($this->config->items) && is_array($this->config->items) && count($this->config->items) > 0) {
                foreach ($this->config->items as $sectionDataItem) {
                    if (!$sectionDataItem['data']['enable']) {
                        continue;
                    }

                    echo '<div class="be-col-24 be-col-md-' . ($sectionDataItem['data']['cols'] * 6) . '">';
                    echo '<div class="be-p-100">';
                    switch ($sectionDataItem['name']) {
                        case 'Menu':
                            $menu = \Be\Be::getMenu('South');
                            $menuTree = $menu->getTree();

                            $i = 0;
                            echo '<div class="be-row">';
                            foreach ($menuTree as $item) {
                                if (!isset($item->subItems) || !is_array($item->subItems) || count($item->subItems) === 0) {
                                    continue;
                                }

                                echo '<div class="be-col-24 be-col-md-' . intval(24 / $sectionDataItem['data']['quantity']) . '">';
                                $url = 'javascript:void(0);';
                                if ($item->route) {
                                    $url = beUrl($item->route, $item->params);
                                } else {
                                    if ($item->url) {
                                        $url = $item->url;
                                    }
                                }

                                echo '<div class="footer-menu-lv1"><a class="link-hover" href="' . $url . '"';
                                if ($item->target === '_blank') {
                                    echo ' target="_blank"';
                                }
                                echo '>' . $item->label . '</a></div>';


                                echo '<div class="footer-menu-lv2">';
                                foreach ($item->subItems as $subItem) {
                                    $url = 'javascript:void(0);';
                                    if ($subItem->route) {
                                        $url = beUrl($subItem->route, $subItem->params);
                                    } else {
                                        if ($subItem->url) {
                                            $url = $subItem->url;
                                        }
                                    }

                                    echo '<div class="footer-menu-lv2-item"><a class="link-hover" href="' . $url . '"';
                                    if ($subItem->target === '_blank') {
                                        echo ' target="_blank"';
                                    }
                                    echo '>' . $subItem->label . '</a></div>';
                                }
                                echo '</div>';

                                echo '</div>';

                                $i++;
                                if ($i >= $sectionDataItem['data']['quantity']) {
                                    break;
                                }
                            }
                            echo '</div>';

                            break;
                        case 'RichText':
                            echo '<div class="be-fs-bold">' . $sectionDataItem['data']['title'] . '</div>';
                            echo '<div class="be-mt-100">' . $sectionDataItem['data']['content'] . '</div>';
                            break;
                        case 'Image':
                            echo '<div style="text-align:' . $sectionDataItem['data']['align'] . '">';
                            echo '<img src="' . $sectionDataItem['data']['image'] . '" style="width:' . $sectionDataItem['data']['width'] . '">';
                            echo '</div>';
                            break;
                        case 'Copyright':
                            echo '<div style="text-align:' . $sectionDataItem['data']['align'] . '">';
                            echo $sectionDataItem['data']['content'];
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
