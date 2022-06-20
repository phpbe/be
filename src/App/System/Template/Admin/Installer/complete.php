<be-head>
    <style>
        .success-icon {
            text-align: center; font-size: 120px; padding: 60px 0 20px 0; color: #67c23a;
        }

        .success-message {
            text-align: center; font-size: 18px; height: 50px; line-height: 50px;
        }
    </style>
</be-head>

<be-center>
    <div id="app" v-cloak>
        <div class="success-icon">
            <i class="el-icon-success"></i>
        </div>

        <div class="success-message">
            安装完成！<a href="<?php echo beAdminUrl(); ?>">进入系统</a>
        </div>

    </div>
    <script>
        new Vue({
            el: '#app',
            data: {}
        });
    </script>
</be-center>
