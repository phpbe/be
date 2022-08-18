<be-head>
    <?php
    $appSystemWwwUrl = \Be\Be::getProperty('App.System')->getWwwUrl();
    ?>
    <script src="<?php echo $appSystemWwwUrl; ?>/lib/sortable/sortable.min.js"></script>
    <script src="<?php echo $appSystemWwwUrl; ?>/lib/vuedraggable/vuedraggable.umd.min.js"></script>
    <link rel="stylesheet" href="<?php echo $appSystemWwwUrl; ?>/admin/menu/css/items.css" type="text/css"/>
</be-head>


<be-north>
    <div id="app-north">
        <div class="be-row">
            <div class="be-col">
                <div style="padding: 1.25rem 0 0 2rem;">
                    <el-link icon="el-icon-back" href="<?php echo beAdminUrl('System.Menu.menus'); ?>">返回菜单导航</el-link>
                </div>
            </div>
            <div class="be-col-auto">
                <div style="padding: .75rem 2rem 0 0;">
                    <el-button size="medium" :disabled="loading" @click="vueCenter.cancel();">取消</el-button>
                    <el-button size="medium" type="primary" :disabled="loading" @click="vueCenter.save();">保存</el-button>
                </div>
            </div>
        </div>
    </div>
    <script>
        let vueNorth = new Vue({
            el: '#app-north',
            data: {
                loading: false,
            }
        });
    </script>
</be-north>


<be-page-content>
    <div class="be-bc-fff be-px-100 be-pt-100 be-pb-50" id="app" v-cloak>
        <el-form ref="menuItemsFormRef" :model="formData">

            <div class="be-row menu-items-header">
                <div class="be-col">
                    <div class="menu-item-col-name be-fw-bold">
                        名称
                    </div>
                </div>
                <div class="be-col-auto">
                    <div class="menu-item-col-link be-fw-bold">
                        链接
                    </div>
                </div>
                <div class="be-col-auto">
                    <div class="menu-item-col-target be-fw-bold">
                        打开方式
                    </div>
                </div>
                <div class="be-col-auto">
                    <div class="menu-item-col-is-enable be-fw-bold">
                        是否启用
                    </div>
                </div>
                <div class="be-col-auto">
                    <div class="menu-item-col-op be-fw-bold be-pr-400">
                        操作
                    </div>
                </div>
            </div>

            <div class="menu-items">
                <draggable
                        v-model="formData.menuItems"
                        ghost-class="menu-item-ghost"
                        chosen-class="menu-item-chosen"
                        drag-class="menu-item-drag"
                        handle=".menu-item-drag-icon"
                        force-fallback="true"
                        animation="100"
                        @start="dragStart"
                        :move="dragMove"
                        @update="dragUpdate"
                        @end="dragEnd"
                >
                    <transition-group>
                        <div
                                :class="{'be-row': true, 'menu-item': true, 'menu-item-level-2': menuItem.level===2, 'menu-item-level-3': menuItem.level===3, 'menu-item-ghost': dragIndexFrom===menuItemIndex}"
                                v-for="menuItem, menuItemIndex in formData.menuItems"
                                :key="menuItem.id">
                            <div class="be-col">
                                <div class="menu-item-col-name">
                                    <div class="be-row">
                                        <div class="be-col-auto menu-item-drag-icon">
                                            <i class="el-icon-rank"></i>
                                        </div>

                                        <div class="be-col">
                                            <el-form-item
                                                    :key="menuItem.id"
                                                    :prop="'menuItems.' + menuItemIndex + '.name'"
                                                    :rules="{required:true,message:'请输入名称',trigger:'blur'}">

                                                <el-input
                                                        style="min-width:200px;max-width:400px;width:80%;"
                                                        type="text"
                                                        placeholder="名称"
                                                        v-model="menuItem.name"
                                                        size="medium"
                                                        maxlength="60">
                                                </el-input>
                                            </el-form-item>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="be-col-auto">
                                <div class="menu-item-col-link">
                                    <el-select
                                            style="width:100%;"
                                            v-model="menuItem.selectedValue"
                                            size="medium"
                                            @change="(val)=>{setMenuLink(val, menuItemIndex);}"
                                    >
                                        <el-option-group key="common" label="通用">
                                            <el-option style="padding-left: 2.5rem;" value="None" label="无"></el-option>
                                            <el-option style="padding-left: 2.5rem;" value="Home" label="默认首页"></el-option>
                                            <el-option style="padding-left: 2.5rem;" value="Url" label="指定网址"></el-option>
                                        </el-option-group>
                                        <?php
                                        foreach ($this->menuPickers as $menuPicker)
                                        {
                                            ?>
                                            <el-option-group key="<?php echo $menuPicker['app']->name; ?>" label="<?php echo $menuPicker['app']->label; ?>">
                                                <?php
                                                foreach ($menuPicker['menuPickers'] as $picker)
                                                {
                                                    ?>
                                                    <el-option style="padding-left: 2.5rem;" value="<?php echo $picker['route']; ?>" label="<?php echo $picker['label']; ?>"></el-option>
                                                    <?php
                                                }
                                                ?>
                                            </el-option-group>
                                            <?php
                                        }
                                        ?>
                                    </el-select>
                                </div>
                            </div>

                            <div class="be-col-auto">
                                <div class="menu-item-col-target">
                                    <el-select
                                            style="width:100%;"
                                            v-model="menuItem.target"
                                            size="medium">
                                        <el-option value="_self" label="当前页面"></el-option>
                                        <el-option value="_blank" label="新窗口"></el-option>
                                    </el-select>
                                </div>
                            </div>

                            <div class="be-col-auto">
                                <div class="menu-item-col-is-enable be-pt-50">
                                    <el-switch v-model.number="menuItem.is_enable" :active-value="1" :inactive-value="0" size="medium"></el-switch>
                                </div>
                            </div>

                            <div class="be-col-auto">
                                <div class="menu-item-col-op be-pt-25 be-pr-50">
                                    <template v-if="menuItem.level === 1">
                                        <el-link type="primary" class="be-mr-100" @click="addChildMenuItem(menuItemIndex)">添加二级菜单项</el-link>
                                    </template>

                                    <template v-else-if="menuItem.level === 2">
                                        <el-link type="primary" class="be-mr-100" @click="addChildMenuItem(menuItemIndex)">添加三级菜单项</el-link>
                                    </template>

                                    <el-link type="danger" @click="removeMenuItem(menuItemIndex)">删除</el-link>
                                </div>
                            </div>
                        </div>
                    </transition-group>
                </draggable>
            </div>
        </el-form>

        <div class="be-my-100">
            <el-button type="primary" size="medium" icon="el-icon-plus" @click="addMenuItem">新增菜单项</el-button>
        </div>

        <el-drawer
                :visible.sync="drawer.visible"
                :size="drawer.width"
                :title="drawer.title"
                :wrapper-closable="false"
                :destroy-on-close="true">
            <div style="padding:0 20px;height: 100%;overflow:hidden;">
                <iframe :src="drawer.url" style="width:100%;height:100%;border:0;"></iframe>
            </div>
        </el-drawer>

    </div>

    <?php
    foreach ($this->flatTree as &$item) {
        if (strpos($item['description'], '指定网址：') === 0) {
            $item['selectedValue'] = $item['description'];
        } else {
            if ($item['route'] === '') {
                if ($item['url'] === '') {
                    $item['selectedValue'] = 'None';
                } elseif ($item['url'] === '/') {
                    $item['selectedValue'] = 'Home';
                } else {
                    $item['selectedValue'] = 'Url';
                }
            } else {
                if ($item['params']) {
                    $item['selectedValue'] = $item['description'];
                } else {
                    $item['selectedValue'] = $item['route'];
                }
            }
        }
    }
    unset($item);
    ?>

    <script>
        Vue.component('vuedraggable', window.vuedraggable);

        let vueCenter = new Vue({
            el: '#app',
            components: {
                vuedraggable: window.vuedraggable,//当前页面注册组件
            },
            data: {
                loading: false,
                formData: {
                    menuItems: <?php echo json_encode($this->flatTree); ?>,
                },

                menuPickers: <?php echo json_encode($this->menuPickers); ?>,

                dragTimer : null,
                dragIndexFrom : null,
                dragIndexTo : null,

                setMenuLinkIndex: null,

                drawer: {visible: false, width: "40%", title: "", url: "about:blank"},

                t: false
            },
            methods: {
                save: function () {
                    let _this = this;
                    this.$refs["menuItemsFormRef"].validate(function (valid) {
                        if (valid) {
                            _this.loading = true;
                            vueNorth.loading = true;
                            _this.$http.post("<?php echo beAdminUrl('System.Menu.items', ['id' => $this->menu->id]); ?>", {
                                formData: _this.formData
                            }).then(function (response) {
                                _this.loading = false;
                                vueNorth.loading = false;
                                //console.log(response);
                                if (response.status === 200) {
                                    let responseData = response.data;
                                    if (responseData.success) {
                                        _this.$message.success(responseData.message);
                                        setTimeout(function () {
                                            window.onbeforeunload = null;
                                            window.location.href = "<?php echo beAdminUrl('System.Menu.menus'); ?>";
                                        }, 1000);
                                    } else {
                                        if (responseData.message) {
                                            _this.$message.error(responseData.message);
                                        } else {
                                            _this.$message.error("服务器返回数据异常！");
                                        }
                                    }
                                }
                            }).catch(function (error) {
                                _this.loading = false;
                                vueNorth.loading = false;
                                _this.$message.error(error);
                            });
                        } else {
                            return false;
                        }
                    });
                },
                cancel: function () {
                    window.onbeforeunload = null;
                    window.location.href = "<?php echo beAdminUrl('System.Menu.menus'); ?>";
                },
                addMenuItem: function () {
                    this.formData.menuItems.push({
                        id: "-" + (this.formData.menuItems.length - 1),
                        parent_id: "",
                        name: "",
                        route: "",
                        params: {},
                        url: "",
                        description: "无",
                        target: "_self",
                        is_enable: 1,
                        level: 1
                    });
                },
                addChildMenuItem: function (menuItemIndex) {
                    let menuItem = this.formData.menuItems[menuItemIndex];
                    this.formData.menuItems.splice(menuItemIndex + 1, 0, {
                        id: "-" + (this.formData.menuItems.length - 1),
                        parent_id: menuItem.id,
                        name: "",
                        route: "",
                        params: {},
                        url: "",
                        description: "无",
                        target: "_self",
                        is_enable: 1,
                        level: menuItem.level + 1
                    });
                },
                removeMenuItem: function (menuItemIndex) {
                    this.formData.menuItems.splice(menuItemIndex, 1);

                    this.updateMenuItems();
                },
                setMenuLink:function (val, menuItemIndex) {
                    let menuItem = this.formData.menuItems[menuItemIndex];

                    switch (val) {
                        case "None":
                            menuItem.route = "";
                            menuItem.params = {};
                            menuItem.url = "";
                            menuItem.description = "无";
                            menuItem.selectedValue = 'None';
                            break;
                        case "Home":
                            menuItem.route = "";
                            menuItem.params = {};
                            menuItem.url = "/";
                            menuItem.description = "默认首页";
                            menuItem.selectedValue = 'Home';
                            break;
                        case "Url":
                            menuItem.selectedValue = menuItem.description;

                            this.setMenuLinkIndex = menuItemIndex;
                            this.setMenuLinkUrl();
                            break;
                        default:
                            let app;
                            let menuPicker;
                            l1:for (let x of this.menuPickers) {
                                for (let xx of x.menuPickers) {
                                    if (xx.route === val) {
                                        app = x.app;
                                        menuPicker = xx;
                                        break l1;
                                    }
                                }
                            }

                            if (menuPicker.hasMenuPicker === 1) {
                                menuItem.selectedValue = menuItem.description;

                                this.setMenuLinkIndex = menuItemIndex;
                                this.setMenuLinkPicker(app, menuPicker);
                            } else {
                                menuItem.route = menuPicker.route;
                                menuItem.params = {};
                                menuItem.url = "";
                                menuItem.description = app.label + "：" + menuPicker.label;

                                menuItem.selectedValue = menuItem.description;
                            }
                    }
                    //this.formData.menuItems[menuItemIndex] = menuItem;
                },
                setMenuLinkPicker:function (app, menuPicker) {
                    let url = "<?php echo beAdminUrl('System.Menu.picker'); ?>";
                    url += url.indexOf("?") === -1 ? "?" : "&";
                    url += "pickerRoute=" +menuPicker.route;
                    this.drawer.url = url;
                    this.drawer.title = app.label + "：" + menuPicker.label;
                    this.drawer.width = "80%";
                    this.drawer.visible = true;
                },
                setMenuLinkUrl:function () {
                    this.drawer.url = "<?php echo beAdminUrl('System.Menu.setUrl'); ?>";
                    this.drawer.title = "指定网址";
                    this.drawer.width = "600px";
                    this.drawer.visible = true;
                },

                setMenuLinkSubmit: function (menuItemSubmit) {
                    let menuItem = this.formData.menuItems[this.setMenuLinkIndex];
                    menuItem.route = menuItemSubmit.route;
                    menuItem.params = menuItemSubmit.params;
                    menuItem.url = menuItemSubmit.url;
                    menuItem.description = menuItemSubmit.description;
                    menuItem.selectedValue = menuItem.description;

                    this.setMenuLinkIndex = null;
                    this.drawer.visible = false;
                },

                dragStart(e) {
                    //console.log("dragStart", e);

                    this.dragIndexFrom = e.oldIndex;
                    this.dragIndexTo = e.oldIndex;

                    if (this.dragTimer !== null) {
                        clearInterval(this.dragTimer);
                    }

                    let dragMenuItem = this.formData.menuItems[this.dragIndexFrom];
                    let dragLevelFrom = dragMenuItem.level;

                    let _this = this;
                    this.dragTimer = setInterval(function () {
                        let matrix = _this.$refs.menuItemsFormRef.$el.getElementsByClassName('menu-item-drag')[0].style.transform;
                        //console.log("matrix", matrix);
                        // matrix matrix(1, 0, 0, 1, 28.1166, 0.457153)
                        if (matrix.substr(0, 6) === "matrix") {
                            let offsetX = matrix.match(/,\s*([\-\.|0-9]+),\s*[\-\.|0-9]+\)/)[1];
                            //console.log("offsetX", offsetX);

                            // 拖动到的目标层级
                            let dragLevelTo = dragLevelFrom;

                            if (offsetX >= 60) {
                                dragLevelTo += 2;
                            } else if (offsetX >= 30) {
                                dragLevelTo += 1;
                            } else if (offsetX <= -60) {
                                dragLevelTo -= 2;
                            } else if (offsetX <= -30) {
                                dragLevelTo -= 1;
                            }

                            if (dragLevelTo < 1) {
                                dragLevelTo = 1;
                            }

                            if (dragLevelTo > 3) {
                                dragLevelTo = 3;
                            }

                            if (_this.dragIndexTo > 0) {
                                let preMenuItem = _this.formData.menuItems[_this.dragIndexTo - 1];
                                if (dragLevelTo > preMenuItem.level + 1) {
                                    dragLevelTo = preMenuItem.level + 1;
                                }
                            } else {
                                dragLevelTo = 1;
                            }

                            //console.log("dragLevelTo", dragLevelTo);

                            dragMenuItem.level = dragLevelTo;
                        }

                    }, 200);
                },
                dragMove(e, originalEvent){
                    //console.log("dragMove", e, originalEvent);
                    this.dragIndexTo = e.draggedContext.futureIndex;
                },
                dragUpdate(e){
                    //console.log("onUpdate", e);
                },
                dragEnd(e){
                    // console.log("onEnd", e);
                    clearInterval(this.dragTimer);

                    this.dragTimer = null;
                    this.dragIndexFrom = null;
                    this.dragIndexTo = null;

                    this.updateMenuItems();
                },

                updateMenuItems() {
                    let level1Item = null;
                    let level2Item = null;
                    let preItem = null;
                    let item = null;

                    for (let i=0, len = this.formData.menuItems.length; i<len; i++) {
                        item = this.formData.menuItems[i];
                        if (preItem === null) {
                            item.level = 1;
                        } else {
                            // 子类不得比前一个父类层级大1
                            if (item.level > preItem.level + 1) {
                                item.level = preItem.level + 1;
                            }
                        }

                        if (item.level === 1) {
                            item.parent_id = "";
                            level1Item = item;
                        } else if (item.level === 2) {
                            item.parent_id = level1Item.id;
                            level2Item = item;
                        } else if (item.level === 3) {
                            item.parent_id = level2Item.id;
                        }

                        preItem = item;
                    }
                },

                t: function () {
                }
            },
            mounted: function () {
                window.onbeforeunload = function(e) {
                    e = e || window.event;
                    if (e) {
                        e.returnValue = "";
                    }
                    return "";
                }
            }
        });


        function setMenuLink(menuItem) {
            vueCenter.setMenuLinkSubmit(menuItem);
        }
    </script>

</be-page-content>