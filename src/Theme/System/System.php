<be-html>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no,viewport-fit=cover">
    <title><?php echo $this->title; ?></title>
    <meta name="keywords" content="<?php echo $this->metaKeywords ?? ''; ?>">
    <meta name="description" content="<?php echo $this->metaDescription ?? ''; ?>">
    <meta name="applicable-device" content="pc,mobile">
    <base href="<?php echo beUrl(); ?>/">
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>

    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

    <link rel="stylesheet" href="https://cdn.phpbe.com/ui/be.css"/>
    <link rel="stylesheet" href="https://cdn.phpbe.com/ui/be-icons.css"/>

    <?php
    $configTheme = \Be\Be::getConfig('Theme.System.Theme');
    $themeWwwUrl = \Be\Be::getProperty('Theme.System')->getWwwUrl();
    ?>
    <link rel="stylesheet" href="<?php echo $themeWwwUrl; ?>/css/drawer.css"/>
    <script src="<?php echo $themeWwwUrl; ?>/js/drawer-menu.js"></script>

    <link rel="stylesheet" href="<?php echo $themeWwwUrl; ?>/css/theme.css?v=20221021"/>

    <style type="text/css">
        html {
            font-size: <?php echo $configTheme->fontSize; ?>px;
            background-color: <?php echo $configTheme->backgroundColor; ?>;
            color: <?php echo $configTheme->fontColor; ?>;
        }

        body {
            <?php
            // 主题配置
            $configTheme = \Be\Be::getConfig('Theme.System.Theme');

            echo '--major-color: ' . $configTheme->majorColor . ';';

            // CSS 处理库
            $libCss = \Be\Be::getLib('Css');
            for ($i=1; $i<=9; $i++) {
                echo '--major-color-' . $i. ': ' . $libCss->lighter($configTheme->majorColor, $i * 10) . ';';
                echo '--major-color' . $i. ': ' . $libCss->darker($configTheme->majorColor, $i * 10) . ';';
            }
            ?>
        }
    </style>

    <be-head>
    </be-head>
</head>
<body>
<be-body></be-body>

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