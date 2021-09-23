<be-html>
    <?php
    $adminThemeUrl = \Be\Be::getProperty('AdminTheme.Admin')->getUrl();
    $themeUrl = \Be\Be::getProperty('AdminTheme.Installer')->getUrl();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8"/>
        <title><?php echo $this->title; ?></title>
        <base href="<?php echo beUrl(); ?>/">
        <script>var beUrl = "<?php echo beUrl(); ?>"; </script>

        <script src="<?php echo $adminThemeUrl; ?>/js/vue-2.6.11.min.js"></script>

        <script src="<?php echo $adminThemeUrl; ?>/js/axios-0.19.0.min.js"></script>
        <script>Vue.prototype.$http = axios;</script>

        <script src="<?php echo $adminThemeUrl; ?>/js/vue-cookies-1.5.13.js"></script>

        <link rel="stylesheet" href="<?php echo $adminThemeUrl; ?>/css/element-ui-2.13.2.css">
        <script src="<?php echo $adminThemeUrl; ?>/js/element-ui-2.13.2.js"></script>

        <link rel="stylesheet" href="<?php echo $adminThemeUrl; ?>/css/font-awesome-4.7.0.min.css"/>

        <link rel="stylesheet" href="<?php echo $themeUrl; ?>/css/theme.css"/>

        <be-head>
        </be-head>
    </head>
    <body>
    <be-body>

        <div class="header">
            <div class="logo-container">
                <div class="logo">
                    <div class="txt">安装程序</div>
                </div>
            </div>
        </div>

        <div class="be-body">
            <?php
            if (isset($this->steps) && is_array($this->steps) && count($this->steps) > 0) {
                ?>
                <div id="steps">
                    <el-steps :active="<?php echo isset($this->step) ? $this->step : 0; ?>" align-center>
                        <?php
                        foreach ($this->steps as $step) {
                            ?>
                            <el-step title="<?php echo $step; ?>"></el-step>
                            <?php
                        }
                        ?>
                    </el-steps>
                </div>
                <?php
            }
            ?>

            <be-center></be-center>
        </div>
        <?php
        if (isset($this->steps) && is_array($this->steps) && count($this->steps) > 0) {
            ?>
            <script>
                var vueSteps = new Vue({
                    el: '#steps',
                    data: {},
                    methods: {}
                });
            </script>
            <?php
        }
        ?>
    </be-body>
    </body>
    </html>
</be-html>