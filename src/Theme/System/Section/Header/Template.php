<?php

namespace Be\Theme\System\Section\Header;

use Be\Theme\Section;

class Template extends Section
{
    public array $positions = ['north'];

    private function css()
    {
        $configTheme = \Be\Be::getConfig('Theme.System.Theme');
        echo '<style type="text/css">';

        echo '#' . $this->id . ' .header-mobile,';
        echo '#' . $this->id . ' .header-desktop {';
        echo 'background-color: ' . $this->config->backgroundColor . ';';
        //echo 'border-bottom: #ddd 1px solid;';
        echo 'box-shadow: 0 2px 4px rgb(33 51 67 / 12%);';
        echo '}';

        echo '#' . $this->id . ' .header-icon {';
        echo 'display: inline-block;';
        echo 'border: none;';
        echo 'background-repeat: no-repeat;';
        echo 'background-position: center center;';
        echo 'cursor: pointer;';
        echo '}';

        echo '#' . $this->id . ' .header-icon-menu {';
        echo 'background-image: url("data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 16 16\'%3e%3cpath fill=\'' . urlencode($configTheme->fontColor) . '\' d=\'M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z\'/%3e%3c/svg%3e");';
        echo '}';

        // 手机端
        echo '@media (max-width: 991px) {';
        echo '#' . $this->id . ' {';
        echo 'height: 4rem;';
        echo '}';

        echo '#' . $this->id . ' .header-mobile {';
        echo 'display: block;';
        echo 'position: fixed;';
        echo 'width: 100%;';
        echo 'z-index: 100;';
        echo '}';

        echo '#' . $this->id . ' .header-desktop {';
        echo 'display: none;';
        echo '}';

        echo '#' . $this->id . ' .header-mobile-row {';
        echo 'display: flex;';
        echo 'flex-wrap: wrap;';
        echo 'justify-content: space-between;';
        echo 'align-items: center;';
        echo 'padding: 0.5rem 0;';
        echo '}';

        echo '#' . $this->id . ' .header-mobile-left-toolbars {';
        echo 'flex: 0 1 auto;';
        echo 'display: flex;';
        echo 'justify-content: flex-end;';
        echo '}';

        echo '#' . $this->id . ' .header-mobile-left-toolbar {';
        echo '}';

        echo '#' . $this->id . ' .header-mobile-left-toolbar a {';
        echo 'display: block;';
        echo 'color: #fff;';
        echo 'text-align: center;';
        echo '}';

        echo '#' . $this->id . ' .header-mobile-left-toolbar a:hover  {';
        echo 'text-decoration: none;';
        echo '}';

        echo '#' . $this->id . ' .header-mobile-left-toolbar-menu  {';
        echo 'margin-right: 1rem;';
        echo '}';

        echo '#' . $this->id . ' .header-mobile-left-toolbar-menu .header-icon-menu {';
        echo 'width: 30px;';
        echo 'height: 28px;';
        echo 'background-size: 30px 28px;';
        echo 'margin-top: 2px;';
        echo '}';

        echo '#' . $this->id . ' .header-mobile-logo {';
        echo 'flex: 0 1 auto;';
        echo 'max-width: 160px;';
        echo '}';

        if ($this->config->logoType == 'text') {
            echo '#' . $this->id . ' .header-mobile-logo a {';
            echo 'color: ' . $this->config->logoTextColor . ';';
            echo 'font-size: 30px;';
            echo 'line-height: 30px;';
            echo '}';

            echo '#' . $this->id . ' .header-mobile-logo a:hover {';
            echo 'text-decoration: none;';
            echo '}';
        } else {
            echo '#' . $this->id . ' .header-mobile-logo img {';
            echo 'max-width: 100%;';
            echo 'max-height: 3rem;';
            echo '}';
        }

        echo '#' . $this->id . ' .header-mobile-right-toolbars {';
        echo 'flex: 0 1 auto;';
        echo '}';

        echo '}';

        // 电脑端
        echo '@media (min-width: 992px) {';

        echo '#' . $this->id . ' .header-mobile {';
        echo 'display: none;';
        echo '}';

        echo '#' . $this->id . ' .header-desktop {';
        echo 'display: block;';
        echo 'position: relative;';
        echo 'z-index: 900;';
        echo '}';

        echo '#' . $this->id . ' .header-desktop-row {';
        echo 'display: flex;';
        echo 'flex-wrap: wrap;';
        echo 'justify-content: space-between;';
        echo 'align-items: center;';
        echo 'padding: 0.5rem 0;';
        echo '}';

        echo '#' . $this->id . ' .header-desktop-logo {';
        echo 'flex: 0 1 auto;';
        echo '}';


        if ($this->config->logoType == 'text') {
            echo '#' . $this->id . ' .header-desktop-logo a {';
            echo 'color: ' . $this->config->logoTextColor . ';';
            echo 'font-size: 30px;';
            echo 'line-height: 30px;';
            echo '}';

            echo '#' . $this->id . ' .header-desktop-logo a:hover {';
            echo 'text-decoration: none;';
            echo '}';
        } else {
            echo '#' . $this->id . ' .header-desktop-logo img {';
            if ($this->config->logoImageMaxWidth) {
                echo 'max-width:' . $this->config->logoImageMaxWidth . 'px;';
            }
            if ($this->config->logoImageMaxHeight) {
                echo 'max-height:' . min($this->config->logoImageMaxHeight, 90) . 'px;';
            }
            echo '}';
        }

        echo '#' . $this->id . ' .header-desktop-menu {';
        echo 'flex: 1 1 auto;';
        echo 'padding-left: 5rem;';
        echo 'padding-top: 5px;';
        echo 'padding-bottom: 5px;';
        echo 'height: 2.75rem;';
        echo 'line-height: 2.75rem;';
        echo 'position: relative;';
        echo 'z-index: 100;';
        echo 'font-size: 1.25rem;';
        echo '}';

        echo '#' . $this->id . ' .header-desktop-menu ul {';
        echo 'margin: 0;';
        echo 'padding: 0;';
        echo '}';

        echo '#' . $this->id . ' .header-desktop-menu li {';
        echo 'list-style: none;';
        echo '}';

        echo '#' . $this->id . ' .header-desktop-menu-lv1-item,';
        echo '#' . $this->id . ' .header-desktop-menu-lv1-item-with-dropdown {';
        echo 'display: inline-block;';
        echo 'padding: 0;';
        echo 'margin: 0 2rem 0 0;';
        echo 'position: relative;';
        echo '}';

        echo '#' . $this->id . ' .header-desktop-menu-lv1-item-active > a {';
        echo 'color: var(--major-color);';
        echo '}';

        echo '#' . $this->id . ' .header-desktop-menu-lv1-item-with-dropdown:after {';
        echo 'display: inline-block;';
        echo 'margin-left: .35em;';
        echo 'vertical-align: middle;';
        echo 'content: "";';
        echo 'border-top: .3em solid #999;';
        echo 'border-left: .3em solid transparent;';
        echo 'border-right: .3em solid transparent;';
        echo 'border-bottom: 0;';
        echo '}';

        echo '#' . $this->id . ' .header-desktop-menu-lv2 {';
        echo 'position: absolute;';
        echo 'left: -.5rem;';
        echo 'background-color: #fff;';
        echo 'min-width: 170px;';
        echo 'box-shadow: 0 0 2px 1px #eee;';
        echo 'z-index: 120;';
        echo 'transition: transform 0.3s linear;';
        echo 'transform: translateY(30px);';
        echo 'visibility: hidden;';
        echo '}';

        echo '#' . $this->id . ' .header-desktop-menu-lv1-item-with-dropdown:hover .header-desktop-menu-lv2 {';
        echo 'visibility: visible;';
        echo 'transform: translateY(-1px)';
        echo '}';

        echo '#' . $this->id . ' .header-desktop-menu-lv2-item {';
        echo 'padding: .2rem 2rem;';
        echo '}';

        echo '#' . $this->id . ' .header-desktop-menu-lv2-item-active, ';
        echo '#' . $this->id . ' .header-desktop-menu-lv2-item:hover  {';
        echo 'background-color: #f1f1f1;';
        echo '}';

        echo '}';
        echo '</style>';
    }


    public function display()
    {
        if ($this->config->enable) {
            $this->css();

            $beUrl = beUrl();

            echo '<div class="header">';

            echo '<div class="header-mobile">';
            echo '<div class="be-container">';
            echo '<div class="header-mobile-row">';

            echo '<div class="header-mobile-left-toolbars">';
            echo '<div class="header-mobile-left-toolbar header-mobile-left-toolbar-menu">';
            echo '<a href="javascript:void(0);" onclick="return DrawerMenu.toggle();"><i class="header-icon header-icon-menu"></i></a>';
            echo '</div>';
            echo '</div>';

            echo '<div class="header-mobile-logo">';
            echo '<a href="' . $beUrl . '">';
            if ($this->config->logoType == 'text') {
                echo $this->config->logoText;
            } else {
                echo '<img src="' . $this->config->logoImage . '">';
            }
            echo '</a>';
            echo '</div>';

            echo '<div class="header-mobile-right-toolbars">';
            echo '</div>';

            echo '</div>';
            echo '</div>';
            echo '</div>';


            echo '<div class="header-desktop">';
            echo '<div class="be-container">';
            echo '<div class="header-desktop-row">';

            echo '<div class="header-desktop-logo">';
            echo '<a href="' . $beUrl . '">';
            if ($this->config->logoType == 'text') {
                echo $this->config->logoText;
            } else {
                echo '<img src="' . $this->config->logoImage . '">';
            }
            echo '</a>';
            echo '</div>';

            echo '<div class="header-desktop-menu">';
            echo '<ul class="header-desktop-menu-lv1">';
            $menu = \Be\Be::getMenu('North');
            $menuTree = $menu->getTree();
            $menuActiveId = $menu->getActiveId();
            foreach ($menuTree as $item) {
                $hasSubItem = false;
                if (isset($item->subItems) && is_array($item->subItems) && count($item->subItems) > 0) {
                    $hasSubItem = true;
                }

                $active = false;
                if ($hasSubItem) {
                    foreach ($item->subItems as &$subItem) {
                        if ($item->id === $menuActiveId) {
                            $subItem->active = true;
                            $active = true;
                            break;
                        }
                    }
                    unset($subItem);
                } else {
                    if ($item->id === $menuActiveId) {
                        $active = true;
                    }
                }

                echo '<li class="header-desktop-menu-lv1-item';

                if ($hasSubItem) {
                    echo '-with-dropdown';
                }

                if ($active) {
                    echo ' header-desktop-menu-lv1-item-active';
                }
                echo '">';

                $url = 'javascript:void(0);';
                if ($item->route) {
                    if ($item->params) {
                        $url = beUrl($item->route, $item->params);
                    } else {
                        $url = beUrl($item->route);
                    }
                } else {
                    if ($item->url) {
                        if ($item->url === '/') {
                            $url = beUrl();
                        } else {
                            $url = $item->url;
                        }
                    }
                }
                echo '<a class="link-hover" href="' . $url . '"';
                if ($item->target === '_blank') {
                    echo ' target="_blank"';
                }
                echo '>' . $item->label . '</a>';

                if ($hasSubItem) {
                    echo '<ul class="header-desktop-menu-lv2">';
                    foreach ($item->subItems as $subItem) {
                        echo '<li class="header-desktop-menu-lv2-item';
                        if (isset($subItem->active) && $subItem->active) {
                            echo ' header-desktop-menu-lv2-item-active';
                        }
                        echo '">';

                        $url = 'javascript:void(0);';
                        if ($subItem->route) {
                            if ($subItem->params) {
                                $url = beUrl($subItem->route, $subItem->params);
                            } else {
                                $url = beUrl($subItem->route);
                            }
                        } else {
                            if ($subItem->url) {
                                $url = $subItem->url;
                            }
                        }

                        echo '<a class="header-desktop-menu-lv2-item-link link-hover" href="' . $url . '"';
                        if ($subItem->target === '_blank') {
                            echo ' target="_blank"';
                        }
                        echo '>' . $subItem->label . '</a>';

                        echo '</li>';
                    }
                    echo '</ul>';
                }
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';

            echo '</div>';
            echo '</div>';
            echo '</div>';

            echo '</div>';
        }
    }

}
