<be-html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no,viewport-fit=cover">
    <title><?php echo $this->title; ?></title>
    <meta name="keywords" content="<?php echo isset($this->meta_keywords) ? $this->meta_keywords : ''; ?>">
    <meta name="description" content="<?php echo isset($this->meta_description) ? $this->meta_description : ''; ?>">
    <meta name="applicable-device" content="pc,mobile">
    <?php
    $beUrl = beUrl();
    $themeUrl = \Be\Be::getProperty('Theme.Sample')->getUrl();
    ?>
    <base href="<?php echo $beUrl; ?>/" >
    <script>var beUrl = "<?php echo $beUrl; ?>"; </script>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

    <script src="https://cdn.jsdelivr.net/npm/jquery@1.12.4/dist/jquery.min.js"></script>

    <link rel="stylesheet" href="<?php echo $beUrl; ?>/vendor/be/scss/src/be.css" />

    <link rel="stylesheet" href="<?php echo $themeUrl; ?>/css/theme.css" />

    <?php
    $configTheme = \Be\Be::getConfig('Theme.Sample.Theme');
    ?>
    <style type="text/css">
        html {
            font-size: <?php echo $configTheme->pageFontSize ?>px;
            background-color: <?php echo $configTheme->pageBackgroundColor ?>;
            color: <?php echo $configTheme->pageColor ?>;
        }

        a {
            color: <?php echo $configTheme->linkColor; ?>;
        }

        a:hover {
            color: <?php echo $configTheme->linkHoverColor; ?>;
        }

        .link-hover:before {
            background-color: <?php echo $configTheme->linkHoverColor; ?>;
        }

        .be-btn {
            color: <?php echo $configTheme->btnColor; ?>;
            background-color: <?php echo $configTheme->btnBackgroundColor; ?>;
            border: 1px solid <?php echo $configTheme->btnBorderColor; ?>;
        }

        .be-btn:hover,
        .be-btn:focus {
            color: <?php echo $configTheme->btnHoverColor; ?>;
            background-color: <?php echo $configTheme->btnHoverBackgroundColor; ?>;
            border-color: <?php echo $configTheme->btnHoverBorderColor; ?>;
        }

        .be-btn-outline:hover,
        .be-btn-outline:focus {
            color: #fff !important;
            background-color: <?php echo $configTheme->btnBackgroundColor; ?> !important;
            border-color: <?php echo $configTheme->btnBorderColor; ?> !important;
        }
    </style>

    <be-head>
    </be-head>
</head>
<body>
    <be-body>
        <be-north>
            <?php
            $configPage = \Be\Be::getConfig('Theme.Sample.Page.Home');
            if (isset($configPage->northSections) && count($configPage->northSections) > 0) {
                $sectionType = 'north';
                foreach ($configPage->northSections as $sectionKey => $sectionName) {
                    $sectionData = $configPage->northSectionsData[$sectionKey];
                    echo '<div id="be-section-'.$sectionType.'-'.$sectionKey.'">';
                    include \Be\Be::getRuntime()->getRootPath() . '/' . \Be\Be::getProperty('Theme.Sample')->getPath() . '/Section/'.$sectionName.'.php';
                    echo '</div>';
                }
            }
            ?>
        </be-north>


        <be-middle>
            <div class="be-middle">
                <be-center>
                    <div class="be-center">
                        <div class="be-center-title"><?php echo $this->title; ?></div>
                        <div class="be-center-body"><be-center-body></be-center-body></div>
                    </div>
                </be-center>
            </div>
        </be-middle>


        <be-south>
            <?php
            $configPage = \Be\Be::getConfig('Theme.Sample.Page.Home');
            if (isset($configPage->southSections) && count($configPage->southSections) > 0) {
                $sectionType = 'south';
                foreach ($configPage->southSections as $sectionKey => $sectionName) {
                    $sectionData = $configPage->southSectionsData[$sectionKey];
                    echo '<div id="be-section-'.$sectionType.'-'.$sectionKey.'">';
                    include \Be\Be::getRuntime()->getRootPath() . '/' . \Be\Be::getProperty('Theme.Sample')->getPath() . '/Section/'.$sectionName.'.php';
                    echo '</div>';
                }
            }
            ?>
        </be-south>
    </be-body>
</body>
</html>
</be-html>