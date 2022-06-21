<be-html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title><?php echo $this->title; ?></title>
    <base href="<?php echo beUrl(); ?>/" >

    <script src="https://libs.baidu.com/jquery/2.0.3/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({cache: false});

            $(document).ajaxStart(function(){
                $('#ajax-loader').fadeIn();
            }).ajaxStop(function(){
                $('#ajax-loader').fadeOut();
            });
        });
    </script>

    <link rel="stylesheet" href="https://cdn.phpbe.com/scss/be.css" />
    <?php
    $libCss = \Be\Be::getLib('Css');
    $mainColor = '#f60';
    $mainColor1 = $libCss->lighter($mainColor, 10);
    $mainColor2 = $libCss->lighter($mainColor, 20);
    $mainColor3 = $libCss->lighter($mainColor, 30);
    $mainColor4 = $libCss->lighter($mainColor, 40);
    $mainColor5 = $libCss->lighter($mainColor, 50);
    $mainColor6 = $libCss->lighter($mainColor, 60);
    $mainColor7 = $libCss->lighter($mainColor, 70);
    $mainColor8 = $libCss->lighter($mainColor, 80);
    $mainColor9 = $libCss->lighter($mainColor, 90);
    $mainColorHover = $libCss->darker($mainColor, 10);
    ?>
    <style type="text/css">
        body {
            --main-color: <?php echo $mainColor; ?>;
            --main-color-1: <?php echo $mainColor1; ?>;
            --main-color-2: <?php echo $mainColor2; ?>;
            --main-color-3: <?php echo $mainColor3; ?>;
            --main-color-4: <?php echo $mainColor4; ?>;
            --main-color-5: <?php echo $mainColor5; ?>;
            --main-color-6: <?php echo $mainColor6; ?>;
            --main-color-7: <?php echo $mainColor7; ?>;
            --main-color-8: <?php echo $mainColor8; ?>;
            --main-color-9: <?php echo $mainColor9; ?>;
            --main-color-hover: <?php echo $mainColorHover; ?>;
            margin: 0;
            padding: 0;
        }

        @keyframes ajaxLoading {
            0% {
                transform: rotate(0deg)
            }

            100% {
                transform: rotate(360deg)
            }
        }

        #ajax-loader {
            position: fixed;
            top: calc(50% - 30px);
            left: calc(50% - 30px);
            width: 60px;
            height: 60px;
            z-index: 1050;
            background-color: rgba(255, 255, 255, .8);
            border-radius: 10px;
            display: none;
        }

        #ajax-loader div {
            position: absolute;
            top: 13px;
            left: 13px;
            width: 35px;
            height: 35px;
            border: 4px solid;
            -moz-border-radius: 50%;
            -webkit-border-radius: 50%;
            border-radius: 50%;
            -moz-animation: ajaxLoading 1.2s cubic-bezier(.5, 0, .5, 1) infinite;
            -webkit-animation: ajaxLoading 1.2s cubic-bezier(.5, 0, .5, 1) infinite;
            -o-animation: ajaxLoading 1.2s cubic-bezier(.5, 0, .5, 1) infinite;
            animation: ajaxLoading 1.2s cubic-bezier(.5, 0, .5, 1) infinite;
            border-color: var(--main-color) transparent transparent transparent;
        }

        #ajax-loader div:nth-child(1) {
            -moz-animation-delay: -0.45s;
            -webkit-animation-delay: -0.45s;
            -o-animation-delay: -0.45s;
            animation-delay: -0.45s
        }

        #ajax-loader div:nth-child(2) {
            -moz-animation-delay: -0.3s;
            -webkit-animation-delay: -0.3s;
            -o-animation-delay: -0.3s;
            animation-delay: -0.3s
        }

        #ajax-loader div:nth-child(3) {
            -moz-animation-delay: -0.15s;
            -webkit-animation-delay: -0.15s;
            -o-animation-delay: -0.15s;
            animation-delay: -0.15s
        }
    </style>

    <be-head>
    </be-head>
</head>
<body>
<be-body>
    <div class="be-py-75" style="background-color: var(--main-color-9)">
        <div class="be-container">
            <div class="be-row">
                <div class="be-col-auto">
                    <a href="<?php echo beUrl(); ?>" class="be-d-inline-block">
                        <svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                            <rect rx="8" width="64" height="64" y="0" x="0" fill="#ff6600"/>
                            <path d="M9 31 L15 31 M15 52 L9 52 L9 11 L15 11 C32 11 32 31 15 31 C32 32 32 52 15 52 M57 11 L39 11 L39 52 L57 52 M39 31 L55 31" stroke="#ffffff" stroke-width="4" fill="none" />
                        </svg>
                    </a>
                </div>
                <div class="be-col-auto be-pl-125">
                    <div class="be-fs-125 be-lh-125 be-fw-100 be-mt-75"><span style="color: #f60;">B</span>eyond</div>
                    <div class="be-fs-125 be-lh-125 be-fw-100"><span class="be-pl-250" style="color: #f60;">E</span>xception</div>
                </div>
                <div class="be-col">
                    <div class="be-fs-150 be-lh-150 be-fw-100 be-mt-150 be-pl-250">BE双驱框架 - 安装程序</div>
                </div>
            </div>

        </div>
    </div>

    <div class="be-container be-my-200">
        <?php
        if (isset($this->steps) && is_array($this->steps) && count($this->steps) > 0) {
            $currentStep = isset($this->step) ? $this->step : 1;
            ?>
            <div class="be-row be-mb-200">
                <div class="be-col"></div>
                <?php
                $i = 1;
                foreach ($this->steps as $step) {
                    if ($currentStep < $i) {
                        $class = 'be-c-ccc';
                    } else if ($currentStep === $i) {
                        $class = 'be-c-main';
                    } else {
                        $class = 'be-c-green';
                    }
                    ?>
                    <div class="be-col-auto">
                        <div class="be-px-100">
                            <div class="be-ta-center be-fs-200 <?php echo $class; ?>"><?php echo $i; ?></div>
                            <div class="be-ta-center be-mt-50 <?php echo $class; ?>"><?php echo $step; ?></div>
                        </div>
                    </div>

                    <?php
                    $i++;
                }
                ?>
                <div class="be-col"></div>
            </div>
            <?php
        }
        ?>

        <be-center>
            <be-center-body></be-center-body>
        </be-center>
    </div>
</be-body>

<div id="ajax-loader">
    <div></div>
    <div></div>
    <div></div>
    <div></div>
</div>

</body>
</html>
</be-html>