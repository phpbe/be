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

        <el-tabs tab-position="left" value="<?php echo $this->configName; ?>" @tab-click="goto">
            <?php
            foreach ($this->configs as $config) {
                ?>
                <el-tab-pane name="<?php echo $config['name']; ?>" label="<?php echo $config['label']; ?>">
                    <?php
                    if ($config['name'] == $this->configName) {
                        if (count($this->configItemDrivers)) {
                            ?>
                            <div style="max-width: 800px;">
                                <el-form size="small" label-width="200px" :disabled="loading">
                                    <?php
                                    foreach ($this->configItemDrivers as $driver) {

                                        echo $driver->getHtml();

                                        if (is_array($driver->value) || is_object($driver->value)) {
                                            $formData[$driver->name] =  json_encode($driver->value, JSON_PRETTY_PRINT);
                                        } else {
                                            $formData[$driver->name] = $driver->value;
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
                                    ?>
                                    <el-form-item>
                                        <el-button type="success" icon="el-icon-check" @click="saveConfig">保存</el-button>
                                        <el-button type="danger" icon="el-icon-close" @click="resetConfig">恢复默认值</el-button>
                                        <?php if (isset($config['test'])) { ?>
                                            <el-button icon="el-icon-view" @click="window.open('<?php echo $config['test']; ?>');">测试</el-button>
                                        <?php } ?>
                                    </el-form-item>
                                </el-form>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </el-tab-pane>
                <?php
            }
            ?>
        </el-tabs>
    </div>

    <?php
    if (count($js) > 0) {
        $js = array_unique($js);
        foreach ($js as $x) {
            echo '<script src="'.$x.'"></script>';
        }
    }

    if (count($css) > 0) {
        $css = array_unique($css);
        foreach ($css as $x) {
            echo '<link rel="stylesheet" href="'.$x.'">';
        }
    }
    ?>

    <script>
        var app = new Vue({
            el: '#app',
            data: {
                formData: <?php echo json_encode($formData); ?>,
                loading: false<?php
                if ($vueData) {
                    foreach ($vueData as $k => $v) {
                        echo ',' . $k . ':' . json_encode($v);
                    }
                }
                ?>
            },
            methods: {
                saveConfig: function () {
                    this.loading = true;
                    var _this = this;
                    _this.$http.post("<?php echo beAdminUrl(null, ['task' => 'saveConfig', 'configName' => $this->configName]); ?>", {
                        formData: _this.formData
                    }).then(function (response) {
                            _this.loading = false;
                            if (response.status == 200) {
                                if (response.data.success) {
                                    _this.$message.success(response.data.message);
                                } else {
                                    _this.$message.error(response.data.message);
                                }
                            }
                        }).catch(function (error) {
                        _this.loading = false;
                        _this.$message.error(error);
                    });
                },

                resetConfig: function () {
                    var _this = this;
                    this.$confirm('该操作不可恢复，确认恢复默认值吗？', '确认恢复默认值吗', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(function () {
                        _this.loading = true;
                        _this.$http.get("<?php echo beAdminUrl(null, ['task' => 'resetConfig', 'configName' => $this->configName]); ?>")
                            .then(function (response) {
                                _this.loading = false;
                                if (response.status == 200) {
                                    if (response.data.success) {
                                        _this.$message.success(response.data.message);
                                        window.location.reload();
                                    } else {
                                        _this.$message.error(response.data.message);
                                    }
                                }
                            })
                            .catch(function (error) {
                                _this.loading = false;
                                _this.$message.error(error);
                            });
                    }).catch(function () {
                        _this.loading = false;
                    });
                },
                goto: function (tab) {
                    var sUrl = "<?php echo beAdminUrl(\Be\Be::getRequest()->getRoute()); ?>";
                    sUrl += sUrl.indexOf("?") >= 0 ? "&" : "?";
                    sUrl += "configName=" + tab.name;
                    window.location.href = sUrl;
                }
                <?php
                if ($vueMethods) {
                    foreach ($vueMethods as $k => $v) {
                        echo ',' . $k . ':' . $v;
                    }
                }
                ?>
            }
            <?php
            if (isset($vueHooks['beforeCreate'])) {
                echo ',beforeCreate: function () {'.$vueHooks['beforeCreate'].'}';
            }

            if (isset($vueHooks['created'])) {
                echo ',created: function () {'.$vueHooks['created'].'}';
            }

            if (isset($vueHooks['beforeMount'])) {
                echo ',beforeMount: function () {'.$vueHooks['beforeMount'].'}';
            }

            if (isset($vueHooks['mounted'])) {
                echo ',mounted: function () {'.$vueHooks['mounted'].'}';
            }

            if (isset($vueHooks['beforeUpdate'])) {
                echo ',beforeUpdate: function () {'.$vueHooks['beforeUpdate'].'}';
            }

            if (isset($vueHooks['updated'])) {
                echo ',updated: function () {'.$vueHooks['updated'].'}';
            }

            if (isset($vueHooks['beforeDestroy'])) {
                echo ',beforeDestroy: function () {'.$vueHooks['beforeDestroy'].'}';
            }

            if (isset($vueHooks['destroyed'])) {
                echo ',destroyed: function () {'.$vueHooks['destroyed'].'}';
            }
            ?>
        });
    </script>
</be-center>
