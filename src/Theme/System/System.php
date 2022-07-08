<be-html>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no,viewport-fit=cover">
    <title><?php echo $this->title; ?></title>
    <meta name="keywords" content="<?php echo isset($this->meta_keywords) ? $this->meta_keywords : ''; ?>">
    <meta name="description" content="<?php echo isset($this->meta_description) ? $this->meta_description : ''; ?>">
    <meta name="applicable-device" content="pc,mobile">
    <?php
    $beUrl = beUrl();
    $themeWwwUrl = \Be\Be::getProperty('Theme.System')->getWwwUrl();
    ?>
    <base href="<?php echo $beUrl; ?>/">
    <script>var beUrl = "<?php echo $beUrl; ?>"; </script>
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>

    <script src="https://libs.baidu.com/jquery/2.0.3/jquery.min.js"></script>

    <link rel="stylesheet" href="https://cdn.phpbe.com/scss/be.css"/>

    <link rel="stylesheet" href="<?php echo $themeWwwUrl; ?>/css/drawer.css"/>
    <script src="<?php echo $themeWwwUrl; ?>/js/drawer-menu.js"></script>

    <link rel="stylesheet" href="<?php echo $themeWwwUrl; ?>/css/theme.css"/>

    <?php
    $configTheme = \Be\Be::getConfig('Theme.System.Theme');

    $libCss = \Be\Be::getLib('Css');
    $mainColor = $configTheme->mainColor;
    $mainColor1 = $libCss->lighter($mainColor, 10);
    $mainColor2 = $libCss->lighter($mainColor, 20);
    $mainColor3 = $libCss->lighter($mainColor, 30);
    $mainColor4 = $libCss->lighter($mainColor, 40);
    $mainColor5 = $libCss->lighter($mainColor, 50);
    $mainColor6 = $libCss->lighter($mainColor, 60);
    $mainColor7 = $libCss->lighter($mainColor, 70);
    $mainColor8 = $libCss->lighter($mainColor, 80);
    $mainColor9 = $libCss->lighter($mainColor, 90);
    $mainColorHover = $libCss->darker($mainColor, 10);
    ?>
    <style type="text/css">
        html {
            font-size: <?php echo $configTheme->fontSize; ?>px;
            background-color: <?php echo $configTheme->backgroundColor; ?>;
            color: <?php echo $configTheme->fontColor; ?>;
        }

        body {
            --main-color: <?php echo $mainColor; ?>;
            --main-color-1: <?php echo $mainColor1; ?>;
            --main-color-2: <?php echo $mainColor2; ?>;
            --main-color-3: <?php echo $mainColor3; ?>;
            --main-color-4: <?php echo $mainColor4; ?>;
            --main-color-5: <?php echo $mainColor5; ?>;
            --main-color-6: <?php echo $mainColor6; ?>;
            --main-color-7: <?php echo $mainColor7; ?>;
            --main-color-8: <?php echo $mainColor8; ?>;
            --main-color-9: <?php echo $mainColor9; ?>;
            --main-color-hover: <?php echo $mainColorHover; ?>;
        }

        a {
            color: <?php echo $configTheme->linkColor; ?>;
        }

        a:hover {
            color: <?php echo $configTheme->linkHoverColor; ?>;
        }

        .link-hover:before {
            background-color: <?php echo $configTheme->linkHoverColor; ?>;
        }
    </style>

    <be-head>
    </be-head>
</head>
<body>
<be-body>
    <?php
    if ($this->_page->north !== 0) {
        ?>
        <be-north>
            <?php
            if (count($this->_page->northSections)) {
                foreach ($this->_page->northSections as $section) {
                    echo '<div class="be-section" id="' . $section->id . '">';
                    $section->template->display();
                    echo '</div>';
                }
            }
            ?>
        </be-north>
        <?php
    }

    if ($this->_page->middle !== 0) {
        ?>
        <be-middle>
            <?php
            if (count($this->_page->middleSections)) {
                foreach ($this->_page->middleSections as $section) {
                    echo '<div class="be-section" id="' . $section->id . '">';

                    if ($section->key === 'be-page-title') {
                        $section->template->before();
                        ?>
                        <be-page-title><?php echo $this->title; ?></be-page-title>
                        <?php
                        $section->template->after();
                    } else if ($section->key === 'be-page-content') {
                        $section->template->before();
                        ?>
                        <be-page-content></be-page-content>
                        <?php
                        $section->template->after();
                    } else {
                        $section->template->display();
                    }
                    echo '</div>';
                }
            }
            ?>
        </be-middle>
        <?php
    } else {
        ?>
        <be-middle>
            <div class="be-row">
            <?php
            $totalWidth = 0;
            if ($this->_page->west !== 0) {
                $totalWidth += abs($this->_page->west);
            }

            if ($this->_page->center !== 0) {
                $totalWidth += abs($this->_page->center);
            }

            if ($this->_page->east !== 0) {
                $totalWidth += abs($this->_page->east);
            }

            if ($this->_page->west !== 0) {
                $width = (abs($this->_page->west) * 100 / $totalWidth) . '%';
                ?>
                <div class="be-col" style="flex-basis: <?php echo $width; ?>;">
                <be-west>
                    <?php
                    if (count($this->_page->westSections)) {
                        foreach ($this->_page->westSections as $section) {
                            echo '<div class="be-section" id="' . $section->id . '">';
                            $section->template->display();
                            echo '</div>';
                        }
                    }
                    ?>
                </be-west>
                </div>
                <?php
            }

            if ($this->_page->center !== 0) {
                $width = (abs($this->_page->center) * 100 / $totalWidth) . '%';
                ?>
                <div class="be-col" style="flex-basis: <?php echo $width; ?>;">
                <be-center>
                    <?php
                    if (count($this->_page->centerSections)) {
                        foreach ($this->_page->centerSections as $section) {
                            echo '<div class="be-section" id="' . $section->id . '">';
                            if ($section->key === 'be-page-title') {
                                $section->template->before();
                                ?>
                                <be-page-title><?php echo $this->title; ?></be-page-title>
                                <?php
                                $section->template->after();
                            } else if ($section->key === 'be-page-content') {
                                $section->template->before();
                                ?>
                                <be-page-content></be-page-content>
                                <?php
                                $section->template->after();
                            } else {
                                $section->template->display();
                            }
                            echo '</div>';
                        }
                    }
                    ?>
                </be-center>
                </div>
                <?php
            }

            if ($this->_page->east !== 0) {
                $width = (abs($this->_page->east) * 100 / $totalWidth) . '%';
                ?>
                <div class="be-col" style="flex-basis: <?php echo $width; ?>;">
                <be-east>
                    <?php
                    if (count($this->_page->eastSections)) {
                        foreach ($this->_page->eastSections as $section) {
                            echo '<div class="be-section" id="' . $section->id . '">';
                            $section->template->display();
                            echo '</div>';
                        }
                    }
                    ?>
                </be-east>
                </div>
                <?php
            }
            ?>
            </div>
        </be-middle>
        <?php
    }

    if ($this->_page->south !== 0) {
        ?>
        <be-south>
            <?php
            if (count($this->_page->southSections)) {
                foreach ($this->_page->southSections as $section) {
                    echo '<div class="be-section" id="' . $section->id . '">';
                    $section->template->display();
                    echo '</div>';
                }
            }
            ?>
        </be-south>
        <?php
    }
    ?>
</be-body>


<div id="overlay"></div>

<div id="drawer-menu" class="drawer">
    <div class="drawer-fixed-header">
        <div class="drawer-header">
            <div class="drawer-title">导航</div>
            <button type="button" class="drawer-close" onclick="DrawerMenu.hide();"></button>
        </div>
    </div>
    <ul class="drawer-menu-lv1">
        <?php
        $menu = \Be\Be::getMenu('North');
        $menuTree = $menu->getTree();
        foreach ($menuTree as $item) {
            $hasSubItem = false;
            if (isset($item->subItems) && is_array($item->subItems) && count($item->subItems) > 0) {
                $hasSubItem = true;
            }

            if ($hasSubItem) {
                echo '<li class="drawer-menu-lv1-item-with-dropdown">';
            } else {
                echo '<li class="drawer-menu-lv1-item">';
            }

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
            echo '<a href="' . $url . '"';
            if ($item->target === '_blank') {
                echo ' target="_blank"';
            }
            echo '>' . $item->label . '</a>';

            if ($hasSubItem) {
                echo '<div class="drawer-menu-lv1-dropdown">';
                echo '<div class="drawer-menu-lv1-dropdown-title">';
                echo $item->label;
                echo '</div>';
                echo '<ul class="drawer-menu-lv2">';
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

                    echo '<li class="drawer-menu-lv2-item"><a href="' . $url . '"';
                    if ($subItem->target === '_blank') {
                        echo ' target="_blank"';
                    }
                    echo '>' . $subItem->label . '</a></li>';
                }
                echo '</ul>';
                echo '</div>';
            }

            echo '</li>';
        }
        ?>
    </ul>
</div>

</body>
</html>
</be-html>