<?php
if ($sectionData['enable']) {
    $configTheme = \Be\Be::getConfig('Theme.Sample.Theme');
    ?>
    <style type="text/css">
        #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-mobile,
        #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-desktop {
            background-color: <?php echo $sectionData['backgroundColor']; ?>;

            /*border-bottom: #ddd 1px solid;*/
            box-shadow: 0 3px 5px 0 #eee;
            margin-bottom: 5px;
        }

        #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-icon {
            display: inline-block;
            border: none;
            background-repeat: no-repeat;
            background-position: center center;
            cursor: pointer;
        }

        #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-icon-menu {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='<?php echo urlencode($configTheme->pageColor) ?>' d='M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z'/%3e%3c/svg%3e");
        }

        /* 手机端 */
        @media (max-width: 991px) {
            #header-<?php echo $sectionType . '-' . $sectionKey; ?> {
                height: 5rem;
            }

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-mobile {
                display: block;
                position: fixed;
                width: 100%;
                z-index: 100;
            }

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-desktop {
                display: none;
            }

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-mobile-row {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                align-items: center;
                padding: 0.5rem 0;
            }

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-mobile-left-toolbars {
                flex: 0 1 auto;
                display: flex;
                justify-content: flex-end;
            }

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-mobile-left-toolbar {

            }

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-mobile-left-toolbar a {
                display: block;
                color: #fff;
                text-align: center;
            }

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-mobile-left-toolbar a:hover {
                text-decoration: none;
            }


            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-mobile-left-toolbar-menu {
                margin-right: 1rem;
            }

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-mobile-left-toolbar-menu .header-icon-menu {
                width: 30px;
                height: 28px;
                background-size: 30px 28px;
                margin-top: 2px;
            }

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-mobile-logo {
                flex: 0 1 auto;
                max-width: 120px;
            }

            <?php
            if ($sectionData['logoType'] == 'text') {
                ?>
                #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-mobile-logo a {
                    color: #fff;
                    font-size: 30px;
                    line-height: 30px;
                }

                #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-mobile-logo a:hover {
                    text-decoration: none;
                }
            <?php
            } else {
                ?>
                    #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-mobile-logo img {
                        max-width: 100%;
                        max-height: 90px;
                    }
                <?php
            }
            ?>

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-mobile-right-toolbars {
                flex: 0 1 auto;
            }
        }

        /* 电脑端 */
        @media (min-width: 992px) {
            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-mobile {
                display: none;
            }

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-desktop {
                display: block;
                position: relative;
                z-index: 900;
            }

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-desktop-row {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                align-items: center;
                padding: 0.5rem 0;
            }

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-desktop-logo {
                flex: 0 1 auto;
            }

            <?php
            if ($sectionData['logoType'] == 'text') {
                ?>
                #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-desktop-logo a {
                    color: #fff;
                    font-size: 30px;
                    line-height: 30px;
                }

                #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-desktop-logo a:hover {
                    text-decoration: none;
                }
            <?php
            } else {
                ?>
                    #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-desktop-logo img {
                    <?php
                    if ($sectionData['logoImageMaxWidth']) {
                        echo 'max-width:' . $sectionData['logoImageMaxWidth'] . 'px;';
                    }
                    if ($sectionData['logoImageMaxHeight']) {
                        echo 'max-height:' . min($sectionData['logoImageMaxHeight'], 90) . 'px;';
                    }
                    ?>
                    }
                <?php
            }
            ?>

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-desktop-menu {
                flex: 1 1 auto;
                padding-left: 5rem;
                padding-top: 5px;
                padding-bottom: 5px;
            }

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-desktop-menu {
                height: 2.75rem;
                line-height: 2.75rem;
                position: relative;
                z-index: 100;
                font-size: 1.25rem;
            }

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-desktop-menu-lv1-item,
            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-desktop-menu-lv1-item-with-dropdown {
                display: inline-block;
                padding: 0;
                margin: 0 2rem 0 0;
                position: relative;
            }


            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-desktop-menu-lv1-item-with-dropdown:after {
                display: inline-block;
                margin-left: .35em;
                vertical-align: middle;
                content: "";
                border-top: .3em solid #999;
                border-left: .3em solid transparent;
                border-right: .3em solid transparent;
                border-bottom: 0;
            }


            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-desktop-menu-lv2 {
                position: absolute;
                left: -.5rem;
                background-color: #fff;
                min-width: 170px;
                box-shadow: 0 0 2px 1px #eee;
                z-index: 120;
                transition: transform 0.3s linear;
                transform: translateY(30px);
                visibility: hidden;
            }

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-desktop-menu-lv1-item-with-dropdown:hover .header-desktop-menu-lv2 {
                visibility: visible;
                transform: translateY(-1px)
            }

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-desktop-menu-lv2-item {
                padding: .2rem 2rem;
            }

            #header-<?php echo $sectionType . '-' . $sectionKey; ?> .header-desktop-menu-lv2-item:hover {
                background-color: #f1f1f1;
            }
        }
    </style>

    <div id="header-<?php echo $sectionType . '-' . $sectionKey; ?>">
        <div class="header-mobile">
            <div class="be-container">

                <div class="header-mobile-row">
                    <div class="header-mobile-left-toolbars">
                        <div class="header-mobile-left-toolbar header-mobile-left-toolbar-menu">
                            <a href="javascript:void(0);" onclick="return DrawerMenu.toggle();">
                                <i class="header-icon header-icon-menu"></i>
                            </a>
                        </div>
                    </div>

                    <div class="header-mobile-logo">
                        <a href="<?php echo beUrl(); ?>">
                            <?php
                            if ($sectionData['logoType'] == 'text') {
                                echo $sectionData['logoText'];
                            } else {
                                echo '<img src="' . $sectionData['logoImage'] . '">';
                            }
                            ?>
                        </a>
                    </div>

                    <div class="header-mobile-right-toolbars">
                    </div>

                </div>

            </div>
        </div>
        <div class="header-desktop">

            <div class="be-container">
                <div class="header-desktop-row">
                    <div class="header-desktop-logo">
                        <a href="<?php echo beUrl(); ?>">
                            <?php
                            if ($sectionData['logoType'] == 'text') {
                                echo $sectionData['logoText'];
                            } else {
                                echo '<img src="' . $sectionData['logoImage'] . '">';
                            }
                            ?>
                        </a>
                    </div>
                    <div class="header-desktop-menu">
                        <ul class="header-desktop-menu-lv1">
                            <?php
                            $menu = \Be\Be::getMenu('North');
                            $menuTree = $menu->getTree();
                            foreach ($menuTree as $item) {
                                $hasSubItem = false;
                                if (isset($item->subItems) && is_array($item->subItems) && count($item->subItems) > 0) {
                                    $hasSubItem = true;
                                }

                                echo '<li class="header-desktop-menu-lv1-item' . ($hasSubItem ? '-with-dropdown' : '') . '">';

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
                                echo '<a class="link-hover" href="'.$url.'"';
                                if ($item->target === '_blank') {
                                    echo ' target="_blank"';
                                }
                                echo '>' . $item->label . '</a>';

                                if ($hasSubItem) {
                                    echo '<ul class="header-desktop-menu-lv2">';
                                    foreach ($item->subItems as $subItem) {
                                        $url = 'javascript:void(0);';
                                        if ($subItem->route) {
                                            if ($subItem->params) {
                                                $url = beUrl($subItem->route, $subItem->params);
                                            } else {
                                                $url = beUrl($subItem->route);
                                            }
                                        } else {
                                            if ($subItem->url) {
                                                if ($subItem->url === '/') {
                                                    $url = beUrl();
                                                } else {
                                                    $url = $subItem->url;
                                                }
                                            }
                                        }
                                        echo '<li class="header-desktop-menu-lv2-item"><a class="link-hover" href="'.$url.'"';
                                        if ($subItem->target === '_blank') {
                                            echo ' target="_blank"';
                                        }
                                        echo '>' . $subItem->label . '</a></li>';
                                    }
                                    echo '</ul>';
                                }
                                echo '</li>';
                            }
                            ?>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <?php
}

