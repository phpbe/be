<be-html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo $this->title; ?></title>

    <?php
    $beUrl = beUrl();
    $appSystemWwwUrl = \Be\Be::getProperty('App.System')->getWwwUrl();
    $adminThemeWwwUrl = \Be\Be::getProperty('AdminTheme.System')->getWwwUrl();
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

    <style>
        html {
            font-size: 14px;
            background-color: #fff;
            color: #333;
        }

        body {
            margin: 0;
            padding: 0;
        }

        [v-cloak] {display: none !important;}
        [class^="el-icon-fa"],
        [class*="el-icon-fa"] {
            display: inline-block;
            font-style: normal;
            font-variant: normal;
            font-weight: normal;
            font-family: FontAwesome!important;
            font-size: inherit;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>

    <be-head>
    </be-head>
</head>
<body>
<be-body>
    <be-page-content></be-page-content>
</be-body>
</body>
</html>
</be-html>