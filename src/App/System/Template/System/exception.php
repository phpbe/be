<be-head>
    <?php
    $appUrl = \Be\Be::getProperty('App.System')->getWwwUrl();
    ?>
    <link rel="stylesheet" href="<?php echo $appUrl; ?>/Template/System/css/be-tab.css" type="text/css"/>
    <script type="text/javascript" src="<?php echo $appUrl; ?>/Template/System/js/be-tab.js"></script>

    <link rel="stylesheet" href="<?php echo $appUrl; ?>/Template/System/google-code-prettify/prettify.css" type="text/css"/>
    <script type="text/javascript" src="<?php echo $appUrl; ?>/Template/System/google-code-prettify/prettify.js"></script>
    <style type="text/css">
        pre.prettyprint {
            background-color: #fff;
            color: #000;
            white-space: pre-wrap;
            word-wrap: break-word;
            border-color: #ddd;
        }
    </style>
</be-head>

<be-body>
    <div class="be-p-100">
    <?php
    $configSystem = \Be\Be::getConfig('App.System.System');
    if ($configSystem->developer) {
        $request = \Be\Be::getRequest();
        ?>

        <div class="be-p-50" style="background-color: #fff4f2; border: #ffe0d8 1px solid; color: #842029; ">
            <div class="be-row">
                <div class="be-col-auto">
                    <div class="be-c-red be-p-50 be-pr-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                        </svg>
                    </div>
                </div>
                <div class="be-col">
                    <div><?php echo htmlspecialchars($this->e->getMessage()); ?></div>
                    <?php if (isset($this->logId)) { echo '<div>#' . $this->logId . '</div>';} ?>
                </div>
            </div>
        </div>


        <div class="be-tab be-mt-150">

            <div class="be-tab-nav">
                <a class="be-tab-nav-active" data-be-target="#be-tab-pane-trace">错误跟踪信息</a>
                <a data-be-target="#be-tab-pane-get">GET</a>
                <a data-be-target="#be-tab-pane-post">POST</a>
                <a data-be-target="#be-tab-pane-request">REQUEST</a>
                <a data-be-target="#be-tab-pane-cookie">COOKIE</a>
                <a data-be-target="#be-tab-pane-header">头信息</a>
                <a data-be-target="#be-tab-pane-server">SERVER</a>
            </div>

            <div class="be-tab-content">
                <div class="be-tab-pane" id="be-tab-pane-trace">
                    <pre class="prettyprint linenums"><?php print_r($this->e->getTrace()); ?></pre>
                </div>
                <div class="be-tab-pane" id="be-tab-pane-get">
                    <pre class="prettyprint linenums"><?php print_r($request->get()) ?></pre>
                </div>
                <div class="be-tab-pane" id="be-tab-pane-post">
                    <pre class="prettyprint linenums"><?php print_r($request->post()) ?></pre>
                </div>
                <div class="be-tab-pane" id="be-tab-pane-request">
                    <pre class="prettyprint linenums"><?php print_r($request->request()) ?></pre>
                </div>
                <div class="be-tab-pane" id="be-tab-pane-cookie">
                    <pre class="prettyprint linenums"><?php print_r($request->cookie()) ?></pre>
                </div>
                <div class="be-tab-pane" id="be-tab-pane-header">
                    <pre class="prettyprint linenums"><?php print_r($request->header()) ?></pre>
                </div>
                <div class="be-tab-pane" id="be-tab-pane-server">
                    <pre class="prettyprint linenums"><?php print_r($request->server()) ?></pre>
                </div>
            </div>
        </div>

        <script>
            $(function () {
                prettyPrint();
            });
        </script>

        <?php
    } else {
        ?>
        <div class="be-ta-center be-c-red be-mt-300">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
            </svg>
        </div>

        <div class="be-ta-center be-fs-150 be-mt-300">
            <?php echo $this->e->getMessage(); ?>
        </div>
        <?php
    }
    ?>
    </div>
</be-body>
