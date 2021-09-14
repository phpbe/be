<be-html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $this->title; ?></title>
    <script src="https://cdn.jsdelivr.net/npm/jquery@1.12.4/dist/jquery.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <script>var beUrl = "<?php echo beUrl(); ?>"; </script>

    <base href="<?php echo beUrl(); ?>" />

    <?php
    $configTheme = \Be\Be::getConfig('Theme.Sample.Theme');
    ?>
    <style type="text/css">
        body {
            font-size: <?php echo $configTheme->bodyFontSize ?>px;
            background-color: <?php echo $configTheme->bodyBackgroundColor ?>;
            color: <?php echo $configTheme->bodyColor ?>;
        }

        a {
            color: <?php echo $configTheme->linkColor ?>;
            text-decoration: none;
        }

        a:hover {
            color: <?php echo $configTheme->linkHoverColor ?>;
            text-decoration: underline;
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
                    <div class="be-center"></div>
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