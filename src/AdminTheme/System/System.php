<be-html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo $this->title; ?></title>

    <?php
    $beUrl = beUrl();
    $appSystemWwwUrl = \Be\Be::getProperty('App.System')->getWwwUrl();
    $adminThemeWwwUrl = \Be\Be::getProperty('AdminTheme.System')->getWwwUrl();
    ?>
    <base href="<?php echo $beUrl; ?>/" >
    <script>var beUrl = "<?php echo $beUrl; ?>"; </script>

    <script src="<?php echo $appSystemWwwUrl; ?>/lib/jquery/jquery-1.12.4.min.js"></script>

    <script src="<?php echo $appSystemWwwUrl; ?>/lib/vue/vue-2.6.11.min.js"></script>

    <script src="<?php echo $appSystemWwwUrl; ?>/lib/axios/axios-0.19.0.min.js"></script>
    <script>Vue.prototype.$http = axios;</script>

    <script src="<?php echo $appSystemWwwUrl; ?>/lib/vue-cookies/vue-cookies-1.5.13.js"></script>

    <link rel="stylesheet" href="<?php echo $appSystemWwwUrl; ?>/lib/element-ui/element-ui-2.15.7.css">
    <script src="<?php echo $appSystemWwwUrl; ?>/lib/element-ui/element-ui-2.15.7.js"></script>

    <link rel="stylesheet" href="//cdn.phpbe.com/ui/be.css?v=20220926" />
    <link rel="stylesheet" href="//cdn.phpbe.com/ui/be-icons.css"/>

    <be-head>
    </be-head>

    <style>
        html {
            font-size: 14px;
            background-color: #eef0f5;
            color: #333;
        }

        body {
            margin: 0;
            padding: 0;
        }

        [v-cloak] {display: none !important;}

        .el-submenu__title i,
        .el-menu-item i {
            color: inherit;
        }

        .el-submenu .el-link,
        .el-menu-item .el-link {
            display: block;
        }

        .el-table th.el-table__cell {
            color: #666;
            background-color: #EBEEF5;
        }

        .be-west {
            width: 200px;
            position: fixed;
            height: 100%;
            left: 0;
            top: 0;
            bottom: 0;
            background-color: #30354d;
            z-index: 999;
            overflow: hidden;
        }

        #app-west {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .be-west a {
            display: inline-block;
        }

        .be-west .logo {
            flex:0;
            height: 60px;
            background-color: #22273b;
        }

        .be-west .logo img,
        .be-west .logo svg {
            width: 200px;
            height: 60px;
        }

        .be-west .menu {
            flex:1;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .be-west .menu::-webkit-scrollbar {
            width: 6px;
        }

        .be-west .menu::-webkit-scrollbar-thumb {
            background-color: #59648f;
        }

        .be-west .menu::-webkit-scrollbar-track {
            background-color: #22273b;
        }

        .be-west .menu .el-menu {
            border-right: 0;
        }

        .be-west .menu .el-menu-item,
        .be-west-popup-menu .el-menu-item,
        .be-west .menu .el-submenu__title {
            height: 40px;
            line-height: 40px;
        }

        .be-west .menu .el-menu-item,
        .be-west-popup-menu .el-menu-item {
            padding: 0 !important;
        }

        .be-west .menu .el-menu-item .el-link,
        .be-west-popup-menu .el-menu-item .el-link {
            color: #aaa;
            padding-left: 20px !important;
        }

        .be-west .menu .el-menu-item .el-link span {
            margin-left: 0;
        }

        .be-west .menu .el-submenu .el-menu-item .el-link {
            padding-left: 50px !important;
        }

        .be-west .menu .el-submenu .el-submenu__title:hover,
        .be-west .menu .el-menu-item:hover,
        .be-west-popup-menu .el-menu-item:hover {
            background-color: transparent !important;
        }

        .be-west .menu .el-menu-item.is-active,
        .be-west-popup-menu .el-menu-item.is-active {
            background-color: #4b5377 !important;
        }

        .be-west .menu .el-menu-item .el-link:hover,
        .be-west-popup-menu .el-menu-item .el-link:hover,
        .be-west .menu .el-submenu .el-submenu__title:hover i,
        .be-west .menu .el-submenu .el-submenu__title:hover span,
        .be-west .menu .el-submenu.is-opened .el-submenu__title i,
        .be-west .menu .el-submenu.is-opened .el-submenu__title span,
        .be-west .menu .el-submenu.is-active .el-submenu__title i,
        .be-west .menu .el-submenu.is-active .el-submenu__title span,
        .be-west-popup-menu .el-submenu.is-active .el-submenu__title i,
        .be-west-popup-menu .el-submenu.is-active .el-submenu__title span,
        .be-west .menu .el-menu-item.is-active i,
        .be-west .menu .el-menu-item.is-active span,
        .be-west-popup-menu .el-menu-item.is-active i,
        .be-west-popup-menu .el-menu-item.is-active span {
            color: #fff;
        }

        .be-west .toggle {
            flex: 0;
            cursor: pointer;
            background-color: #22273b;
            text-align: center;
            color: #aaa;
            line-height: 40px;
        }

        .be-west .toggle:hover {
            color: #fff;
        }

        .be-north {
            position: fixed;
            left: 200px;
            right: 0;
            top: 0;
            height: 60px;
            background-color: #fff;
            border-bottom: 1px solid #cfd6db;
            z-index: 1000;
        }

        .be-north .north-links i {
            font-size: 16px;
            vertical-align: middle;
        }

        .be-north .lh-60 {
            line-height: 60px;
        }

        .be-north .el-submenu__title,
        .be-north .el-menu-item {
            padding: 0 10px;
        }


        .be-north .el-dropdown-link {
            cursor: pointer;
        }

        .be-north-popup-menu .el-submenu .el-submenu__title:hover i,
        .be-north-popup-menu .el-submenu .el-submenu__title:hover span,
        .be-north-popup-menu .el-submenu.is-opened .el-submenu__title i ,
        .be-north-popup-menu .el-submenu.is-opened .el-submenu__title span,
        .be-north-popup-menu .el-submenu.is-active .el-submenu__title i,
        .be-north-popup-menu .el-submenu.is-active .el-submenu__title span,
        .be-north-popup-menu .el-menu-item.is-active i,
        .be-north-popup-menu .el-menu-item.is-active span {
            /*color: #409EFF;*/
        }

        .be-middle {
            margin-top: 60px;
            margin-left: 200px;
            overflow: auto;
        }

        .be-page-title {
            font-size: 1.25rem;
            height: 3.5rem;
            line-height: 3.5rem;
        }

        .be-page-content {
        }

        .be-dialog,
        .be-drawer {
            z-index: 999999 !important;
        }

        .be-dialog .el-dialog__body {padding: 0 5px;}

        .el-drawer__header {
            padding: 10px 20px 20px 20px;
            margin-bottom: 0;
            font-size: 1.1rem;
            font-weight: bold;
        }

        .be-north-menu-style-toggle {
            height: 20px;
            line-height: 20px;
            font-size: 20px;
            color: #ccc;
            cursor: pointer;
            display: inline-block;
            padding-left: 2px;
        }

        .be-north-menu-style-toggle-on {
            color: #999;
        }

    </style>
</head>
<body>
    <be-body>
        <be-north>
            <div id="app-north" v-cloak>

                <div class="be-row">

                    <?php
                    $adminMenuStyle = (int) \Be\Be::getRequest()->cookie('be-admin-north-menu-style', 6);
                    ?>
                    <div class="be-col-auto lh-60">
                        <div style="line-height: 1;">
                            <div class="be-row">
                                <div class="be-col-auto">
                                    <a @click="toggleStyle(1)" class="be-north-menu-style-toggle <?php echo 1 === $adminMenuStyle ? 'be-north-menu-style-toggle-on' : ''; ?>">
                                        <i class="bi-1-square<?php echo 1 === $adminMenuStyle ? '-fill' : ''; ?>"></i>
                                    </a>
                                </div>
                                <div class="be-col-auto">
                                    <a @click="toggleStyle(2)" class="be-north-menu-style-toggle <?php echo 2 === $adminMenuStyle ? 'be-north-menu-style-toggle-on' : ''; ?>">
                                        <i class="bi-2-square<?php echo 2 === $adminMenuStyle ? '-fill' : ''; ?>"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="be-row">
                                <div class="be-col-auto">
                                    <a @click="toggleStyle(3)" class="be-north-menu-style-toggle <?php echo 3 === $adminMenuStyle ? 'be-north-menu-style-toggle-on' : ''; ?>">
                                        <i class="bi-3-square<?php echo 3 === $adminMenuStyle ? '-fill' : ''; ?>"></i>
                                    </a>
                                </div>
                                <div class="be-col-auto">
                                    <a @click="toggleStyle(4)" class="be-north-menu-style-toggle <?php echo 4 === $adminMenuStyle ? 'be-north-menu-style-toggle-on' : ''; ?>">
                                        <i class="bi-4-square<?php echo 4 === $adminMenuStyle ? '-fill' : ''; ?>"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="be-row">
                                <div class="be-col-auto">
                                    <a @click="toggleStyle(5)" class="be-north-menu-style-toggle <?php echo 5 === $adminMenuStyle ? 'be-north-menu-style-toggle-on' : ''; ?>">
                                        <i class="bi-5-square<?php echo 5 === $adminMenuStyle ? '-fill' : ''; ?>"></i>
                                    </a>
                                </div>
                                <div class="be-col-auto">
                                    <a @click="toggleStyle(6)" class="be-north-menu-style-toggle <?php echo 6 === $adminMenuStyle ? 'be-north-menu-style-toggle-on' : ''; ?>">
                                        <i class="bi-6-square<?php echo 6 === $adminMenuStyle ? '-fill' : ''; ?>"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="be-col be-pl-50">
                        <?php
                        $adminMenu = \Be\Be::getAdminMenu();
                        $adminMenuTree = $adminMenu->getTree();
                        $adminMenuActiveMenuKey = $adminMenu->getActiveMenuKey();

                        switch ($adminMenuStyle) {
                            case 1:
                                ?>
                                <el-menu
                                        mode="horizontal"
                                        :default-active="defaultActive">
                                    <?php

                                    echo '<el-submenu popper-class="be-north-popup-menu">';

                                    echo '<template slot="title">';
                                    echo '<i class="el-icon-bi bi-list"></i>';
                                    echo '<span>已安装应用</span>';
                                    echo '</template>';

                                    foreach ($adminMenuTree as $item) {

                                        if (isset($item->subItems) && is_array($item->subItems) && count($item->subItems) > 0) {
                                            echo '<el-menu-item index="north-menu-'.$item->id.'">';

                                            $url = '';
                                            if ($item->url) {
                                                $url = $item->url;
                                            } else {
                                                foreach ($item->subItems as $subItem) {
                                                    if ($subItem->url) {
                                                        $url = $subItem->url;
                                                        break;
                                                    }

                                                    if (isset($subItem->subItems) && is_array($subItem->subItems) && count($subItem->subItems) > 0) {
                                                        foreach ($subItem->subItems as $subSubItem) {
                                                            $url = $subSubItem->url;
                                                            break 2;
                                                        }
                                                    }
                                                }
                                            }

                                            echo '<template slot="title">';
                                            echo '<el-link href="'.$url.'" icon="'.$item->icon.'" :underline="false" style="display:inline !important;">';
                                            echo $item->label;
                                            echo '</el-link>';
                                            echo '</template>';

                                            echo '</el-menu-item>';
                                        }
                                    }
                                    echo '</el-submenu>';
                                    ?>
                                </el-menu>
                                <?php
                                break;
                            case 2:
                                ?>
                                <el-menu
                                        mode="horizontal"
                                        :default-active="defaultActive">
                                    <?php

                                    echo '<el-submenu popper-class="be-north-popup-menu">';

                                    echo '<template slot="title">';
                                    echo '<i class="el-icon-bi bi-list"></i>';
                                    echo '<span>已安装应用</span>';
                                    echo '</template>';

                                    foreach ($adminMenuTree as $item) {

                                        $hasSubItem = false;
                                        if (isset($item->subItems) && is_array($item->subItems) && count($item->subItems) > 0) {
                                            $hasSubItem = true;
                                        }

                                        // 有子菜单
                                        if ($hasSubItem) {
                                            echo '<el-submenu index="north-menu-'.$item->id.'" popper-class="be-north-popup-menu">';

                                            echo '<template slot="title">';
                                            if ($item->url) {
                                                echo '<el-link href="'.$item->url.'" icon="'.$item->icon.'" :underline="false" style="display:inline !important;">';
                                                echo $item->label;
                                                echo '</el-link>';
                                            } else {
                                                echo '<i class="'.$item->icon.'"></i>';
                                                echo '<span>'.$item->label.'</span>';
                                            }
                                            echo '</template>';

                                            foreach ($item->subItems as $subItem) {

                                                $hasSubSubItem = false;
                                                if (isset($subItem->subItems) && is_array($subItem->subItems) && count($subItem->subItems) > 0) {
                                                    $hasSubSubItem = true;
                                                }

                                                if ($hasSubSubItem) {
                                                    echo '<el-submenu index="north-menu-'.$subItem->id.'" popper-class="be-north-popup-menu">';

                                                    echo '<template slot="title">';
                                                    if ($subItem->url) {
                                                        echo '<el-link href="'.$subItem->url.'" icon="'.$subItem->icon.'" :underline="false">';
                                                        echo $subItem->label;
                                                        echo '</el-link>';
                                                    } else {
                                                        echo '<i class="'.$subItem->icon.'"></i>';
                                                        echo '<span>'.$subItem->label.'</span>';
                                                    }
                                                    echo '</template>';

                                                    foreach ($subItem->subItems as $subSubItem) {
                                                        echo '<el-menu-item index="north-menu-'.$subSubItem->id.'">';
                                                        echo '<el-link href="'.$subSubItem->url.'" icon="'.$subSubItem->icon.'" :underline="false">';
                                                        echo $subSubItem->label;
                                                        echo '</el-link>';
                                                        echo '</el-menu-item>';
                                                    }
                                                    echo '</el-submenu>';
                                                } else {
                                                    echo '<el-menu-item index="north-menu-'.$subItem->id.'">';
                                                    echo '<el-link href="'.$subItem->url.'" icon="'.$subItem->icon.'" :underline="false">';
                                                    echo $subItem->label;
                                                    echo '</el-link>';
                                                    echo '</el-menu-item>';
                                                }
                                            }
                                            echo '</el-submenu>';
                                        }
                                    }



                                    echo '</el-submenu>';
                                    ?>
                                </el-menu>
                                <?php
                                break;
                            case 3:
                                ?>
                                <el-menu
                                        mode="horizontal"
                                        :default-active="defaultActive">
                                    <?php
                                    foreach ($adminMenuTree as $item) {

                                        if (isset($item->subItems) && is_array($item->subItems) && count($item->subItems) > 0) {
                                            echo '<el-menu-item index="north-menu-'.$item->id.'">';

                                            $url = '';
                                            if ($item->url) {
                                                $url = $item->url;
                                            } else {
                                                foreach ($item->subItems as $subItem) {
                                                    if ($subItem->url) {
                                                        $url = $subItem->url;
                                                        break;
                                                    }

                                                    if (isset($subItem->subItems) && is_array($subItem->subItems) && count($subItem->subItems) > 0) {
                                                        foreach ($subItem->subItems as $subSubItem) {
                                                            $url = $subSubItem->url;
                                                            break 2;
                                                        }
                                                    }
                                                }
                                            }

                                            echo '<template slot="title">';
                                            echo '<el-link href="'.$url.'" icon="'.$item->icon.'" :underline="false" style="display:inline !important;">';
                                            echo '</el-link>';
                                            echo '</template>';

                                            echo '</el-menu-item>';
                                        }
                                    }
                                    ?>
                                </el-menu>
                                <?php
                                break;
                            case 4:
                                ?>
                                <el-menu
                                        mode="horizontal"
                                        :default-active="defaultActive">
                                    <?php
                                    foreach ($adminMenuTree as $item) {

                                        if (isset($item->subItems) && is_array($item->subItems) && count($item->subItems) > 0) {
                                            echo '<el-menu-item index="north-menu-'.$item->id.'">';

                                            $url = '';
                                            if ($item->url) {
                                                $url = $item->url;
                                            } else {
                                                foreach ($item->subItems as $subItem) {
                                                    if ($subItem->url) {
                                                        $url = $subItem->url;
                                                        break;
                                                    }

                                                    if (isset($subItem->subItems) && is_array($subItem->subItems) && count($subItem->subItems) > 0) {
                                                        foreach ($subItem->subItems as $subSubItem) {
                                                            $url = $subSubItem->url;
                                                            break 2;
                                                        }
                                                    }
                                                }
                                            }

                                            echo '<template slot="title">';
                                            echo '<el-link href="'.$url.'" icon="'.$item->icon.'" :underline="false" style="display:inline !important;">';
                                            echo $item->label;
                                            echo '</el-link>';
                                            echo '</template>';

                                            echo '</el-menu-item>';
                                        }
                                    }
                                    ?>
                                </el-menu>
                                <?php
                                break;
                            case 5:
                                ?>
                                <el-menu
                                        mode="horizontal"
                                        :default-active="defaultActive">
                                    <?php
                                    foreach ($adminMenuTree as $item) {

                                        $hasSubItem = false;
                                        if (isset($item->subItems) && is_array($item->subItems) && count($item->subItems) > 0) {
                                            $hasSubItem = true;
                                        }

                                        // 有子菜单
                                        if ($hasSubItem) {
                                            echo '<el-submenu index="north-menu-'.$item->id.'" popper-class="be-north-popup-menu">';

                                            echo '<template slot="title">';
                                            if ($item->url) {
                                                echo '<el-link href="'.$item->url.'" icon="'.$item->icon.'" :underline="false" style="display:inline !important;">';
                                                //echo $item->label;
                                                echo '</el-link>';
                                            } else {
                                                echo '<i class="'.$item->icon.'"></i>';
                                                //echo '<span>'.$item->label.'</span>';
                                            }
                                            echo '</template>';

                                            foreach ($item->subItems as $subItem) {

                                                $hasSubSubItem = false;
                                                if (isset($subItem->subItems) && is_array($subItem->subItems) && count($subItem->subItems) > 0) {
                                                    $hasSubSubItem = true;
                                                }

                                                if ($hasSubSubItem) {
                                                    echo '<el-submenu index="north-menu-'.$subItem->id.'" popper-class="be-north-popup-menu">';

                                                    echo '<template slot="title">';
                                                    if ($subItem->url) {
                                                        echo '<el-link href="'.$subItem->url.'" icon="'.$subItem->icon.'" :underline="false">';
                                                        echo $subItem->label;
                                                        echo '</el-link>';
                                                    } else {
                                                        echo '<i class="'.$subItem->icon.'"></i>';
                                                        echo '<span>'.$subItem->label.'</span>';
                                                    }
                                                    echo '</template>';

                                                    foreach ($subItem->subItems as $subSubItem) {
                                                        echo '<el-menu-item index="north-menu-'.$subSubItem->id.'">';
                                                        echo '<el-link href="'.$subSubItem->url.'" icon="'.$subSubItem->icon.'" :underline="false">';
                                                        echo $subSubItem->label;
                                                        echo '</el-link>';
                                                        echo '</el-menu-item>';
                                                    }
                                                    echo '</el-submenu>';
                                                } else {
                                                    echo '<el-menu-item index="north-menu-'.$subItem->id.'">';
                                                    echo '<el-link href="'.$subItem->url.'" icon="'.$subItem->icon.'" :underline="false">';
                                                    echo $subItem->label;
                                                    echo '</el-link>';
                                                    echo '</el-menu-item>';
                                                }
                                            }
                                            echo '</el-submenu>';
                                        }
                                    }
                                    ?>
                                </el-menu>
                                <?php
                                break;
                            case 6:
                                ?>
                                <el-menu
                                        mode="horizontal"
                                        :default-active="defaultActive">
                                    <?php
                                    foreach ($adminMenuTree as $item) {

                                        $hasSubItem = false;
                                        if (isset($item->subItems) && is_array($item->subItems) && count($item->subItems) > 0) {
                                            $hasSubItem = true;
                                        }

                                        // 有子菜单
                                        if ($hasSubItem) {
                                            echo '<el-submenu index="north-menu-'.$item->id.'" popper-class="be-north-popup-menu">';

                                            echo '<template slot="title">';
                                            if ($item->url) {
                                                echo '<el-link href="'.$item->url.'" icon="'.$item->icon.'" :underline="false" style="display:inline !important;">';
                                                echo $item->label;
                                                echo '</el-link>';
                                            } else {
                                                echo '<i class="'.$item->icon.'"></i>';
                                                echo '<span>'.$item->label.'</span>';
                                            }
                                            echo '</template>';

                                            foreach ($item->subItems as $subItem) {

                                                $hasSubSubItem = false;
                                                if (isset($subItem->subItems) && is_array($subItem->subItems) && count($subItem->subItems) > 0) {
                                                    $hasSubSubItem = true;
                                                }

                                                if ($hasSubSubItem) {
                                                    echo '<el-submenu index="north-menu-'.$subItem->id.'" popper-class="be-north-popup-menu">';

                                                    echo '<template slot="title">';
                                                    if ($subItem->url) {
                                                        echo '<el-link href="'.$subItem->url.'" icon="'.$subItem->icon.'" :underline="false">';
                                                        echo $subItem->label;
                                                        echo '</el-link>';
                                                    } else {
                                                        echo '<i class="'.$subItem->icon.'"></i>';
                                                        echo '<span>'.$subItem->label.'</span>';
                                                    }
                                                    echo '</template>';

                                                    foreach ($subItem->subItems as $subSubItem) {
                                                        echo '<el-menu-item index="north-menu-'.$subSubItem->id.'">';
                                                        echo '<el-link href="'.$subSubItem->url.'" icon="'.$subSubItem->icon.'" :underline="false">';
                                                        echo $subSubItem->label;
                                                        echo '</el-link>';
                                                        echo '</el-menu-item>';
                                                    }
                                                    echo '</el-submenu>';
                                                } else {
                                                    echo '<el-menu-item index="north-menu-'.$subItem->id.'">';
                                                    echo '<el-link href="'.$subItem->url.'" icon="'.$subItem->icon.'" :underline="false">';
                                                    echo $subItem->label;
                                                    echo '</el-link>';
                                                    echo '</el-menu-item>';
                                                }
                                            }
                                            echo '</el-submenu>';
                                        }
                                    }
                                    ?>
                                </el-menu>
                                <?php
                                break;
                        }
                        ?>
                    </div>

                    <div class="be-col-auto be-pr-150 north-links lh-60">
                        <el-link href="https://www.phpbe.com/doc/help/v2" icon="el-icon-warning-outline" target="_blank" :underline="false">帮助</el-link>
                    </div>

                    <div class="be-col-auto be-pr-150 lh-60">
                        <el-link href="<?php echo beUrl() ?>" icon="el-icon-view" target="_blank" :underline="false">主页</el-link>
                    </div>

                    <?php
                    $configUser = \Be\Be::getConfig('App.System.AdminUser');
                    $my = \Be\Be::getAdminUser();
                    ?>
                    <div class="be-col-auto be-pr-30 north-links lh-60">
                        <img src="<?php
                        if ($my->avatar === '') {
                            echo \Be\Be::getProperty('App.System')->getWwwUrl().'/admin/admin-user/images/avatar.png';
                        } else {
                            echo \Be\Be::getStorage()->getRootUrl() . '/app/system/admin-user/avatar/' . $my->avatar;
                        }
                        ?>" alt="" style="max-width:24px;max-height:24px; vertical-align: middle" >
                    </div>

                    <div class="be-col-auto be-pr-200 north-links" style="padding-top: 20px;">
                        <el-dropdown>
                            <span class="el-dropdown-link">
                                <!--i class="el-icon-user" style="font-size: 16px; vertical-align: middle;"></i-->
                                <?php echo $my->name; ?>
                                <i class="el-icon-arrow-down el-icon--right"></i>
                            </span>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item icon="el-icon-switch-button">
                                    <el-link href="<?php echo beAdminUrl('System.AdminUserLogin.logout'); ?>" :underline="false">退出登录</el-link>
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                    </div>

                </div>
            </div>
            <script>
                var vueNorth = new Vue({
                    el: '#app-north',
                    data: {
                        defaultActive: "north-menu-<?php echo $adminMenuActiveMenuKey; ?>",
                        aboutModel: false
                    },
                    methods: {
                        toggleStyle: function (style) {
                            this.$cookies.set("be-admin-north-menu-style", style);
                            window.location.reload();
                        }
                    }
                });
            </script>
        </be-north>


        <be-west>
            <div id="app-west" :class="{'be-west-collapse': collapse}" v-cloak>
                <?php
                /*
                <div class="logo">
                    <a href="<?php echo beAdminUrl(); ?>"></a>
                </div>
                 */
                ?>

                <div class="logo">
                    <a href="<?php echo beAdminUrl(); ?>">
                        <?php
                        $configTheme = \Be\Be::getConfig('AdminTheme.System.Theme');
                        if ($configTheme->logo !== '') {
                            echo '<img src="' . $configTheme->logo . '">';
                        } else {
                            ?>
                            <svg viewBox="0 0 200 60" xmlns="http://www.w3.org/2000/svg">
                                <rect rx="5" height="40" width="40" x="10" y="10" fill="#ff5c35"/>
                                <path d="M16 29 L21 29 M21 42 L16 42 L16 17 L21 17 C30 17 30 29 21 29 C30 30 30 42 21 42 M45 17 L34 17 L34 42 L46 42 M35 29 L44 29" stroke="#ffffff" stroke-width="2" fill="none" />
                                <text x="65" y="28" style="font-size: 14px;"><tspan fill="#ff5c35">B</tspan><tspan fill="#999999">eyond</tspan></text>
                                <text x="90" y="42" style="font-size: 14px;"><tspan fill="#ff5c35">E</tspan><tspan fill="#999999">xception</tspan></text>
                            </svg>
                            <?php
                        }
                        ?>
                    </a>
                </div>

                <div class="menu">
                    <?php
                    $adminMenu = \Be\Be::getAdminMenu();
                    $adminMenuTree = $adminMenu->getTree();
                    $adminMenuActiveMenuKey = $adminMenu->getActiveMenuKey();
                    ?>
                    <el-menu
                            background-color="#30354d"
                            text-color="#aaa"
                            active-text-color="#fff"
                            :default-active="activeIndex"
                            :collapse="collapse"
                            :collapse-transition="false">
                        <?php
                        $appName = \Be\Be::getRequest()->getAppName();
                        foreach ($adminMenuTree as $item) {

                            if ($item->id === $appName) {

                                $hasSubItem = false;
                                if (isset($item->subItems) && is_array($item->subItems) && count($item->subItems) > 0) {
                                    $hasSubItem = true;
                                }

                                // 有子菜单
                                if ($hasSubItem) {
                                    foreach ($item->subItems as $subItem) {

                                        $hasSubSubItem = false;
                                        if (isset($subItem->subItems) && is_array($subItem->subItems) && count($subItem->subItems) > 0) {
                                            $hasSubSubItem = true;
                                        }

                                        if ($hasSubSubItem) {
                                            echo '<el-submenu index="west-menu-'.$subItem->id.'" popper-class="be-west-popup-menu">';
                                            echo '<template slot="title">';
                                            echo '<i class="'.$subItem->icon.'"></i>';
                                            echo '<span>'.$subItem->label.'</span>';
                                            echo '</template>';

                                            foreach ($subItem->subItems as $subSubItem) {
                                                echo '<el-menu-item index="west-menu-'.$subSubItem->id.'">';
                                                echo '<template slot="title">';
                                                echo '<el-link href="'.$subSubItem->url.'" icon="'.$subSubItem->icon.'" :underline="false">';
                                                echo $subSubItem->label;
                                                echo '</el-link>';
                                                echo '</template>';
                                                echo '</el-menu-item>';
                                            }

                                            echo '</el-submenu>';
                                        } else {
                                            echo '<el-menu-item index="west-menu-'.$subItem->id.'">';
                                            echo '<template slot="title">';
                                            echo '<el-link href="'.$subItem->url.'" icon="'.$subItem->icon.'" :underline="false">';
                                            echo $subItem->label;
                                            echo '</el-link>';
                                            echo '</template>';
                                            echo '</el-menu-item>';
                                        }

                                    }
                                }
                                break;
                            }
                        }
                        ?>
                    </el-menu>
                </div>

                <div class="toggle" @click="toggleMenu">
                    <i :class="collapse ?'el-icon-s-unfold': 'el-icon-s-fold'"></i>
                </div>
            </div>
            <script>
                let westCollapseKey = 'be-admin-west-collapse';
                let vueWest = new Vue({
                    el: '#app-west',
                    data : {
                        activeIndex: "west-menu-<?php echo $adminMenuActiveMenuKey; ?>",
                        collapse: this.$cookies.isKey(westCollapseKey) && this.$cookies.get(westCollapseKey) === '1'
                    },
                    methods: {
                        toggleMenu: function (e) {
                            this.collapse = !this.collapse;
                            console.log(this.collapse);
                            document.getElementById("be-west").style.width = this.collapse ? "64px" : "200px";
                            document.getElementById("be-north").style.left = this.collapse ? "64px" : "200px";
                            document.getElementById("be-middle").style.marginLeft = this.collapse ? "64px" : "200px";
                            this.$cookies.set(westCollapseKey, this.collapse ? '1' : '0', 86400 * 180);
                        }
                    }
                });
            </script>
        </be-west>

        <be-middle>
            <be-center>
            </be-center>
        </be-middle>
    </be-body>

    <div id="app-be" v-cloak>
        <el-dialog
                class="be-dialog"
                :title="dialog.title"
                :visible.sync="dialog.visible"
                :width="dialog.width"
                :close-on-click-modal="false"
                :destroy-on-close="true">
            <iframe id="frame-be-dialog" name="frame-be-dialog" :src="dialog.url" :style="{width:'100%',height:dialog.height,border:0}"></iframe>
        </el-dialog>

        <el-drawer
                class="be-drawer"
                :visible.sync="drawer.visible"
                :size="drawer.width"
                :title="drawer.title"
                :wrapper-closable="false"
                :destroy-on-close="true">
            <div style="padding:0 10px;height: 100%;overflow:hidden;">
                <iframe id="frame-be-drawer" name="frame-be-drawer" :src="drawer.url" style="width:100%;height:100%;border:0;"></iframe>
            </div>
        </el-drawer>
    </div>
    <script src="<?php echo $adminThemeWwwUrl; ?>/js/theme.js?v=20220716"></script>

</body>
</html>
</be-html>