<be-page-content>
    <?php
    $formData = [];
    $vueItems = new \Be\AdminPlugin\VueItem\VueItems();
    ?>
    <div class="be-bc-fff be-px-100 be-pt-100 be-pb-50" id="app" v-cloak>

        <el-form<?php
            $formUi = [
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
                        if (substr($item['driver'], 0, 10) === 'DetailItem') {
                            $driverClass = '\\Be\\AdminPlugin\\Detail\\Item\\' . $item['driver'];
                        } else {
                            $driverClass = $item['driver'];
                        }
                    } else {
                        $driverClass = \Be\AdminPlugin\Detail\Item\DetailItemText::class;
                    }
                    $driver = new $driverClass($item);

                    echo $driver->getHtml();

                    $formData[$driver->name] = $driver->value;

                    $vueItems->add($driver);
                }
            }
            ?>
            <el-form-item>
                <?php
                if (isset($this->setting['form']['actions']) && count($this->setting['form']['actions']) > 0) {
                    foreach ($this->setting['form']['actions'] as $key => $item) {
                        if ($key === 'cancel') {
                            if ($item) {
                                if ($item === true) {
                                    echo '<el-button type="primary" @click="cancel">关闭</el-button> ';
                                    continue;
                                } elseif (is_string($item)) {
                                    echo '<el-button type="primary" @click="cancel">' . $item . '</el-button> ';
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
    </div>

    <?php
    $vueItems->setting($this->setting);

    echo $vueItems->getJs();
    echo $vueItems->getCss();
    ?>

    <script>
        var vueDetail = new Vue({
            el: '#app',
            data: {
                formData: <?php echo json_encode($formData); ?>
                <?php
                echo $vueItems->getVueData();
                ?>
            },
            methods: {
                cancel: function () {
                    if(self.frameElement !== null && (self.frameElement.tagName === "IFRAME" || self.frameElement.tagName === "iframe")){
                        parent.close();
                    } else {
                        window.close();
                    }
                },
                formAction: function (name, option) {
                    var data = {};
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
