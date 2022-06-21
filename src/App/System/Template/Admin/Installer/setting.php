<be-head>
    <style>
        .be-input, .be-select {
            max-width: 300px;
        }

        .be-input[type="number"] {
            max-width:200px;
        }
    </style>
</be-head>

<be-center>
    <form id="form" action="<?php echo \Be\Be::getRequest()->getUrl(); ?>" method="post">
        <div class="be-row be-mt-200">
            <div class="be-col be-ta-right be-lh-250 be-c-999">
                超级管理员账号：
            </div>
            <div class="be-col">
                <input type="text" class="be-input" name="username" value="admin">
            </div>
        </div>

        <div class="be-row be-mt-200">
            <div class="be-col be-ta-right be-lh-250 be-c-999">
                密码：
            </div>
            <div class="be-col">
                <input type="text" class="be-input" name="password" value="admin">
            </div>
        </div>

        <div class="be-row be-mt-200">
            <div class="be-col be-ta-right be-lh-250 be-c-999">
                名称：
            </div>
            <div class="be-col">
                <input type="text" class="be-input" name="name" value="管理员">
            </div>
        </div>

        <div class="be-row be-mt-200">
            <div class="be-col be-ta-right be-lh-250 be-c-999">
                邮箱：
            </div>
            <div class="be-col">
                <input type="text" class="be-input" name="email" value="">
            </div>
        </div>

        <div class="be-mt-200 be-bt-eee be-pt-100 be-ta-center">
            <a class="be-btn" href="<?php echo beAdminUrl('System.Installer.installApp'); ?>">上一步</a>
            <input type="button" class="be-btn be-btn-main" value="完成安装" onclick="setting()">
        </div>

    </form>

    <script>
        function setting() {
            $.ajax({
                url: "<?php echo beAdminUrl('System.Installer.setting'); ?>",
                data : $("#form").serialize(),
                method: "POST",
                success: function (json) {
                    if (json.success) {
                        window.location.href = json.redirectUrl;
                    } else {
                        alert(json.message);
                    }
                },
                error: function () {
                    alert("系统错误!");
                }
            });
        }
    </script>
</be-center>
