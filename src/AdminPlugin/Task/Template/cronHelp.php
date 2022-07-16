<be-head>
    <style type="text/css">
        body {color: #666;}
        code { color: #999; padding: 0 10px;}
    </style>
</be-head>

<be-page-content>
    <div class="be-bc-fff be-px-100 be-pt-100 be-pb-50" id="app" v-cloak>
        <h3>部置密码</h3>
        <hr>
        <ul>
            <li>部置密码用来防止非法用户匿名访问触发计划任务</li>
            <li>部置密码在 系统 -> 系统配置 -> 系统配置 下的 "计划任务" 中设置</li>
            <?php
            if ($this->configTaskPassword) {
                ?>
                <li>系统自动帮您生成了部署密码：<code><?php echo $this->configTask->password; ?></code></li>
                <?php
            } else {
                ?>
                <li>当前部署密码：<code><?php echo $this->configTask->password; ?></code></li>
                <?php
            }
            ?>
        </ul>

        <h3>Linux crontab 配置</h3>
        <hr>
        <p>
            <code># 每分钟访问一次任务调度中心</code><br>
            <code>* * * * * curl <?php echo beUrl('System.Task.schedule', ['password' => $this->configTask->password ]); ?></code>
        </p>

        <h3>Windows 计划任务 配置</h3>
        <hr>
        <p>
            请查阅相关资料，配置 windows 计划任务每分钟访问任务调度中心网址：<br>
            <code>
                <?php echo beUrl('System.Task.schedule', ['password' => $this->configTask->password ]); ?>
            </code>
        </p>

    </div>

    <script>
        var vueLists = new Vue({
            el: '#app'
        });
    </script>
</be-page-content>
