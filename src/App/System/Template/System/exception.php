<be-head>
    <link rel="stylesheet" href="<?php echo \Be\Be::getProperty('App.System')->getUrl(); ?>/Template/System/google-code-prettify/prettify.css" type="text/css"/>
    <script type="text/javascript" src="<?php echo \Be\Be::getProperty('App.System')->getUrl(); ?>/Template/System/google-code-prettify/prettify.js"></script>
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
    <div class="p-2">
    <?php
    $configSystem = \Be\Be::getConfig('App.System.System');
    if ($configSystem->developer) {
        $request = \Be\Be::getRequest();
        ?>

        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <div class="fs-2 text-danger pe-3">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div>
                <div><?php echo htmlspecialchars($this->e->getMessage()); ?></div>
                <?php if (isset($this->logId)) { echo '<div>#' . $this->logId . '</div>';} ?>
            </div>
        </div>

        <nav>
            <div class="nav nav-tabs">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-trace" type="button">错误跟踪信息</button>
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-server" type="button">$_SERVER</button>
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-get" type="button">$_GET</button>
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-post" type="button">$_POST</button>
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-request" type="button">$_REQUEST</button>
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-cookie" type="button">$_COOKIE</button>
            </div>
        </nav>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-trace">
                <pre class="prettyprint linenums"><?php print_r($this->e->getTrace()); ?></pre>
            </div>
            <div class="tab-pane fade" id="tab-server">
                <pre class="prettyprint linenums"><?php print_r($request->server()) ?></pre>
            </div>
            <div class="tab-pane fade" id="tab-get">
                <pre class="prettyprint linenums"><?php print_r($request->get()) ?></pre>
            </div>
            <div class="tab-pane fade" id="tab-post">
                <pre class="prettyprint linenums"><?php print_r($request->post()) ?></pre>
            </div>
            <div class="tab-pane fade" id="tab-request">
                <pre class="prettyprint linenums"><?php print_r($request->request()) ?></pre>
            </div>
            <div class="tab-pane fade" id="tab-cookie">
                <pre class="prettyprint linenums"><?php print_r($request->cookie()) ?></pre>
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
        <div class="text-center display-1 text-danger mt-5">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>

        <div class="text-center fs-4 mt-5">
            <?php echo $this->e->getMessage(); ?>
        </div>
        <?php
    }
    ?>
    </div>
</be-body>
