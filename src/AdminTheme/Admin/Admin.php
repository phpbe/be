<be-html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo $this->title; ?></title>

    <?php
    $beUrl = beUrl();
    $configTheme = \Be\Be::getConfig('AdminTheme.Admin.Theme');
    $themeUrl = \Be\Be::getProperty('AdminTheme.Admin')->getUrl();
    ?>
    <base href="<?php echo $beUrl; ?>/" >
    <script>var beUrl = "<?php echo $beUrl; ?>"; </script>

    <?php
    if ($configTheme->localAssetLib) {
        ?>
        <script src="<?php echo $themeUrl; ?>/js/jquery-1.12.4.min.js"></script>

        <script src="<?php echo $themeUrl; ?>/js/vue-2.6.11.min.js"></script>

        <script src="<?php echo $themeUrl; ?>/js/axios-0.19.0.min.js"></script>
        <script>Vue.prototype.$http = axios;</script>

        <script src="<?php echo $themeUrl; ?>/js/vue-cookies-1.5.13.js"></script>

        <link rel="stylesheet" href="<?php echo $themeUrl; ?>/css/element-ui-2.15.7.css">
        <script src="<?php echo $themeUrl; ?>/js/element-ui-2.15.7.js"></script>

        <link rel="stylesheet" href="<?php echo $themeUrl; ?>/css/font-awesome-4.7.0.min.css" />
        <?php
    } else {
        ?>
        <script src="https://unpkg.com/jquery@1.12.4/dist/jquery.min.js"></script>

        <script src="https://unpkg.com/vue@2.6.11/dist/vue.min.js"></script>

        <script src="https://unpkg.com/axios@0.19.0/dist/axios.min.js"></script>
        <script>Vue.prototype.$http = axios;</script>

        <script src="https://unpkg.com/vue-cookies@1.5.13/vue-cookies.js"></script>

        <link rel="stylesheet" href="https://unpkg.com/element-ui@2.15.7/lib/theme-chalk/index.css">
        <script src="https://unpkg.com/element-ui@2.15.7/lib/index.js"></script>

        <link rel="stylesheet" href="https://unpkg.com/font-awesome@4.7.0/css/font-awesome.min.css" />
        <?php
    }
    ?>

    <link rel="stylesheet" href="<?php echo $beUrl; ?>/vendor/be/scss/src/be.css" />

    <link rel="stylesheet" href="<?php echo $themeUrl; ?>/css/theme.css?v=20220608" />
    <be-head>
    </be-head>
</head>
<body>
    <be-body>
    <div class="be-body">

        <be-north>
            <div class="be-north" id="be-north" v-cloak>

                <div class="be-row">
                    <div class="be-col be-pl-200">
                        <?php
                        $adminMenu = \Be\Be::getAdminMenu();
                        $adminMenuTree = $adminMenu->getTree();
                        $adminMenuActiveMenuKey = $adminMenu->getActiveMenuKey();
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

                                        $hasSubSubItem = false;
                                        if (isset($subItem->subItems) && is_array($subItem->subItems) && count($subItem->subItems) > 0) {
                                            $hasSubSubItem = true;
                                        }

                                        if ($hasSubSubItem) {
                                            foreach ($subItem->subItems as $subSubItem) {
                                                echo '<el-menu-item index="north-menu-'.$subSubItem->id.'">';
                                                echo '<el-link href="'.$subSubItem->url.'" icon="'.$subSubItem->icon.'" :underline="false">';
                                                echo $subSubItem->label;
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
                        if ($my->avatar === '') {
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
                                    <el-link href="<?php echo beAdminUrl('System.AdminUserLogin.logout'); ?>" :underline="false">退出登录</el-link>
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                    </div>

                </div>
            </div>
            <script>
                var vueNorth = new Vue({
                    el: '#be-north',
                    data: {
                        defaultActive: "north-menu-<?php echo $adminMenuActiveMenuKey; ?>",
                        aboutModel: false
                    },
                    methods: {

                    }
                });
            </script>
        </be-north>


        <be-west>
            <div id="app-west" :class="{'be-west': true, 'be-west-collapse': collapse}" v-cloak>
                <?php
                /*
                <div class="logo">
                    <a href="<?php echo beAdminUrl(); ?>"></a>
                </div>
                 */
                ?>

                <div class="logo">
                    <div class="be-row">
                        <div class="be-col-auto">
                            <a href="<?php echo beAdminUrl(); ?>">
                                <svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                                    <rect rx="5" height="40" width="40" y="0" x="0" fill="#ff6600"/>
                                    <path d="M6 19 L11 19 M11 32 L6 32 L6 7 L11 7 C20 7 20 19 11 19 C20 20 20 32 11 32 M35 7 L24 7 L24 32 L36 32 M25 19 L34 19" stroke="#ffffff" stroke-width="2" fill="none" />
                                </svg>
                            </a>
                        </div>
                        <div class="be-col">
                            <div class="logo-text-1"><span>B</span>eyond</div>
                            <div class="logo-text-2"><span>E</span>xception</div>
                        </div>
                    </div>
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
                                        echo '<el-submenu index="west-menu-'.$subItem->id.'" popper-class="be-west-popup-menu">';

                                        echo '<template slot="title">';
                                        echo '<i class="'.$subItem->icon.'"></i>';
                                        echo '<span>'.$subItem->label.'</span>';
                                        echo '</template>';

                                        $hasSubSubItem = false;
                                        if (isset($subItem->subItems) && is_array($subItem->subItems) && count($subItem->subItems) > 0) {
                                            $hasSubSubItem = true;
                                        }

                                        if ($hasSubSubItem) {
                                            foreach ($subItem->subItems as $subSubItem) {
                                                echo '<el-menu-item index="west-menu-'.$subSubItem->id.'">';
                                                echo '<template slot="title">';
                                                echo '<el-link href="'.$subSubItem->url.'" icon="'.$subSubItem->icon.'" :underline="false">';
                                                echo $subSubItem->label;
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
            <script>
                var sWestMenuCollapseKey = '_westMenuCollapse';
                var vueWestMenu = new Vue({
                    el: '#app-west',
                    data : {
                        activeIndex: "west-menu-<?php echo $adminMenuActiveMenuKey; ?>",
                        collapse: this.$cookies.isKey(sWestMenuCollapseKey) && this.$cookies.get(sWestMenuCollapseKey) === '1'
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
    <script src="<?php echo $themeUrl; ?>/js/theme.js?v=20200221"></script>

</body>
</html>
</be-html>