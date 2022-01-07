<be-head>
    <script src="<?php echo \Be\Be::getProperty('App.System')->getUrl(); ?>/Template/Admin/ThemeEditor/js/sortable/Sortable.min.js"></script>
    <script src="<?php echo \Be\Be::getProperty('App.System')->getUrl(); ?>/Template/Admin/ThemeEditor/js/vuedraggable/vuedraggable.umd.min.js"></script>

    <style>
        body {
            padding: 0;
            margin: 0;
            font-size: 14px;
        }
        #app {
            display:grid;
            grid-template-areas: "north north north"
                    "west center east";
            grid-template-rows: auto 1fr;
            grid-template-columns: 250px 1fr 250px;
            height: 100vh;
        }

        .west-links,
        .west-links-disabled {
            padding: 0;
            margin: 0;
        }

        .middle-west-links {
            padding: 2px 0 5px 0;
            margin: 5px 0 2px 0;
            border-top: #eee 1px solid;
            border-bottom: #eee 1px solid;
        }

        .west-links li,
        .west-links-disabled li {
            list-style: none;
            margin-top: 3px;
            padding: 0 5px;
        }

        .west-links li li {
            padding: 0;
        }

        .west-links li a {
            height: 30px;
            line-height: 30px;
            display: block;
            padding: 0 3px;
            color: #333;
            text-decoration: none;
        }
        .west-links-disabled li a {
            height: 30px;
            line-height: 30px;
            display: block;
            padding: 0 3px;
            color: #999;
            text-decoration: none;
        }


        .west-links li:hover {
            background-color: #fafafa;
        }

        .west-links li a:hover {
            background-color: #eee;
        }

        .west-links li a.active {
            background-color: rgba(242, 247, 254, 1);
        }

        .west-links li a .icon,
        .west-links-disabled li a .icon,
        .el-dropdown-menu .icon{
            padding-right: 5px;
        }

        .west-links-disabled li a .icon { color: #999;}

        .west-links li a .icon img,
        .west-links li a .icon svg,
        .west-links-disabled li a .icon img,
        .west-links-disabled li a .icon svg,
        .el-dropdown-menu .icon img,
        .el-dropdown-menu .icon svg  {
            max-width: 14px;
            max-height: 14px;
            vertical-align: middle;
        }

        .west-links-disabled li a .icon img,
        .west-links-disabled li a .icon svg {
            opacity: 0.4;
        }

        .el-dropdown-menu__item {
            white-space: nowrap;
            padding-right: 30px;
        }

        .west-links li ul{
            padding: 0 0 0 35px;
            margin: 0;
        }

        .west-links li .el-dropdown {
            cursor: pointer;
            height: 30px;
            line-height: 30px;
            padding: 0 3px;
        }

        .west-links li .el-dropdown:hover {
            background-color: #eee;
        }

        .west-links li .close-icon,
        .west-links li .drag-icon{
            display: none;
        }

        .west-links li:hover .close-icon,
        .west-links li:hover .drag-icon{
            display: block;
        }

        .west-links li li .item-close-icon,
        .west-links li li .item-drag-icon{
            display: none;
        }

        .west-links li li:hover .item-close-icon,
        .west-links li li:hover .item-drag-icon{
            display: block;
        }

        .preview-desktop {
            width: 100%;
        }

        .preview-mobile {
            width: 375px;
            margin: 0 auto;
        }

        .el-dropdown-link {
            color: rgba(44, 110, 203, 1);
        }

    </style>
</be-head>

<be-body>
    <div id="app" v-cloak>

        <header style="grid-area: north;z-index: 9;">

            <div style="display: flex; padding: 5px 10px; box-shadow: 0 0 2px 2px #eee; ">
                <div style="flex: 0 0 auto;">
                    <h1 style="margin: 0; font-size: 1.2rem; line-height: 40px;">{{theme.property.label}}（{{theme.name}}）</h1>
                </div>

                <div style="flex: 1 1 auto; text-align: center;">
                    <el-select v-model="pageName" placeholder="请选择" @change="changePage">
                        <el-option v-for="page in theme.pages" :key="page.name" :label="page.label" :value="page.name"></el-option>
                    </el-select>
                </div>

                <div style="flex: 0 0 auto;">
                    <?php if ($this->themeType === 'Theme') { ?>
                    <el-dropdown @command="toggleScreen">
                        <el-button size="medium" style="border: none">
                            <template v-if="screen === 'mobile'">
                                <i class="el-icon-mobile-phone" style="font-size: 1.5rem;"></i>
                            </template>
                            <template v-else-if="screen === 'desktop'">
                                <i class="el-icon-s-platform" style="font-size: 1.5rem;"></i>
                            </template>
                        </el-button>

                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item command="mobile"><i class="el-icon-mobile-phone" style="font-size: 1.2rem;"></i> 手机版</el-dropdown-item>
                            <el-dropdown-item command="desktop"><i class="el-icon-s-platform" style="font-size: 1.2rem;"></i> 桌面版</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                    <?php } ?>
                </div>

            </div>
        </header>

        <aside style="grid-area: west; position: relative;">
            <div style="position: absolute; left:0; right:0 ;top: 0; bottom: 30px; overflow-y: auto;">
                <h2 style="padding: 10px 0 10px 20px; margin: 0; border-bottom: #eee 1px solid; font-size: 1rem;">
                    {{page.page.label}}
                </h2>

                <?php
                foreach (array_keys($this->theme['property']->pages[$this->pageName]['sections']) as $sectionType) {
                    $key = $sectionType . 'Sections';
                    if (!isset($this->page[$key]) && isset($this->pageHome[$key])) {
                        ?>
                        <ul class="west-links-disabled <?php echo $sectionType; ?>-west-links">
                            <?php
                            foreach ($this->pageHome[$key] as $section) {
                                ?>
                                <li>
                                    <div style="display: flex">
                                        <div style="flex: 0 0 20px; height: 30px; line-height: 30px;">
                                            <?php
                                            if (isset($section['items']) && $section['items']) {
                                                ?>
                                                <a href="javascript:void(0);">
                                                    <i class="el-icon-caret-right"></i>
                                                </a>
                                                <?php
                                            }
                                            ?>
                                        </div>

                                        <div style="flex: 1">
                                            <a href="javascript:void(0);">
                                                <span class="icon"><?php echo $section['icon']; ?></span><?php echo $section['label']; ?>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                            ?>
                            <li style="padding-left: 28px;">
                                <el-tooltip>
                                    <div slot="content">默认继承使用了首页的配置，<br>如果当前页面需要自定义样式，<br>请先点击右侧链接启用</div>
                                    <i class="el-icon-question" style="color: #999;"></i>
                                </el-tooltip>

                                <el-link style="display: inline" href="<?php echo beAdminUrl('System.'.$this->themeType.'.enableSectionType', ['themeName' => $this->themeName, 'pageName' => $this->pageName, 'sectionType' => $sectionType]); ?>">启用自定义配置</el-link>
                            </li>
                        </ul>
                        <?php
                        continue;
                    }
                    ?>
                    <ul class="west-links <?php echo $sectionType; ?>-west-links">
                        <draggable v-model="page.<?php echo $sectionType; ?>Sections" handle=".drag-icon" force-fallback="true" group="<?php echo $sectionType; ?>" animation="100" @update="sectionDragUpdate">
                            <transition-group>
                                <li v-for="(section, sectionKey) in page.<?php echo $sectionType; ?>Sections" :key="sectionKey" data-sectiontype="<?php echo $sectionType; ?>">
                                    <div style="display: flex">
                                        <div style="flex: 0 0 20px; height: 30px; line-height: 30px;">
                                            <a v-if="section.items" href="javascript:void(0);" @click="toggleSection('<?php echo $sectionType; ?>', sectionKey)">
                                                <i :class="toggle['<?php echo $sectionType; ?>'][sectionKey] ? 'el-icon-caret-bottom' : 'el-icon-caret-right'"></i>
                                            </a>
                                        </div>

                                        <div style="flex: 1">
                                            <a href="javascript:void(0);" @click="editItem(section.url, '<?php echo $sectionType; ?>', sectionKey)" :class="activeUrl === section.url ? 'active' : ''">
                                                <span class="icon" v-html="section.icon"></span>{{section.label}}
                                            </a>
                                        </div>

                                        <div class="close-icon" style="flex: 0 0 20px; height: 30px; line-height: 30px;">
                                            <a href="javascript:void(0);" @click="deleteSection('<?php echo $sectionType; ?>', sectionKey)">
                                                <i class="el-icon-close"></i>
                                            </a>
                                        </div>
                                        <div class="drag-icon" style="flex: 0 0 20px; height: 30px; line-height: 30px;">
                                            <a href="javascript:void(0);">
                                                <i class="el-icon-sort"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <ul v-if="section.items && toggle['<?php echo $sectionType; ?>'][sectionKey]">
                                        <template v-if="section.items.existItems">
                                            <draggable v-model="section.items.existItems" :disabled="!section.items.newItems" handle=".item-drag-icon" force-fallback="true" :group="'<?php echo $sectionType; ?>' + sectionKey" animation="100" @update="sectionItemDragUpdate">
                                                <transition-group>
                                                    <li v-for="(existItem, existItemKey) in section.items.existItems" :key="existItemKey" data-sectiontype="<?php echo $sectionType; ?>" :data-sectionkey="sectionKey">
                                                        <div style="display: flex">
                                                            <div style="flex: 1">
                                                                <a href="javascript:void(0);" @click="editItem(existItem.url, '<?php echo $sectionType; ?>', sectionKey)" :class="activeUrl === existItem.url ? 'active' : ''">
                                                                    <span class="icon" v-html="existItem.icon"></span>{{existItem.label}}
                                                                </a>
                                                            </div>

                                                            <div class="item-close-icon" style="flex: 0 0 20px; height: 30px; line-height: 30px;">
                                                                <a v-if="section.items.newItems" href="javascript:void(0);" @click="deleteSectionItem('<?php echo $sectionType; ?>', sectionKey, existItemKey)">
                                                                    <i class="el-icon-close"></i>
                                                                </a>
                                                            </div>
                                                            <div class="item-drag-icon" style="flex: 0 0 20px; height: 30px; line-height: 30px;">
                                                                <a href="javascript:void(0);">
                                                                    <i class="el-icon-sort"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </transition-group>
                                            </draggable>

                                        </template>

                                        <li v-if="section.items.newItems">
                                            <el-dropdown @command="addSectionItem">
                                                <span class="el-dropdown-link">
                                                    <i class="el-icon-circle-plus"></i>
                                                    {{section.items.labelNewItem}}
                                                </span>
                                                <el-dropdown-menu slot="dropdown">
                                                    <el-dropdown-item v-for="(newItem, newItemKey) in section.items.newItems" :command="newItem.url">
                                                        <span class="icon" v-html="newItem.icon"></span>
                                                        {{newItem.label}}
                                                    </el-dropdown-item>
                                                </el-dropdown-menu>
                                            </el-dropdown>
                                        </li>
                                    </ul>
                                </li>

                            </transition-group>
                        </draggable>

                        <li style="padding-left: 25px;">
                            <el-dropdown @command="addSection">
                                <span class="el-dropdown-link">
                                    <i class="el-icon-circle-plus"></i>
                                    新增组件
                                </span>
                                <el-dropdown-menu slot="dropdown">
                                    <el-dropdown-item v-for="(section, sectionKey) in page.<?php echo $sectionType; ?>SectionsAvailable" :command="'<?php echo $sectionType; ?>-' + section.name">
                                        <span class="icon" v-html="section.icon"></span>{{section.label}}
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                        </li>

                        <?php
                        if ($this->pageName !== 'Home' && isset($this->page[$sectionType . 'Extended']) && $this->page[$sectionType . 'Extended']) {
                            ?>
                            <li style="padding-left: 28px;">
                                <el-tooltip>
                                    <div slot="content">移除自定义配置后，将默认继承使用了首页的配置。</div>
                                    <i class="el-icon-question" style="color: #999;"></i>
                                </el-tooltip>

                                <el-link style="display: inline" href="<?php echo beAdminUrl('System.'.$this->themeType.'.disableSectionType', ['themeName' => $this->themeName, 'pageName' => $this->pageName, 'sectionType' => $sectionType]); ?>">移除自定义配置</el-link>
                            </li>
                            <?php
                        }
                        ?>

                        </ul>
                    <?php
                }
                ?>

            </div>

            <div style="position: absolute;left:0; right:0; bottom:0; height: 35px; line-height: 35px; background-color: #fff; ">
                <ul class="west-links">
                    <li>
                        <a href="javascript:void(0);" @click="editItem(theme.url, '', '')" :class="activeUrl === theme.url ? 'active' : ''" style=" padding-left: 20px;">
                            主题参数配置
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <main style="grid-area: center; background-color: #fafafa; padding: 15px;">
            <div :class="'preview-' + this.screen" style="height: 100%; box-shadow: 0 0 2px 2px #eee">
                <iframe :src="previewUrl" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </main>

        <aside style="grid-area: east; position: relative;">
            <div style="position: absolute; left:0; right:0 ;top: 0; bottom: 0;">
                <iframe name="frame-setting" id="frame-setting" :src="activeUrl" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </aside>

    </div>


    <?php
    $toggle = [];
    foreach (array_keys($this->theme['property']->pages[$this->pageName]['sections']) as $sectionType) {
        if (isset($this->page[$sectionType.'Sections'])) {
            foreach ($this->page[$sectionType.'Sections'] as $sectionKey => $sectionName) {
                $toggle[$sectionType][$sectionKey] = 0;
            }
        }
    }
    ?>

    <script>
        Vue.component('vuedraggable', window.vuedraggable);

        var vue = new Vue({
            el: '#app',
            components: {
                vuedraggable: window.vuedraggable,//当前页面注册组件
            },
            data: {
                theme : <?php echo json_encode($this->theme); ?>,
                pageName : "<?php echo $this->pageName; ?>",
                page : <?php echo json_encode($this->page); ?>,
                toggle: <?php echo json_encode($toggle); ?>,
                activeUrl: "<?php echo beAdminUrl('System.'.$this->themeType.'.editSectionItem', ['themeName' => $this->themeName, 'pageName' => $this->pageName, 'sectionType' => $this->sectionType, 'sectionKey' => $this->sectionKey, 'sectionName' => $this->sectionName, 'itemKey' => $this->itemKey, 'itemName' => $this->itemName]); ?>",
                previewUrl: "<?php echo $this->page['desktopPreviewUrl']; ?>",
                previewUrlTag: "",
                screen: "desktop"
            },
            methods: {
                toggleScreen: function (command) {
                    this.screen = command;
                    switch (this.screen) {
                        case "desktop":
                            this.previewUrl = this.page.desktopPreviewUrl + this.previewUrlTag;
                            break;
                        case "mobile":
                            this.previewUrl = this.page.mobilePreviewUrl + this.previewUrlTag;
                            break;
                    }
                },
                reloadPreviewFrame: function() {
                    var randomParam;
                    switch (this.screen) {
                        case "desktop":
                            randomParam = (this.page.desktopPreviewUrl.indexOf('?') === -1 ? '?_=' : '&_=') + Math.random();
                            this.previewUrl = this.page.desktopPreviewUrl + randomParam + this.previewUrlTag;
                            break;
                        case "mobile":
                            randomParam = (this.page.mobilePreviewUrl.indexOf('?') === -1 ? '?_=' : '&_=') + Math.random();
                            this.previewUrl = this.page.mobilePreviewUrl + randomParam + this.previewUrlTag;
                            break;
                    }
                },
                changePage: function(pageName) {
                    var page;
                    for(var x in this.theme.pages) {
                        page = this.theme.pages[x];
                        if (page.name === pageName) {
                            window.location.href = page.url;
                            break;
                        }
                    }
                },
                toggleSection: function(sectionType, sectionKey) {
                    this.toggle[sectionType][sectionKey] = !this.toggle[sectionType][sectionKey];
                    this.$forceUpdate();
                    //console.log(this.page);
                },
                addSection: function(command) {
                    var loading = this.$loading();
                    var commands = command.split("-");

                    var _this = this;
                    _this.$http.post("<?php echo beAdminUrl('System.'.$this->themeType.'.addSection', ['themeName' => $this->themeName, 'pageName' => $this->pageName]); ?>", {
                        sectionType: commands[0],
                        sectionName: commands[1]
                    }).then(function (response) {
                        loading.close();
                        if (response.status === 200) {
                            if (response.data.success) {
                                _this.page = response.data.page;
                            } else {
                                _this.$message.error(response.data.message);
                            }
                        }
                    }).catch(function (error) {
                        loading.close();
                        _this.$message.error(error);
                    });
                },
                deleteSection: function(sectionType, sectionKey) {
                    var _this = this;
                    this.$confirm('确定要删除组件吗?', '删除确认', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(function () {
                        var loading = _this.$loading();
                        _this.$http.post("<?php echo beAdminUrl('System.'.$this->themeType.'.deleteSection', ['themeName' => $this->themeName, 'pageName' => $this->pageName]); ?>", {
                            sectionType: sectionType,
                            sectionKey: sectionKey
                        }).then(function (response) {
                            loading.close();
                            if (response.status === 200) {
                                if (response.data.success) {
                                    _this.page = response.data.page;
                                } else {
                                    _this.$message.error(response.data.message);
                                }
                            }
                        }).catch(function (error) {
                            loading.close();
                            _this.$message.error(error);
                        });
                    }).catch(function () {
                    });
                },
                sectionDragUpdate: function(event) {
                    //console.log(event);
                    //return;
                    var loading = this.$loading();

                    var _this = this;
                    _this.$http.post("<?php echo beAdminUrl('System.'.$this->themeType.'.sortSection', ['themeName' => $this->themeName, 'pageName' => $this->pageName]); ?>", {
                        sectionType: event.item.dataset.sectiontype,
                        oldIndex: event.oldIndex,
                        newIndex: event.newIndex,
                    }).then(function (response) {
                        loading.close();
                        if (response.status === 200) {
                            if (response.data.success) {
                                _this.page = response.data.page;
                            } else {
                                _this.$message.error(response.data.message);
                            }
                        }
                    }).catch(function (error) {
                        loading.close();
                        _this.$message.error(error);
                    });
                },
                editItem: function(url, sectionType, sectionKey) {
                    this.activeUrl = url;

                    var previewUrlTag = "#be-section-" + sectionType + "-" + sectionKey;
                    if (this.previewUrlTag !== previewUrlTag) {
                        this.previewUrlTag = previewUrlTag;
                        this.reloadPreviewFrame();
                    }
                },
                addSectionItem:  function (command) {
                    var loading = this.$loading();

                    var _this = this;
                    _this.$http.get(command).then(function (response) {
                        loading.close();
                        if (response.status === 200) {
                            if (response.data.success) {
                                _this.page = response.data.page;
                            } else {
                                _this.$message.error(response.data.message);
                            }
                        }
                    }).catch(function (error) {
                        loading.close();
                        _this.$message.error(error);
                    });

                    console.log(command);
                },
                deleteSectionItem: function(sectionType, sectionKey, itemKey) {
                    var _this = this;
                    this.$confirm('确定要删除子组件吗?', '删除确认', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(function () {
                        var loading = _this.$loading();
                        _this.$http.post("<?php echo beAdminUrl('System.'.$this->themeType.'.deleteSectionItem', ['themeName' => $this->themeName, 'pageName' => $this->pageName]); ?>", {
                            sectionType: sectionType,
                            sectionKey: sectionKey,
                            itemKey: itemKey
                        }).then(function (response) {
                            loading.close();
                            if (response.status === 200) {
                                if (response.data.success) {
                                    _this.page = response.data.page;
                                } else {
                                    _this.$message.error(response.data.message);
                                }
                            }
                        }).catch(function (error) {
                            loading.close();
                            _this.$message.error(error);
                        });
                    }).catch(function () {
                    });
                },
                sectionItemDragUpdate: function(event) {
                    console.log(event);
                    //return;
                    var loading = this.$loading();

                    var _this = this;
                    _this.$http.post("<?php echo beAdminUrl('System.'.$this->themeType.'.sortSectionItem', ['themeName' => $this->themeName, 'pageName' => $this->pageName]); ?>", {
                        sectionType: event.item.dataset.sectiontype,
                        sectionKey: event.item.dataset.sectionkey,
                        oldIndex: event.oldIndex,
                        newIndex: event.newIndex,
                    }).then(function (response) {
                        loading.close();
                        if (response.status === 200) {
                            if (response.data.success) {
                                _this.page = response.data.page;
                            } else {
                                _this.$message.error(response.data.message);
                            }
                        }
                    }).catch(function (error) {
                        loading.close();
                        _this.$message.error(error);
                    });
                }
            }
        });
        
        function reloadPreviewFrame () {
            vue.reloadPreviewFrame();
        }
    </script>

</be-body>