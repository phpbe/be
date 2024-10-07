<be-head>
<?php
$configSystem = \Be\Be::getConfig('App.System.System');
if ($configSystem->developer) {
    $appSystemWwwUrl = \Be\Be::getProperty('App.System')->getWwwUrl();
    ?>
    <link rel="stylesheet" href="<?php echo $appSystemWwwUrl; ?>/lib/google-code-prettify/prettify.css" type="text/css"/>
    <script type="text/javascript" language="javascript" src="<?php echo $appSystemWwwUrl; ?>/lib/google-code-prettify/prettify.js"></script>
    <style type="text/css">
        pre.prettyprint {
            background-color: #fff;
            color: #000;
            white-space: pre-wrap;
            word-wrap: break-word;
            border-color: #ddd;
        }
    </style>
    <?php
}
?>
</be-head>

<be-body>
<?php
$configSystem = \Be\Be::getConfig('App.System.System');
if ($configSystem->developer) {
    $request = \Be\Be::getRequest();
    ?>
    <div id="app" v-cloak>
        <el-alert
                title="<?php echo htmlspecialchars($this->e->getMessage()); ?>"
                type="error"
                description="<?php if (isset($this->logId)) { echo '<div>#' . $this->logId . '</div>';} ?>"
                show-icon>
        </el-alert>

        <el-tabs v-model="activeTab" type="border-card" style="margin-top:10px;">
            <el-tab-pane label="错误跟踪信息" name="tab-trace">
                <pre class="prettyprint linenums"><?php print_r($this->e->getTrace()); ?></pre>
            </el-tab-pane>
            <el-tab-pane label="GET" name="tab-get">
                <pre class="prettyprint linenums"><?php print_r(Request::get()) ?></pre>
            </el-tab-pane>
            <el-tab-pane label="POST" name="tab-post">
                <pre class="prettyprint linenums"><?php print_r(Request::post()) ?></pre>
            </el-tab-pane>
            <el-tab-pane label="REQUEST" name="tab-request">
                <pre class="prettyprint linenums"><?php print_r(Request::request()) ?></pre>
            </el-tab-pane>
            <el-tab-pane label="COOKIE" name="tab-cookie">
                <pre class="prettyprint linenums"><?php print_r(Request::cookie()) ?></pre>
            </el-tab-pane>
            <el-tab-pane label="头信息" name="tab-server">
                <pre class="prettyprint linenums"><?php print_r(Request::header()) ?></pre>
            </el-tab-pane>
            <el-tab-pane label="SERVER" name="tab-server">
                <pre class="prettyprint linenums"><?php print_r(Request::server()) ?></pre>
            </el-tab-pane>
        </el-tabs>
    </div>
    <script>
        new Vue({
            el: '#app',
            data: {
                activeTab: 'tab-trace'
            },
            created: function () {
                prettyPrint();
            }
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
</be-body>
