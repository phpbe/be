<be-head>
    <style type="text/css">

        .be-center .el-dropdown-link {
            cursor: pointer;
            color: #409eff;
        }

        .el-table__row .el-divider__text,
        .el-table .el-link {
            margin-left: 4px;
            margin-right: 4px;
        }

        .el-table th.el-table__cell {
            color: #666;
            background-color: #EBEEF5;
        }

        .el-table__cell .el-avatar,
        .el-table__cell .el-image {
            display: block;
        }

    </style>
</be-head>


<be-center>
    <?php
    $js = [];
    $css = [];
    $formData = [];
    $vueData = [];
    $vueMethods = [];
    $vueHooks = [];
    ?>
    <div id="app" v-cloak>
        <div class="be-center-body">
            <el-form<?php
            $formUi = [
                ':inline' => 'true',
                'size' => 'medium',
            ];
            if (isset($this->setting['grid']['form']['ui'])) {
                $formUi = array_merge($formUi, $this->setting['grid']['form']['ui']);
            }

            foreach ($formUi as $k => $v) {
                if ($v === null) {
                    echo ' ' . $k;
                } else {
                    echo ' ' . $k . '="' . $v . '"';
                }
            }
            ?>>
                <?php
                if (isset($this->setting['grid']['headnote'])) {
                    echo $this->setting['grid']['headnote'];
                }

                if (isset($this->setting['grid']['form']['items']) && count($this->setting['grid']['form']['items']) > 0) {
                    ?>
                    <div id="form-items" ref="formItemsRef">
                        <?php
                        foreach ($this->setting['grid']['form']['items'] as $item) {
                            $driverClass = null;
                            if (isset($item['driver'])) {
                                if (substr($item['driver'], 0, 8) === 'FormItem') {
                                    $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $item['driver'];
                                } else {
                                    $driverClass = $item['driver'];
                                }
                            } else {
                                $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
                            }
                            $driver = new $driverClass($item);

                            echo $driver->getHtml();

                            if ($driver->name !== null) {
                                $formData[$driver->name] = $driver->getValueString();
                            }

                            $jsX = $driver->getJs();
                            if ($jsX) {
                                $js = array_merge($js, $jsX);
                            }

                            $cssX = $driver->getCss();
                            if ($cssX) {
                                $css = array_merge($css, $cssX);
                            }

                            $vueDataX = $driver->getVueData();
                            if ($vueDataX) {
                                $vueData = \Be\Util\Arr::merge($vueData, $vueDataX);
                            }

                            $vueMethodsX = $driver->getVueMethods();
                            if ($vueMethodsX) {
                                $vueMethods = array_merge($vueMethods, $vueMethodsX);
                            }

                            $vueHooksX = $driver->getVueHooks();
                            if ($vueHooksX) {
                                foreach ($vueHooksX as $k => $v) {
                                    if (isset($vueHooks[$k])) {
                                        $vueHooks[$k] .= "\r\n" . $v;
                                    } else {
                                        $vueHooks[$k] = $v;
                                    }
                                }
                            }
                        }

                        if (isset($this->setting['grid']['form']['actions']) && count($this->setting['grid']['form']['actions']) > 0) {
                            $html = '';
                            foreach ($this->setting['grid']['form']['actions'] as $key => $item) {
                                if ($key === 'submit') {
                                    if ($item) {
                                        if ($item === true) {
                                            $html .= '<el-button type="primary" icon="el-icon-search" @click="search" :disabled="loading">查询</el-button> ';
                                            continue;
                                        } elseif (is_string($item)) {
                                            $html .= '<el-button type="primary" icon="el-icon-search" @click="search" :disabled="loading">' . $item . '</el-button> ';
                                            continue;
                                        }
                                    } else {
                                        continue;
                                    }
                                }

                                $driverClass = null;
                                if (isset($item['driver'])) {
                                    if (substr($item['driver'], 0, 10) === 'FormAction') {
                                        $driverClass = '\\Be\\AdminPlugin\\Form\\Action\\' . $item['driver'];
                                    } else {
                                        $driverClass = $item['driver'];
                                    }
                                } else {
                                    $driverClass = \Be\AdminPlugin\Form\Action\FormActionButton::class;
                                }
                                $driver = new $driverClass($item);

                                $html .= $driver->getHtml() . ' ';

                                $vueDataX = $driver->getVueData();
                                if ($vueDataX) {
                                    $vueData = \Be\Util\Arr::merge($vueData, $vueDataX);
                                }

                                $vueMethodsX = $driver->getVueMethods();
                                if ($vueMethodsX) {
                                    $vueMethods = array_merge($vueMethods, $vueMethodsX);
                                }
                            }

                            if ($html) {
                                echo '<el-form-item>' . $html . '</el-form-item>';
                            }
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>

                <el-table<?php
                $tableUi = [
                    ':data' => 'gridData',
                    'ref' => 'tableRef',
                    'v-loading' => 'loading',
                    'size' => 'medium',
                    ':height' => 'tableHeight',
                    ':default-sort' => '{prop:orderBy,order:orderByDir}',
                    'highlight-current-row' => 'true',
                    '@sort-change' => 'sort',
                    '@row-click' => 'selectRow',
                ];
                if (isset($this->setting['grid']['table']['ui'])) {
                    $tableUi = array_merge($tableUi, $this->setting['grid']['table']['ui']);
                }

                foreach ($tableUi as $k => $v) {
                    if ($v === null) {
                        echo ' ' . $k;
                    } else {
                        echo ' ' . $k . '="' . $v . '"';
                    }
                }
                ?>>
                    <template slot="empty">
                        <?php
                        if (isset($this->setting['grid']['table']['empty']) && is_string($this->setting['grid']['table']['empty'])) {
                            echo $this->setting['grid']['table']['empty'];
                        } else {
                            echo '<el-empty description="暂无数据"></el-empty>';
                        }
                        ?>
                    </template>

                    <el-table-column
                            align="center"
                            header-align="center"
                            label=""
                            width="60">
                        <template scope="scope">
                            <el-radio :label="scope.row.<?php echo $this->setting['name']; ?>" v-model="selectedValue" @change.native="selectRow(scope.row)">&nbsp</el-radio>
                        </template>
                    </el-table-column>

                    <?php
                    foreach ($this->setting['grid']['table']['items'] as $item) {

                        $driverClass = null;
                        if (isset($item['driver'])) {
                            if (substr($item['driver'], 0, 9) === 'TableItem') {
                                $driverClass = '\\Be\\AdminPlugin\\Table\\Item\\' . $item['driver'];
                            } else {
                                $driverClass = $item['driver'];
                            }
                        } else {
                            $driverClass = \Be\AdminPlugin\Table\Item\TableItemText::class;
                        }
                        $driver = new $driverClass($item);

                        echo $driver->getHtml();

                        $vueDataX = $driver->getVueData();
                        if ($vueDataX) {
                            $vueData = \Be\Util\Arr::merge($vueData, $vueDataX);
                        }

                        $vueMethodsX = $driver->getVueMethods();
                        if ($vueMethodsX) {
                            $vueMethods = array_merge($vueMethods, $vueMethodsX);
                        }
                    }
                    ?>
                </el-table>
                <?php

                if (isset($this->setting['grid']['footnote'])) {
                    echo $this->setting['grid']['footnote'];
                }
                ?>

                <div class="be-row be-mt-50">
                    <div class="be-col be-ta-center">
                        <el-pagination
                                v-if="total > 0"
                                @size-change="changePageSize"
                                @current-change="gotoPage"
                                :current-page="page"
                                :page-sizes="[10, 15, 20, 25, 30, 50, 100, 200, 500]"
                                :page-size="pageSize"
                                layout="total, sizes, prev, pager, next, jumper"
                                :total="total">
                        </el-pagination>
                    </div>
                    <div class="be-col-auto">
                        <el-button type="primary" icon="el-icon-check" @click="selectRowConfirm" :disabled="selectedValue === ''">确定</el-button>
                    </div>
                </div>

            </el-form>
        </div>

    </div>

    <?php
    if (isset($this->setting['grid']['js'])) {
        $js = array_merge($js, $this->setting['grid']['js']);
    }

    if (isset($this->setting['grid']['css'])) {
        $css = array_merge($css, $this->setting['grid']['css']);
    }

    if (isset($this->setting['grid']['vueData'])) {
        $vueData = \Be\Util\Arr::merge($vueData, $this->setting['grid']['vueData']);
    }

    if (isset($this->setting['grid']['vueMethods'])) {
        $vueMethods = \Be\Util\Arr::merge($vueMethods, $this->setting['grid']['vueMethods']);
    }

    if (isset($this->setting['grid']['vueHooks'])) {
        foreach ($this->setting['grid']['vueHooks'] as $k => $v) {
            if (isset($vueHooks[$k])) {
                $vueHooks[$k] .= "\r\n" . $v;
            } else {
                $vueHooks[$k] = $v;
            }
        }
    }

    if (count($js) > 0) {
        $js = array_unique($js);
        foreach ($js as $x) {
            echo '<script src="' . $x . '"></script>';
        }
    }

    if (count($css) > 0) {
        $css = array_unique($css);
        foreach ($css as $x) {
            echo '<link rel="stylesheet" href="' . $x . '">';
        }
    }
    ?>

    <script>
        var pageSizeKey = "<?php echo 'url:' . md5($this->url); ?>:pageSize";
        var pageSize = localStorage.getItem(pageSizeKey);
        if (pageSize === null || isNaN(pageSize)) {
            pageSize = <?php echo $this->pageSize; ?>;
        } else {
            pageSize = Number(pageSize);
        }

        var vueGrid = new Vue({
            el: '#app',
            data: {
                formData: <?php echo json_encode($formData); ?>,
                gridData: [],
                orderBy: "",
                orderByDir: "",
                pageSize: pageSize,
                page: 1,
                pages: 1,
                total: 0,
                loading: false,
                tableHeight: 500,

                selectedRow: null,
                selectedValue: "",

                t: false<?php
                if ($vueData) {
                    foreach ($vueData as $k => $v) {
                        echo ',' . $k . ':' . json_encode($v);
                    }
                }
                ?>
            },
            methods: {
                search: function () {
                    this.page = 1;
                    this.loadGridData();
                },
                selectRow(row) {
                    this.selectedRow = row;
                    this.selectedValue = row.<?php echo $this->setting['name']; ?>;
                },

                selectRowConfirm() {
                    let description = "<?php echo $this->setting['value']; ?>";
                    for (let x in this.selectedRow) {
                        description = description.replace("{" + x + "}", this.selectedRow[x]);
                    }
                    if (description.length > 15) {
                        description = description.substr(0, 15) +  "...";
                    }

                    parent.setMenuLink({
                        route: "<?php echo $this->route; ?>",
                        params: {
                            <?php echo $this->setting['name']; ?>: this.selectedValue
                        },
                        url: "",
                        description: "<?php echo $this->app->label; ?>：" + description
                    });
                },

                loadGridData: function () {
                    this.loading = true;
                    var _this = this;
                    _this.$http.post("<?php echo $this->setting['grid']['form']['action']; ?>", {
                        formData: _this.formData,
                        orderBy: _this.orderBy,
                        orderByDir: _this.orderByDir,
                        page: _this.page,
                        pageSize: _this.pageSize
                    }).then(function (response) {
                        _this.loading = false;
                        //console.log(response);
                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                _this.total = parseInt(responseData.data.total);
                                _this.gridData = responseData.data.gridData;
                                _this.pages = Math.floor(_this.total / _this.pageSize);
                            } else {
                                _this.total = 0;
                                _this.gridData = [];
                                _this.page = 1;
                                _this.pages = 1;

                                if (responseData.message) {
                                    _this.$message({
                                        showClose: true,
                                        message: responseData.message,
                                        type: 'error'
                                    });
                                }
                            }
                            _this.resize();
                        }
                    }).catch(function (error) {
                        _this.loading = false;
                        _this.$message.error(error);
                    });
                },
                changePageSize: function (pageSize) {
                    this.pageSize = pageSize;
                    this.page = 1;
                    localStorage.setItem(pageSizeKey, pageSize);
                    this.loadGridData();
                },
                gotoPage: function (page) {
                    this.page = page;
                    this.loadGridData();
                },
                sort: function (option) {
                    if (option.order === "ascending" || option.order === "descending") {
                        this.orderBy = option.prop;
                        this.orderByDir = option.order === "ascending" ? "ASC" : "DESC";
                    } else {
                        this.orderBy = "";
                        this.orderByDir = "";
                    }
                    this.loadGridData();
                },
                resize: function () {
                    let offset = this.total > 0 ? 60 : 15;
                    let rect = this.$refs.tableRef.$el.getBoundingClientRect();
                    this.tableHeight = Math.max(document.documentElement.clientHeight - rect.top - offset, 100);
                }
                <?php
                if ($vueMethods) {
                    foreach ($vueMethods as $k => $v) {
                        echo ',' . $k . ':' . $v;
                    }
                }
                ?>
            },
            created: function () {
                this.search();
                <?php
                if (isset($vueHooks['created'])) {
                    echo $vueHooks['created'];
                }
                ?>
            },
            mounted: function () {
                this.$nextTick(function () {
                    this.resize();
                    let _this = this;
                    window.onresize = function () {
                        _this.resize();
                    };
                });

                <?php
                if (isset($vueHooks['mounted'])) {
                    echo $vueHooks['mounted'];
                }
                ?>
            },
            updated: function () {
                let _this = this;
                this.$nextTick(function () {
                    _this.$refs.tableRef.doLayout();
                });

                <?php
                if (isset($vueHooks['updated'])) {
                    echo $vueHooks['updated'];
                }
                ?>
            }
            <?php
            if (isset($vueHooks['beforeCreate'])) {
                echo ',beforeCreate: function () {' . $vueHooks['beforeCreate'] . '}';
            }

            if (isset($vueHooks['beformMount'])) {
                echo ',beformMount: function () {' . $vueHooks['beformMount'] . '}';
            }

            if (isset($vueHooks['beforeUpdate'])) {
                echo ',beforeUpdate: function () {' . $vueHooks['beforeUpdate'] . '}';
            }


            if (isset($vueHooks['beforeDestroy'])) {
                echo ',beforeDestroy: function () {' . $vueHooks['beforeDestroy'] . '}';
            }

            if (isset($vueHooks['destroyed'])) {
                echo ',destroyed: function () {' . $vueHooks['destroyed'] . '}';
            }
            ?>
        });
    </script>
</be-center>
