<be-html>
    <?php
    $beUrl = beUrl();
    $configTheme = \Be\Be::getConfig('AdminTheme.Blank.Theme');
    $adminThemeUrl = \Be\Be::getProperty('AdminTheme.Admin')->getUrl();
    $themeUrl = \Be\Be::getProperty('AdminTheme.Blank')->getUrl();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8" />
        <title><?php echo $this->title; ?></title>
        <base href="<?php echo $beUrl; ?>/">
        <script>var beUrl = "<?php echo $beUrl; ?>"; </script>

        <?php
        if ($configTheme->localAssetLib) {
            ?>
            <script src="<?php echo $adminThemeUrl; ?>/js/jquery-1.12.4.min.js"></script>

            <script src="<?php echo $adminThemeUrl; ?>/js/vue-2.6.11.min.js"></script>

            <script src="<?php echo $adminThemeUrl; ?>/js/axios-0.19.0.min.js"></script>
            <script>Vue.prototype.$http = axios;</script>

            <script src="<?php echo $adminThemeUrl; ?>/js/vue-cookies-1.5.13.js"></script>

            <link rel="stylesheet" href="<?php echo $adminThemeUrl; ?>/css/element-ui-2.15.7.css">
            <script src="<?php echo $adminThemeUrl; ?>/js/element-ui-2.15.7.js"></script>

            <link rel="stylesheet" href="<?php echo $adminThemeUrl; ?>/css/font-awesome-4.7.0.min.css" />
            <?php
        } else {
            ?>
            <script src="https://unpkg.com/jquery@1.12.4/dist/jquery.min.js"></script>

            <script src="https://unpkg.com/vue@2.6.11/dist/vue.min.js"></script>

            <script src="https://unpkg.com/axios@0.19.0/dist/axios.min.js"></script>
            <script>Vue.prototype.$http = axios;</script>

            <script src="https://unpkg.com/vue-cookies@1.5.13/vue-cookies.js"></script>

            <link rel="stylesheet" href="https://unpkg.com/element-ui@2.15.7/lib/theme-chalk/index.css">
            <script src="https://unpkg.com/element-ui@2.15.7/lib/index.js"></script>

            <link rel="stylesheet" href="https://unpkg.com/font-awesome@4.7.0/css/font-awesome.min.css" />
            <?php
        }
        ?>

        <link rel="stylesheet" href="<?php echo $beUrl; ?>/vendor/be/scss/src/be.css" />
        <link rel="stylesheet" href="<?php echo $themeUrl; ?>/css/theme.css" />

        <be-head>
        </be-head>
    </head>
    <body>
    <be-body>
        <div class="be-body">
            <be-middle>
                <be-center>
                    <be-center-body></be-center-body>
                </be-center>
            </be-middle>
        </div>
    </be-body>
    </body>
    </html>
</be-html>