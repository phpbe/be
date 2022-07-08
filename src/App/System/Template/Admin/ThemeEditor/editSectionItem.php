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

        <div style="position: absolute; left:0; right:0 ;top: 0; bottom: 50px; overflow-y: auto;">

            <el-form class="be-p-50" size="small" label-position="top" :disabled="loading">
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

        <div style="position: absolute; left:0; right:0 ;bottom: 0; height: 50px; overflow: hidden;">
            <div class="be-pt-50 be-ta-center">
                <el-button type="primary" :disabled="loading" @click="save" size="small">保存</el-button>
                <el-button type="danger" :disabled="loading" @click="reset" size="small">恢复默认值</el-button>
            </div>
        </div>

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
                loading: false<?php
                if ($vueData) {
                    foreach ($vueData as $k => $v) {
                        echo ',' . $k . ':' . json_encode($v);
                    }
                }
                ?>
            },
            methods: {
                save: function () {
                    this.loading = true;

                    let _this = this;
                    _this.$http.post("<?php echo beAdminUrl('System.' . $this->themeType . '.saveSectionItem', ['themeName' => $this->themeName, 'pageName' => $this->pageName, 'position' => $this->position, 'sectionIndex' => $this->sectionIndex, 'itemIndex' => $this->itemIndex]); ?>", {
                        formData: _this.formData,
                    }).then(function (response) {
                        _this.loading = false;
                        if (response.status === 200) {
                            if (response.data.success) {
                                parent.reloadPreviewFrame();
                            } else {
                                alert(response.data.message);
                            }
                        }
                    }).catch(function (error) {
                        _this.loading = false;
                        alert(error);
                    });
                },
                reset: function () {
                    this.loading = true;

                    let _this = this;
                    _this.$http.get(
                        "<?php echo beAdminUrl('System.' . $this->themeType . '.resetSectionItem', ['themeName' => $this->themeName, 'pageName' => $this->pageName, 'position' => $this->position, 'sectionIndex' => $this->sectionIndex, 'itemIndex' => $this->itemIndex]); ?>"
                    ).then(function (response) {
                        _this.loading = false;
                        if (response.status === 200) {
                            if (response.data.success) {
                                parent.reloadPreviewFrame();
                                window.location.reload();
                            } else {
                                alert(response.data.message);
                            }
                        }
                    }).catch(function (error) {
                        _this.loading = false;
                        alert(error);
                    });
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
