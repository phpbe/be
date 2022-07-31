<be-page-content>
    <?php
    $formData = [];
    $vueItems = new \Be\AdminPlugin\VueItem\VueItems();
    ?>
    <div class="be-bc-fff be-px-100 be-pt-100 be-pb-50" id="app" v-cloak>

        <el-form<?php
        $formUi = [
            ':model' => 'formData',
            'ref' => 'formRef',
            'size' => 'medium',
            'label-width' => '150px',
        ];

        if (isset($this->setting['form']['ui'])) {
            $formUi = array_merge($formUi, $this->setting['form']['ui']);
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
            if (isset($this->setting['headnote'])) {
                echo $this->setting['headnote'];
            }

            if (isset($this->setting['form']['items']) && count($this->setting['form']['items']) > 0) {
                foreach ($this->setting['form']['items'] as $item) {

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
                    $driver = new $driverClass($item, $this->row);

                    echo $driver->getHtml();

                    if ($driver instanceof \Be\AdminPlugin\Form\Item\FormItems) {
                        if ($driver->name !== null) {
                            $formData[$driver->name] = $driver->value;
                        }
                    } else {
                        if ($driver->name !== null) {
                            $formData[$driver->name] = $driver->getValueString();
                        }
                    }

                    $vueItems->add($driver);
                }
            }
            ?>

            <el-form-item>
                <?php
                if (isset($this->setting['form']['actions']) && count($this->setting['form']['actions']) > 0) {
                    foreach ($this->setting['form']['actions'] as $key => $item) {
                        if ($key === 'submit') {
                            if ($item) {
                                if ($item === true) {
                                    echo '<el-button type="primary" @click="submit" :disabled="loading" icon="el-icon-check">保存</el-button> ';
                                    continue;
                                } elseif (is_string($item)) {
                                    echo '<el-button type="primary" @click="submit" :disabled="loading" icon="el-icon-check">' . $item . '</el-button> ';
                                    continue;
                                }
                            } else {
                                continue;
                            }
                        } elseif ($key === 'reset') {
                            if ($item) {
                                if ($item === true) {
                                    echo '<el-button type="warning" @click="reset" :disabled="loading" icon="el-icon-refresh-left">重置</el-button> ';
                                    continue;
                                } elseif (is_string($item)) {
                                    echo '<el-button type="warning" @click="reset" :disabled="loading" icon="el-icon-refresh-left">' . $item . '</el-button> ';
                                    continue;
                                }
                            } else {
                                continue;
                            }
                        } elseif ($key === 'cancel') {
                            if ($item) {
                                if ($item === true) {
                                    echo '<el-button @click="cancel" :disabled="loading" icon="el-icon-close">取消</el-button> ';
                                    continue;
                                } elseif (is_string($item)) {
                                    echo '<el-button @click="cancel" :disabled="loading" icon="el-icon-close">' . $item . '</el-button> ';
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

                        echo $driver->getHtml() . ' ';

                        $vueItems->add($driver);
                    }
                }
                ?>
            </el-form-item>
            <?php
            if (isset($this->setting['footnote'])) {
                echo $this->setting['footnote'];
            }
            ?>
        </el-form>

        <el-dialog
                :title="dialog.title"
                :visible.sync="dialog.visible"
                :width="dialog.width"
                :close-on-click-modal="false"
                :destroy-on-close="true">
            <iframe id="frame-dialog" name="frame-dialog" src="about:blank"
                    :style="{width:'100%',height:dialog.height,border:0}"></iframe>
        </el-dialog>

        <el-drawer
                :visible.sync="drawer.visible"
                :size="drawer.width"
                :title="drawer.title"
                :wrapper-closable="false"
                :destroy-on-close="true">
            <iframe id="frame-drawer" name="frame-drawer" src="about:blank"
                    style="width:100%;height:100%;border:0;"></iframe>
        </el-drawer>
    </div>

    <?php
    $vueItems->setting($this->setting);

    echo $vueItems->getJs();
    echo $vueItems->getCss();
    ?>

    <script>
        var vueForm = new Vue({
            el: '#app',
            data: {
                formData: <?php echo json_encode($formData); ?>,
                loading: false,
                dialog: {visible: false, width: "600px", height: "400px", title: ""},
                drawer: {visible: false, width: "40%", title: ""}
                <?php
                echo $vueItems->getVueData();
                ?>
            },
            methods: {
                submit: function () {
                    var _this = this;
                    this.$refs["formRef"].validate(function (valid) {
                        if (valid) {
                            _this.loading = true;
                            _this.$http.post("<?php echo $this->setting['form']['action']; ?>", {
                                formData: _this.formData
                            }).then(function (response) {
                                _this.loading = false;
                                //console.log(response);
                                if (response.status === 200) {
                                    var responseData = response.data;
                                    if (responseData.success) {
                                        var message;
                                        if (responseData.message) {
                                            message = responseData.message;
                                        } else {
                                            message = '保存成功';
                                        }

                                        alert(message);

                                        if (responseData.callback) {

                                        } else {
                                            if (responseData.redirectUrl) {
                                                window.location.href=responseData.redirectUrl;
                                            } else {
                                                if (self.frameElement != null && (self.frameElement.tagName.toLowerCase() === "iframe")) {
                                                    parent.closeAndReload();
                                                } else {
                                                    window.close();
                                                }
                                            }
                                        }
                                    } else {
                                        if (responseData.message) {
                                            _this.$message.error(responseData.message);
                                        }
                                    }
                                }
                            }).catch(function (error) {
                                _this.loading = false;
                                _this.$message.error(error);
                            });

                        } else {
                            return false;
                        }
                    });
                },
                formAction: function (name, option) {
                    var data = {};
                    data.formData = this.formData;
                    data.postData = option.postData;
                    return this.action(option, data);
                },
                action: function (option, data) {
                    if (option.target === 'ajax') {
                        var _this = this;
                        this.$http.post(option.url, data).then(function (response) {
                            if (response.status === 200) {
                                if (response.data.success) {
                                    _this.$message.success(response.data.message);
                                } else {
                                    if (response.data.message) {
                                        _this.$message.error(response.data.message);
                                    }
                                }
                            }
                        }).catch(function (error) {
                            _this.$message.error(error);
                        });
                    } else {
                        var eForm = document.createElement("form");
                        eForm.action = option.url;
                        switch (option.target) {
                            case "self":
                            case "_self":
                                eForm.target = "_self";
                                break;
                            case "blank":
                            case "_blank":
                                eForm.target = "_blank";
                                break;
                            case "dialog":
                                eForm.target = "frame-dialog";
                                this.dialog.title = option.dialog.title;
                                this.dialog.width = option.dialog.width;
                                this.dialog.height = option.dialog.height;
                                this.dialog.visible = true;
                                break;
                            case "drawer":
                                eForm.target = "frame-drawer";
                                this.drawer.title = option.drawer.title;
                                this.drawer.width = option.drawer.width;
                                this.drawer.visible = true;
                                break;
                        }
                        eForm.method = "post";
                        eForm.style.display = "none";

                        var e = document.createElement("textarea");
                        e.name = 'data';
                        e.value = JSON.stringify(data);
                        eForm.appendChild(e);

                        document.body.appendChild(eForm);

                        setTimeout(function () {
                            eForm.submit();
                        }, 50);

                        setTimeout(function () {
                            document.body.removeChild(eForm);
                        }, 3000);
                    }

                    return false;
                },
                hideDialog: function () {
                    this.dialog.visible = false;
                },
                hideDrawer: function () {
                    this.drawer.visible = false;
                },
                reset: function () {
                    this.$refs["formRef"].resetFields();
                },
                cancel: function () {
                    if (self.frameElement != null && (self.frameElement.tagName.toLowerCase() === "iframe")) {
                        parent.close();
                    } else {
                        window.close();
                    }
                }

                <?php
                echo $vueItems->getVueMethods();
                ?>
            }

            <?php
            echo $vueItems->getVueHooks();
            ?>
        });

        function close() {
            vueForm.drawer.visible = false;
            vueForm.dialog.visible = false;
        }

        function closeDrawer() {
            vueForm.drawer.visible = false;
        }

        function closeDialog() {
            vueForm.dialog.visible = false;
        }

    </script>

</be-page-content>
