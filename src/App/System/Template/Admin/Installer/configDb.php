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
                主机名：
            </div>
            <div class="be-col">
                <input type="text" class="be-input" name="host" value="<?php echo $this->configDb->master['host']; ?>">
            </div>
        </div>

        <div class="be-row be-mt-200">
            <div class="be-col be-ta-right be-lh-250 be-c-999">
                端口号：
            </div>
            <div class="be-col">
                <input type="number" min="1" max="65535" class="be-input" name="port" value="<?php echo $this->configDb->master['port']; ?>">
            </div>
        </div>

        <div class="be-row be-mt-200">
            <div class="be-col be-ta-right be-lh-250 be-c-999">
                用户名：
            </div>
            <div class="be-col">
                <input type="text" class="be-input" name="username" value="<?php echo $this->configDb->master['username']; ?>">
            </div>
        </div>

        <div class="be-row be-mt-200">
            <div class="be-col be-ta-right be-lh-250 be-c-999">
                密码：
            </div>
            <div class="be-col">
                <input type="text" class="be-input" name="password" value="<?php echo $this->configDb->master['password']; ?>">
            </div>
        </div>

        <div class="be-row be-mt-200">
            <div class="be-col be-ta-right be-lh-250 be-c-999">
            </div>
            <div class="be-col">
                <input  type="button" class="be-btn be-btn-green" value="测试连接，并获取库名列表" onclick="testDb()">
            </div>
        </div>

        <div class="be-row be-mt-200">
            <div class="be-col be-ta-right be-lh-250 be-c-999">
                库名：
            </div>
            <div class="be-col">
                <div id="name-container"></div>
            </div>
        </div>

        <div class="be-row be-mt-200">
            <div class="be-col be-ta-right be-lh-250 be-c-999">
                连接池大小（0：不启用）：
            </div>
            <div class="be-col">
                <input type="number" min="0" max="1000" class="be-input" name="pool" value="<?php echo $this->configDb->master['pool']; ?>">
            </div>
        </div>

        <div class="be-mt-200 be-bt-eee be-pt-100 be-ta-center">
            <a class="be-btn" href="<?php echo beAdminUrl('System.Installer.detect'); ?>">上一步</a>
            <input type="button" class="be-btn be-btn-main" value="继续安装" onclick="configDb()">
        </div>

    </form>

    <script type="text/html" id="name-input-template">
        <input type="text" class="be-input" name="name" value="<?php echo $this->configDb->master['name']; ?>">
    </script>

    <script type="text/html" id="name-select-template">
        <select class="be-select" id="name-select" name="name">
            <option value="<?php echo $this->configDb->master['name']; ?>"><?php echo $this->configDb->master['name']; ?></option>
        </select>
    </script>

    <script>
        $(function () {
            $("#name-container").html($("#name-input-template").html());
        });

        function testDb() {
            $.ajax({
                url: "<?php echo beAdminUrl('System.Installer.testDb'); ?>",
                data : $("#form").serialize(),
                method: "POST",
                success: function (json) {
                    alert(json.message);
                    if (json.success && json.data.databases && json.data.databases.length > 0) {
                        $("#name-container").html($("#name-select-template").html());
                        setTimeout(function () {
                            let el = document.getElementById("name-select");
                            el.options.length = 0;
                            for(let name of json.data.databases) {
                                let option = new Option(name, name);
                                el.options.add(option);
                            }
                        }, 100);
                    } else {
                        $("#name-container").html($("#name-input-template").html());
                    }
                },
                error: function () {
                    alert("System Error!");
                }
            });
        }

        function configDb() {
            $.ajax({
                url: "<?php echo beAdminUrl('System.Installer.configDb'); ?>",
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
