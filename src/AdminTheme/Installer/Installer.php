<be-html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title><?php echo $this->title; ?></title>

    <?php
    $beUrl = beUrl();
    $appSystemWwwUrl = \Be\Be::getProperty('App.System')->getWwwUrl();
    $adminThemeWwwUrl = \Be\Be::getProperty('AdminTheme.Admin')->getWwwUrl();
    ?>
    <base href="<?php echo $beUrl; ?>/" >
    <script>var beUrl = "<?php echo $beUrl; ?>"; </script>

    <script src="<?php echo $appSystemWwwUrl; ?>/lib/jquery/jquery-1.12.4.min.js"></script>

    <script src="<?php echo $appSystemWwwUrl; ?>/lib/vue/vue-2.6.11.min.js"></script>

    <script src="<?php echo $appSystemWwwUrl; ?>/lib/axios/axios-0.19.0.min.js"></script>
    <script>Vue.prototype.$http = axios;</script>

    <script src="<?php echo $appSystemWwwUrl; ?>/lib/vue-cookies/vue-cookies-1.5.13.js"></script>

    <link rel="stylesheet" href="<?php echo $appSystemWwwUrl; ?>/lib/element-ui/element-ui-2.15.7.css">
    <script src="<?php echo $appSystemWwwUrl; ?>/lib/element-ui/element-ui-2.15.7.js"></script>

    <link rel="stylesheet" href="<?php echo $appSystemWwwUrl; ?>/lib/font-awesome/font-awesome-4.7.0.min.css" />

    <link rel="stylesheet" href="https://cdn.phpbe.com/scss/be.css" />
    <link rel="stylesheet" href="<?php echo $adminThemeWwwUrl; ?>/css/theme.css?v=20220620" />

    <style>
        .header {
            background-color: #409EFF;
        }

        .be-body {
            width: 900px;
            margin: 0 auto;
            padding: 10px;
        }

        #steps {
            padding: 10px 0 30px 0;
        }
    </style>

    <be-head>
    </be-head>
</head>
<body>
<be-body>

    <div class="header">

        <div class="be-row">
            <div class="be-col-auto">
                <a href="https://www.phpbe.com" class="be-d-inline-block">
                    <svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                        <rect rx="8" width="64" height="64" y="0" x="0" fill="#ff6600"/>
                        <path d="M9 31 L15 31 M15 52 L9 52 L9 11 L15 11 C32 11 32 31 15 31 C32 32 32 52 15 52 M57 11 L39 11 L39 52 L57 52 M39 31 L55 31" stroke="#ffffff" stroke-width="4" fill="none" />
                    </svg>
                </a>
            </div>
            <div class="be-col be-pl-125">
                <div class="be-fs-125 be-lh-125 be-fw-100 be-mt-75"><span style="color: #f60;">B</span>eyond</div>
                <div class="be-fs-125 be-lh-125 be-fw-100"><span class="be-pl-250" style="color: #f60;">E</span>xception</div>
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
        <be-center>
            <be-center-body></be-center-body>
        </be-center>
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