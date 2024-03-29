<be-head>
    <style type="text/css">
        html {
            font-size: 14px;
            background-color: #fff;
            color: #333;
        }

        body {
            text-align: center;
            background-color: #fff;
        }

        #app {
            margin: 0 auto;
            width: 350px;
        }

        .logo {
            margin: 50px 0 0 0;
        }

        .logo img,
        .logo svg {
            width: 300px;
            height: 90px;
        }

        .login-box {
            text-align: left;
            margin-top: 40px;
            padding: 30px 30px 10px 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px #bbb;
        }
    </style>
</be-head>

<be-body>
<?php
$config = \Be\Be::getConfig('App.System.System');
?>
<div id="app" v-cloak>

    <div class="logo">
        <?php
        $configTheme = \Be\Be::getConfig('AdminTheme.System.Theme');
        if ($configTheme->logo !== '') {
            echo '<img src="' . $configTheme->logo . '">';
        } else {
            ?>
            <svg viewBox="0 0 200 60" xmlns="http://www.w3.org/2000/svg">
                <rect rx="5" height="40" width="40" x="10" y="10" fill="#ff5c35"/>
                <path d="M16 29 L21 29 M21 42 L16 42 L16 17 L21 17 C30 17 30 29 21 29 C30 30 30 42 21 42 M45 17 L34 17 L34 42 L46 42 M35 29 L44 29" stroke="#ffffff" stroke-width="2" fill="none" />
                <text x="65" y="35" style="font-size: 14px;"><tspan fill="#ff5c35">B</tspan><tspan fill="#999999">eyond</tspan> <tspan fill="#ff5c35">E</tspan><tspan fill="#999999">xception</tspan></text>
            </svg>
            <?php
        }
        ?>
    </div>

    <div class="login-box">
        <el-form size="small" layout="horizontal" ref="loginForm" :model="formData" label-width="80px">
            <el-form-item label="用户名" prop="username">
                <el-input v-model="formData.username" placeholder="用户名" prefix-icon="el-icon-user" clearable></el-input>
            </el-form-item>
            <el-form-item label="密码" prop="password">
                <el-input v-model="formData.password" placeholder="密码" prefix-icon="el-icon-lock" show-password clearable></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" native-type="submit" @click="login" :loading="loginLoading">
                    <el-icon type="unlock"></el-icon>登录
                </el-button>
                <el-button @click="resetForm">重置</el-button>
            </el-form-item>
        </el-form>
    </div>

</div>

<?php
$return = \Be\Be::getRequest()->get('return', '');
if ($return=='') {
    $return = beAdminUrl('System.Home.index');
} else {
    $return = base64_decode($return);
}
?>
<script>
    new Vue({
        el: '#app',
        data: {
            formData: {
                username : "",
                password : ""
            },
            loginLoading: false
        },
        methods: {
            login: function() {
                var _this = this;
                _this.loginLoading = true;
                this.$http.post("<?php echo beAdminUrl('System.AdminUserLogin.login'); ?>", _this.formData)
                    .then(function (response) {
                        _this.loginLoading = false;
                        if (response.status === 200) {
                            if (response.data.success) {
                                window.location.href = "<?php echo $return; ?>";
                            } else {
                                _this.$message.error(response.data.message);
                            }
                        }
                    })
                    .catch(function (error) {
                        _this.loginLoading = false;
                        _this.$message.error(error);
                    });

            },
            resetForm: function () {
                this.$refs["loginForm"].resetFields();
            }
        }
    });
</script>

</be-body>