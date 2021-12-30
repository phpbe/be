<be-head>
<link type="text/css" rel="stylesheet" href="<?php echo \Be\Be::getProperty('App.System')->getUrl(); ?>/Template/Admin/AdminUserLogin/css/login.css?v=20211230" />
</be-head>

<be-body>
<?php
$config = \Be\Be::getConfig('App.System.System');
?>
<div id="app">

    <div class="logo">

        <div class="be-row">
            <div class="be-col-auto">
                <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                    <rect rx="8" height="64" width="64" y="0" x="0" fill="#ff6600"/>
                    <path d="M9 31 L15 31 M15 52 L9 52 L9 11 L15 11 C32 11 32 31 15 31 C32 32 32 52 15 52 M57 11 L39 11 L39 52 L57 52 M39 31 L55 31" stroke="#ffffff" stroke-width="4" fill="none" />
                </svg>
            </div>
            <div class="be-col">
                <div class="logo-text"><span>B</span>eyond <span>E</span>xception</div>
            </div>
        </div>

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
    $return = beAdminUrl('System.Index.index');
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
                        if (response.status == 200) {
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