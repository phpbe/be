<?php
use Be\Be;
?>
<be-html>
<?php
$adminThemeUrl = \Be\Be::getProperty('AdminTheme.Admin')->getUrl();
$themeUrl = Be::getProperty('AdminTheme.Nude')->getUrl();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo $this->title; ?></title>

    <script src="<?php echo $adminThemeUrl; ?>/js/vue-2.6.11.min.js"></script>

    <script src="<?php echo $adminThemeUrl; ?>/js/axios-0.19.0.min.js"></script>
    <script>Vue.prototype.$http = axios;</script>

    <script src="<?php echo $adminThemeUrl; ?>/js/vue-cookies-1.5.13.js"></script>

    <link rel="stylesheet" href="<?php echo $adminThemeUrl; ?>/css/element-ui-2.13.2.css">
    <script src="<?php echo $adminThemeUrl; ?>/js/element-ui-2.13.2.js"></script>

    <link rel="stylesheet" href="<?php echo $adminThemeUrl; ?>/css/font-awesome-4.7.0.min.css" />

    <link rel="stylesheet" href="<?php echo $themeUrl; ?>/css/theme.css" />

    <be-head>
    </be-head>
</head>
<body>
    <be-body>
    <div class="be-body">

        <be-center>
        </be-center>

    </div>
    </be-body>
</body>
</html>
</be-html>