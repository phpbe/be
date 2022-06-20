<be-head>
    <link rel="stylesheet" href="<?php echo \Be\Be::getProperty('App.System')->getWwwUrl(); ?>/admin/theme-editor/css/edit-section-item.css" type="text/css"/>
</be-head>

<be-body>
    <?php
    $js = [];
    $jsCode = '';
    $css = [];
    $cssCode = '';
    $formData = [];
    $vueData = [];
    $vueMethods = [];
    $vueHooks = [];
    ?>
    <div id="app" v-cloak>
        <el-form size="small" label-position="top" :disabled="loading">
            <?php
            foreach ($this->drivers as $driver) {

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

                $jsX = $driver->getJs();
                if ($jsX) {
                    $js = array_merge($js, $jsX);
                }

                $jsCodeX = $driver->getJsCode();
                if ($jsCodeX) {
                    $jsCode .= $jsCodeX . "\n";
                }

                $cssX = $driver->getCss();
                if ($cssX) {
                    $css = array_merge($css, $cssX);
                }

                $cssCodeX = $driver->getCssCode();
                if ($cssCodeX) {
                    $cssCode .= $cssCodeX . "\n";
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
        </el-form>

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

    if ($jsCode) {
        echo '<script>' . $jsCode . '</script>';
    }

    if ($cssCode) {
        echo '<style>' . $cssCode . '</style>';
    }
    ?>

    <script>
        var vueForm = new Vue({
            el: '#app',
            data: {
                formData: <?php echo json_encode($formData); ?>,
                loading: false,
                changing: 0<?php
                if ($vueData) {
                    foreach ($vueData as $k => $v) {
                        echo ',' . $k . ':' . json_encode($v);
                    }
                }
                ?>
            },
            watch: {
                formData: {
                    handler: function(newValue, oldValue) {
                        // console.log(newValue)

                        if (this.changing >= 1) {
                            this.changing = 1;
                            return;
                        }

                        this.changing = 1;
                        let _this = this;
                        let timer = setInterval(function () {
                            _this.changing++;
                            if (_this.changing > 5) {
                                _this.changing = 0;
                                clearInterval(timer);

                                _this.$http.post("<?php echo beAdminUrl('System.' . $this->themeType . '.saveSectionItem', ['themeName' => $this->themeName, 'pageName' => $this->pageName, 'sectionType' => $this->sectionType, 'sectionKey' => $this->sectionKey, 'itemKey' => $this->itemKey]); ?>", {
                                    formData: _this.formData,
                                }).then(function (response) {
                                    if (response.status === 200) {
                                        if (response.data.success) {
                                            parent.reloadPreviewFrame();
                                        } else {
                                            alert(response.data.message);
                                        }
                                    }
                                }).catch(function (error) {
                                    alert(error);
                                });
                            }
                        }, 100);
                    },
                    deep: true
                }
            },
            methods: {
                test: function () {

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

</be-body>
