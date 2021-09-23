<be-html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $this->title; ?></title>
    <base href="<?php echo beUrl(); ?>/">
    <script>var beUrl = "<?php echo beUrl(); ?>"; </script>

    <script src="https://cdn.jsdelivr.net/npm/jquery@1.12.4/dist/jquery.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

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

        .be-container {
            width: 100%;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
            margin-left: auto;
            margin-right: auto;
        }

        @media (max-width: 768px) {
            .be-container {

            }
        }

        @media (min-width: 768px) {
            .be-container {
                max-width: 720px;
            }
        }

        @media (min-width: 992px) {
            .be-container {
                max-width: 960px;
            }
        }

        @media (min-width: 1200px) {
            .be-container {
                max-width: 1140px;
            }
        }

        @media (min-width: 1400px) {
            .be-container {
                max-width: 1320px;
            }
        }

        .be-btn {
            color: <?php echo $configTheme->btnColor ?>;
            background-color: <?php echo $configTheme->btnBackgroundColor ?>;
            border: 1px solid <?php echo $configTheme->btnBorderColor ?>;
            font-size: 1rem;
            display: inline-block;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            outline: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            transition: all .3s ease;
            border-radius: 2rem;
            padding: 0.4rem 1rem;
            min-width: 5rem;
            text-decoration: none;
        }

        .be-btn:hover {
            color: <?php echo $configTheme->btnHoverColor ?>;
            background-color: <?php echo $configTheme->btnHoverBackgroundColor ?>;
            border-color: <?php echo $configTheme->btnHoverBorderColor ?>;
            text-decoration: none;
        }

        .be-btn-mini {
            font-size: 0.6rem !important;
            padding: 0.2rem 0.6rem !important;
            border-radius: 1rem !important;
            min-width: 3rem;
        }

        .be-btn-small {
            font-size: 0.8rem !important;
            padding: 0.3rem 0.8rem !important;
            border-radius: 1.5rem !important;
            min-width: 4rem;
        }

        .be-btn-large {
            font-size: 1.2rem !important;
            padding: 0.5rem 1.2rem !important;
            border-radius: 2.5rem !important;
            min-width: 6rem;
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