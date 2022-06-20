<be-head>
    <?php
    $appWwwUrl = \Be\Be::getProperty('App.System')->getWwwUrl();
    ?>
    <link rel="stylesheet" href="<?php echo $appWwwUrl; ?>/lib/google-code-prettify/prettify.css" type="text/css"/>
    <script type="text/javascript" language="javascript" src="<?php echo $appWwwUrl; ?>/lib/google-code-prettify/prettify.js"></script>
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

    <div id="app" v-cloak style="padding: 0 20px;">

        <el-tabs v-model="activeTab">
            <el-tab-pane label="日志基本信息" name="tab-base">
                <el-form label-width="120px" size="medium" style="padding-top: 12px;">
                    <el-form-item label="日志编号">
                        <?php echo $this->log['id'] ?? ''; ?>
                    </el-form-item>
                    <el-form-item label="日志级别">
                        <?php echo $this->log['level'] ?? ''; ?>
                    </el-form-item>
                    <el-form-item label="文件">
                        <?php echo $this->log['file'] ?? ''; ?>
                    </el-form-item>
                    <el-form-item label="行号">
                        <?php echo $this->log['line'] ?? ''; ?>
                    </el-form-item>
                    <el-form-item label="错误码">
                        <?php echo $this->log['code'] ?? ''; ?>
                    </el-form-item>
                    <el-form-item label="错误信息">
                        <?php echo $this->log['message'] ?? ''; ?>
                    </el-form-item>
                    <el-form-item label="首次记录时间">
                        <?php echo $this->log['create_time'] ?? ''; ?>
                    </el-form-item>
                </el-form>
            </el-tab-pane>

            <el-tab-pane label="日志跟踪信息" name="tab-trace">
                <pre class="prettyprint linenums"><?php print_r($this->log['trace'] ?? ''); ?></pre>
            </el-tab-pane>

            <?php
            $configSystemLog = \Be\Be::getConfig('App.System.Log');

            if (isset($configSystemLog->get) && $configSystemLog->get) {
                ?>
                <el-tab-pane label="GET" name="tab-get">
                    <pre class="prettyprint linenums"><?php print_r($this->log['get'] ?? ''); ?></pre>
                </el-tab-pane>
                <?php
            }

            if (isset($configSystemLog->post) && $configSystemLog->post) {
                ?>
                <el-tab-pane label="POST" name="tab-post">
                    <pre class="prettyprint linenums"><?php print_r($this->log['post'] ?? ''); ?></pre>
                </el-tab-pane>
                <?php
            }

            if (isset($configSystemLog->request) && $configSystemLog->request) {
                ?>
                <el-tab-pane label="REQUEST" name="tab-request">
                    <pre class="prettyprint linenums"><?php print_r($this->log['request'] ?? ''); ?></pre>
                </el-tab-pane>
                <?php
            }

            if (isset($configSystemLog->cookie) && $configSystemLog->cookie) {
                ?>
                <el-tab-pane label="COOKIE" name="tab-cookie">
                    <pre class="prettyprint linenums"><?php print_r($this->log['cookie'] ?? ''); ?></pre>
                </el-tab-pane>
                <?php
            }

            if (isset($configSystemLog->session) && $configSystemLog->session) {
                ?>
                <el-tab-pane label="SESSION" name="tab-session">
                    <pre class="prettyprint linenums"><?php print_r($this->log['session'] ?? ''); ?></pre>
                </el-tab-pane>
                <?php
            }

            if (isset($configSystemLog->header) && $configSystemLog->header) {
                ?>
                <el-tab-pane label="头信息" name="tab-header">
                    <pre class="prettyprint linenums"><?php print_r($this->log['header'] ?? ''); ?></pre>
                </el-tab-pane>
                <?php
            }

            if (isset($configSystemLog->server) && $configSystemLog->server) {
                ?>
                <el-tab-pane label="SERVER" name="tab-server">
                    <pre class="prettyprint linenums"><?php print_r($this->log['server'] ?? ''); ?></pre>
                </el-tab-pane>
                <?php
            }

            ?>
        </el-tabs>

    </div>

    <script>
        new Vue({
            el: '#app',
            data: {
                activeTab: 'tab-base'
            },
            created: function () {
                prettyPrint();
            }
        });
    </script>

</be-body>
