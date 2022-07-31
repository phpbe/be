<be-head>
    <style type="text/css">
        .el-form-item .el-form-item {
            margin-bottom: 10px;
        }
    </style>
</be-head>


<be-page-content>
    <?php
    $formData = [];
    $vueItems = new \Be\AdminPlugin\VueItem\VueItems();
    ?>
    <div class="be-bc-fff be-px-150 be-pt-150 be-pb-50" id="app" v-cloak>

        <el-tabs tab-position="left" value="<?php echo $this->configName; ?>" @tab-click="goto">
            <?php
            foreach ($this->configs as $config) {
                ?>
                <el-tab-pane name="<?php echo $config['name']; ?>" label="<?php echo $config['label']; ?>">
                    <?php
                    if ($config['name'] === $this->configName) {
                        if (count($this->configItemDrivers)) {
                            ?>
                            <div style="max-width: 800px;">
                                <el-form size="medium" label-width="200px" :disabled="loading">
                                    <?php
                                    foreach ($this->configItemDrivers as $driver) {

                                        echo $driver->getHtml();

                                        if ($driver instanceof \Be\AdminPlugin\Form\Item\FormItems) {
                                            if ($driver->name !== null) {
                                                $formData[$driver->name] = $driver->value;
                                            }
                                        } else {
                                            if ($driver->name !== null) {
                                                if (is_array($driver->value) || is_object($driver->value)) {
                                                    $formData[$driver->name] =  json_encode($driver->value, JSON_PRETTY_PRINT);
                                                } else {
                                                    $formData[$driver->name] = $driver->value;
                                                }
                                            }
                                        }

                                        $vueItems->add($driver);
                                    }
                                    ?>
                                    <el-form-item>
                                        <el-button type="primary" icon="el-icon-check" @click="saveConfig">保存</el-button>
                                        <el-button type="warning" icon="el-icon-close" @click="resetConfig">恢复默认值</el-button>
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
    echo $vueItems->getJs();
    echo $vueItems->getCss();
    ?>

    <script>
        var vueForm = new Vue({
            el: '#app',
            data: {
                formData: <?php echo json_encode($formData); ?>,
                loading: false
                <?php
                echo $vueItems->getVueData();
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
                            if (response.status === 200) {
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
                                if (response.status === 200) {
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
                echo $vueItems->getVueMethods();
                ?>
            }

            <?php
            echo $vueItems->getVueHooks();
            ?>
        });
    </script>
</be-page-content>
