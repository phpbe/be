<be-head>

    <script src="<?php echo \Be\Be::getProperty('App.System')->getUrl(); ?>/Template/Admin/js/sortable/Sortable.min.js"></script>
    <script src="<?php echo \Be\Be::getProperty('App.System')->getUrl(); ?>/Template/Admin/js/vuedraggable/vuedraggable.umd.min.js"></script>

    <style>

        .menu-items-header {
            color: #666;
            background-color: #EBEEF5;
            height: 3rem;
            line-height: 3rem;
            margin-bottom: .5rem;
        }

        .menu-items {

        }

        .menu-items .el-form-item {
            margin-bottom: 0;
        }

        .menu-items .el-form-item.is-error {
            margin-bottom: 1rem;
        }

        .menu-item {
            background-color: #fff;
            border-bottom: #EBEEF5 1px solid;
            padding-top: .5rem;
            padding-bottom: .5rem;
            margin-bottom: 2px;
        }

        .menu-item-col-name {
            padding-left: 10px;
        }

        .menu-item-col-link {
            width: 300px;
            text-align: center;
        }

        .menu-item-col-target {
            width: 160px;
            padding-left: 20px;
            padding-right: 20px;
            text-align: center;
        }

        .menu-item-col-op {
            width: 200px;
            text-align: right;
        }

        .menu-item-level-1 {
            margin-left: 0;
        }

        .menu-item-level-2 {
            margin-left: 35px;
        }

        .menu-item-level-3 {
            margin-left: 70px;
        }

        .menu-item-ghost {
            border: #ccc 1px dashed !important;
            background-color: #fafafa !important;
        }

        .menu-item-chosen {
        }

        .menu-item-drag {
        }

        .menu-item-drag-icon {
            color: #ccc;
            font-size: 20px;
            padding-top: .25rem;
            padding-right: 1rem;
            cursor: move;
        }

        .menu-item-drag-icon:hover {
            color: #409EFF;
        }

    </style>
</be-head>


<be-north>
    <div class="be-north" id="be-north">
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
            el: '#be-north',
            data: {
                loading: false,
            }
        });
    </script>
</be-north>


<be-center>
    <div id="app" v-cloak>
        <div class="be-center">
            <div class="be-center-title"><?php echo $this->title; ?></div>

            <div class="be-center-box">
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
                                                    v-model="menuItem.description"
                                                    size="medium"
                                                    @change="((val)=>{setMenuLink(val, menuItemIndex)})"
                                            >
                                                <el-option value="None" label="无"></el-option>
                                                <el-option value="Home" label="首页"></el-option>
                                                <el-option value="Url" label="指定网址"></el-option>
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

            </div>
        </div>


        <el-drawer
                :visible.sync="drawer.visible"
                :size="drawer.width"
                :title="drawer.title"
                :wrapper-closable="false"
                :destroy-on-close="true">
            <div style="padding:0 10px;height: 100%;">
                <iframe :src="drawer.url" style="width:100%;height:100%;border:0;"></iframe>
            </div>
        </el-drawer>

    </div>

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
                        description: "未选择链接",
                        target: "_self",
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
                        description: "未选择链接",
                        target: "_self",
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
                            menuItem.description = "未选择链接";
                            break;
                        case "Home":
                            menuItem.route = "System.Home.index";
                            menuItem.params = {};
                            menuItem.url = "";
                            menuItem.description = "首页";
                            break;
                        case "ProductCategory":
                            menuItem.route = "";
                            menuItem.params = {};
                            menuItem.url = "";
                            menuItem.description = "未选择链接";

                            this.setMenuLinkIndex = menuItemIndex;
                            this.setMenuLinkChooseCategory();
                            /*
                            menuItem.route = "ShopFai.Product.category";
                            menuItem.params = {};
                            menuItem.url = "";
                            menuItem.description = "商品分类：";
                             */
                            break;
                        case "ProductDetail":
                            menuItem.route = "";
                            menuItem.params = {};
                            menuItem.url = "";
                            menuItem.description = "未选择链接";

                            this.setMenuLinkIndex = menuItemIndex;
                            this.setMenuLinkChooseProduct();
                            /*
                            menuItem.route = "ShopFai.Product.detail";
                            menuItem.params = {};
                            menuItem.url = "";
                            menuItem.description = "商品详情页：";
                             */
                            break;
                        case "ProductGuessYouLike":
                            menuItem.route = "ShopFai.Product.guessYouLike";
                            menuItem.params = {};
                            menuItem.url = "";
                            menuItem.description = "猜你喜欢商品列表";
                            break;
                        case "ProductNewProducts":
                            menuItem.route = "ShopFai.Product.newProducts";
                            menuItem.params = {};
                            menuItem.url = "";
                            menuItem.description = "新品列表";
                            break;
                        case "ProductTopSales":
                            menuItem.route = "ShopFai.Product.topSales";
                            menuItem.params = {};
                            menuItem.url = "";
                            menuItem.description = "热销商品列表";
                            break;
                        case "Url":
                            menuItem.route = "";
                            menuItem.params = {};
                            menuItem.url = "";
                            menuItem.description = "未选择链接";

                            this.setMenuLinkIndex = menuItemIndex;
                            this.setMenuLinkUrl();
                            /*
                            menuItem.route = "";
                            menuItem.params = {};
                            menuItem.url = "";
                            menuItem.description = "指定网址：";
                            */
                            break;
                    }
                    //this.formData.menuItems[menuItemIndex] = menuItem;
                },
                setMenuLinkChooseProduct:function () {
                    this.drawer.url = "<?php echo beAdminUrl('System.Menu.chooseProduct'); ?>";
                    this.drawer.title = "选择商品";
                    this.drawer.width = "80%";
                    this.drawer.visible = true;
                },
                setMenuLinkChooseCategory:function () {
                    this.drawer.url = "<?php echo beAdminUrl('System.Menu.chooseCategory'); ?>";
                    this.drawer.title = "选择分类";
                    this.drawer.width = "600";
                    this.drawer.visible = true;
                },
                setMenuLinkUrl:function () {
                    this.drawer.url = "<?php echo beAdminUrl('System.Menu.setUrl'); ?>";
                    this.drawer.title = "指定网址";
                    this.drawer.width = "400";
                    this.drawer.visible = true;
                },
                setMenuLinkSubmit: function (menuItemSubmit) {
                    let menuItem = this.formData.menuItems[this.setMenuLinkIndex];
                    menuItem.route = menuItemSubmit.route;
                    menuItem.params = menuItemSubmit.params;
                    menuItem.url = menuItemSubmit.url;
                    menuItem.description = menuItemSubmit.description;

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

</be-center>