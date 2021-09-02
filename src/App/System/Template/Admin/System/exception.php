<be-head>
    <link type="text/css" rel="stylesheet"
          href="<?php echo \Be\Be::getProperty('App.System')->getUrl(); ?>/AdminTemplate/System/css/exception.css">
    <link rel="stylesheet"
          href="<?php echo \Be\Be::getProperty('App.System')->getUrl(); ?>/AdminTemplate/System/google-code-prettify/prettify.css"
          type="text/css"/>
    <script type="text/javascript" language="javascript"
            src="<?php echo \Be\Be::getProperty('App.System')->getUrl(); ?>/AdminTemplate/System/google-code-prettify/prettify.js"></script>
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

    <div id="app" v-cloak>
        <?php
        $configSystem = \Be\Be::getConfig('App.System.System');
        if ($configSystem->developer) {
            $request = \Be\Be::getRequest();
            ?>
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
                <el-tab-pane label="$_SERVER" name="tab-server">
                    <pre class="prettyprint linenums"><?php print_r($request->server()) ?></pre>
                </el-tab-pane>
                <el-tab-pane label="$_GET" name="tab-get">
                    <pre class="prettyprint linenums"><?php print_r($request->get()) ?></pre>
                </el-tab-pane>
                <el-tab-pane label="$_POST" name="tab-post">
                    <pre class="prettyprint linenums"><?php print_r($request->post()) ?></pre>
                </el-tab-pane>
                <el-tab-pane label="$_REQUEST" name="tab-request">
                    <pre class="prettyprint linenums"><?php print_r($request->request()) ?></pre>
                </el-tab-pane>
                <el-tab-pane label="$_COOKIE" name="tab-cookie">
                    <pre class="prettyprint linenums"><?php print_r($request->cookie()) ?></pre>
                </el-tab-pane>
            </el-tabs>
            <?php
        } else {
            ?>
            <div class="exception-icon">
                <i class="el-icon-warning"></i>
            </div>

            <div class="exception-message">
                <?php echo $this->e->getMessage(); ?>
            </div>
            <?php
        }
        ?>

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

</be-body>
