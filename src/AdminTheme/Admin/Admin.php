<be-html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo $this->title; ?></title>

    <?php
    $beUrl = beUrl();
    ?>
    <base href="<?php echo $beUrl; ?>/" >
    <script>var beUrl = "<?php echo $beUrl; ?>"; </script>

    <script src="https://cdn.jsdelivr.net/npm/jquery@1.12.4/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="<?php echo $beUrl; ?>/vendor/be/scss/src/be.css" />

    <?php
    $themeUrl = \Be\Be::getProperty('AdminTheme.Admin')->getUrl();
    ?>
    <script src="<?php echo $themeUrl; ?>/js/vue-2.6.11.min.js"></script>

    <script src="<?php echo $themeUrl; ?>/js/axios-0.19.0.min.js"></script>
    <script>Vue.prototype.$http = axios;</script>

    <script src="<?php echo $themeUrl; ?>/js/vue-cookies-1.5.13.js"></script>

    <link rel="stylesheet" href="<?php echo $themeUrl; ?>/css/element-ui-2.15.7-f60.css">
    <script src="<?php echo $themeUrl; ?>/js/element-ui-2.15.7.js"></script>

    <link rel="stylesheet" href="<?php echo $themeUrl; ?>/css/font-awesome-4.7.0.min.css" />

    <link rel="stylesheet" href="<?php echo $themeUrl; ?>/css/theme.css" />
    <be-head>
    </be-head>
</head>
<body>
    <be-body>
    <div class="be-body">

        <be-north>
            <div class="be-north" id="be-north" v-cloak>

                <div class="be-row">
                    <div class="be-col be-pl-50">
                        <?php
                        $adminMenu = \Be\Be::getAdminMenu();
                        $adminMenuTree = $adminMenu->getMenuTree()
                        ?>
                        <el-menu
                                mode="horizontal"
                                :default-active="defaultActive">
                            <?php
                            foreach ($adminMenuTree as $menu) {

                                // 有子菜单
                                if ($menu->subMenu) {
                                    echo '<el-submenu index="north-menu-'.$menu->id.'" popper-class="be-north-popup-menu">';

                                    echo '<template slot="title">';
                                    if ($menu->url) {
                                        echo '<el-link href="'.$menu->url.'" icon="'.$menu->icon.'" :underline="false" style="display:inline !important;">';
                                        echo $menu->label;
                                        echo '</el-link>';
                                    } else {
                                        echo '<i class="'.$menu->icon.'"></i>';
                                        echo '<span>'.$menu->label.'</span>';
                                    }
                                    echo '</template>';

                                    foreach ($menu->subMenu as $subMenu) {
                                        echo '<el-submenu index="north-menu-'.$subMenu->id.'" popper-class="be-north-popup-menu">';

                                        echo '<template slot="title">';
                                        if ($subMenu->url) {
                                            echo '<el-link href="'.$subMenu->url.'" icon="'.$subMenu->icon.'" :underline="false">';
                                            echo $subMenu->label;
                                            echo '</el-link>';
                                        } else {
                                            echo '<i class="'.$subMenu->icon.'"></i>';
                                            echo '<span>'.$subMenu->label.'</span>';
                                        }
                                        echo '</template>';

                                        if ($subMenu->subMenu) {
                                            foreach ($subMenu->subMenu as $subSubMenu) {
                                                echo '<el-menu-item index="north-menu-'.$subSubMenu->id.'">';
                                                echo '<el-link href="'.$subSubMenu->url.'" icon="'.$subSubMenu->icon.'" :underline="false">';
                                                echo $subSubMenu->label;
                                                echo '</el-link>';
                                                echo '</el-menu-item>';
                                            }
                                        }
                                        echo '</el-submenu>';
                                    }
                                    echo '</el-submenu>';
                                }
                            }
                            ?>
                        </el-menu>
                    </div>

                    <div class="be-col-auto be-pr-150 north-links lh-60">
                        <el-link href="http://www.phpbe.com/" icon="el-icon-warning-outline" target="_blank" :underline="false">技术支持</el-link>
                    </div>

                    <div class="be-col-auto be-pr-150 lh-60">
                        <el-link href="<?php echo $beUrl ?>" icon="el-icon-view" target="_blank" :underline="false">预览网站</el-link>
                    </div>

                    <?php
                    $configUser = \Be\Be::getConfig('App.System.AdminUser');
                    $my = \Be\Be::getAdminUser();
                    ?>
                    <div class="be-col-auto be-pr-30 north-links lh-60">
                        <img src="<?php
                        if ($my->avatar == '') {
                            echo \Be\Be::getProperty('App.System')->getUrl().'/Template/Admin/AdminUser/images/avatar.png';
                        } else {
                            echo \Be\Be::getRequest()->getUploadUrl().'/System/AdminUser/Avatar/'.$my->avatar;
                        }
                        ?>" alt="<?php echo $my->name; ?>" style="max-width:24px;max-height:24px; vertical-align: middle" >
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
                                    <el-link href="<?php echo beAdminUrl('System.AdminUser.logout'); ?>" :underline="false">退出登录</el-link>
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                    </div>

                </div>
            </div>

        </be-north>


        <be-west>
            <div id="app-west" :class="{'be-west': true, 'be-west-collapse': collapse}" v-cloak>
                <div class="logo">
                    <a href="<?php echo beAdminUrl(); ?>"></a>
                </div>

                <div class="menu">
                    <?php
                    $adminMenu = \Be\Be::getAdminMenu();
                    $adminMenuTree = $adminMenu->getMenuTree()
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
                        foreach ($adminMenuTree as $menu) {

                            if ($menu->id == $appName) {
                                // 有子菜单
                                if ($menu->subMenu) {
                                    foreach ($menu->subMenu as $subMenu) {
                                        echo '<el-submenu index="west-menu-'.$subMenu->id.'" popper-class="be-west-popup-menu">';

                                        echo '<template slot="title">';
                                        echo '<i class="'.$subMenu->icon.'"></i>';
                                        echo '<span>'.$subMenu->label.'</span>';
                                        echo '</template>';

                                        if ($subMenu->subMenu) {
                                            foreach ($subMenu->subMenu as $subSubMenu) {
                                                echo '<el-menu-item index="west-menu-'.$subSubMenu->id.'">';
                                                echo '<template slot="title">';
                                                echo '<el-link href="'.$subSubMenu->url.'" icon="'.$subSubMenu->icon.'" :underline="false">';
                                                echo $subSubMenu->label;
                                                echo '</el-link>';
                                                echo '</template>';
                                                echo '</el-menu-item>';
                                            }
                                        }
                                        echo '</el-submenu>';
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
        </be-west>


        <be-middle>
            <div class="be-middle" id="be-middle">
                <be-center>
                    <div class="be-center">
                        <div class="be-center-title"><?php echo $this->title; ?></div>
                        <div class="be-center-body"><be-center-body></be-center-body></div>
                    </div>
                </be-center>
            </div>
        </be-middle>
    </div>

    <script>
        <?php
        $menuKey = \Be\Be::getRequest()->getRoute();
        ?>
        var vueNorth = new Vue({
            el: '#be-north',
            data: {
                defaultActive: "north-menu-<?php echo $menuKey; ?>",
                aboutModel: false
            },
            methods: {

            }
        });


        var sWestMenuCollapseKey = '_westMenuCollapse';
        var vueWestMenu = new Vue({
            el: '#app-west',
            data : {
                activeIndex: "west-menu-<?php echo $menuKey; ?>",
                collapse: this.$cookies.isKey(sWestMenuCollapseKey) && this.$cookies.get(sWestMenuCollapseKey) == '1'
            },
            methods: {
                toggleMenu: function (e) {
                    this.collapse = !this.collapse;
                    console.log(this.collapse);
                    document.getElementById("be-north").style.left = this.collapse ? "64px" : "200px";
                    document.getElementById("be-middle").style.left = this.collapse ? "64px" : "200px";
                    this.$cookies.set(sWestMenuCollapseKey, this.collapse ? '1' : '0', 86400 * 180);
                }
            },
            created: function () {
                if (this.collapse) {
                    document.getElementById("be-north").style.left = "64px";
                    document.getElementById("be-middle").style.left = "64px";
                }
            }
        });

    </script>

    </be-body>
</body>
</html>
</be-html>