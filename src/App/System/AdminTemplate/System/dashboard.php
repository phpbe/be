<?php
use Be\Be;
?>

<be-head>
<link type="text/css" rel="stylesheet" href="<?php echo Be::getProperty('App.System')->getUrl(); ?>/AdminTemplate/System/css/dashboard.css">
<script type="text/javascript" language="javascript" src="<?php echo Be::getProperty('App.System')->getUrl(); ?>/AdminTemplate/System/js/dashboard.js"></script>
</be-head>

<be-center>
<?php
$my = Be::getAdminUser();
$configAdminUser = Be::getConfig('App.System.AdminUser');
?>
<div id="app">

    <el-row :gutter="20">
        <el-col :span="12">

            <el-card shadow="hover" style="height: 180px;">
                <el-image src="<?php
                if ($this->adminUser->avatar == '') {
                    echo Be::getProperty('App.System')->getUrl().'/AdminTemplate/AdminUser/images/avatar.png';
                } else {
                    echo Be::getRequest()->getUploadUrl().'/System/AdminUser/Avatar/'.$this->adminUser->avatar;
                }
                ?>"></el-image>

                <div style="font-size:14px; font-weight: bold;"><?php echo $this->adminUser->name; ?>（<?php echo $my->getRoleName(); ?>）</div>
                <div style="color: #999;font-size: 12px;">上次登陆时间：<?php echo $this->adminUser->last_login_time; ?></div>
            </el-card>

        </el-col>

        <el-col :span="4">
            <el-card shadow="hover" style="height: 180px; text-align:center;">
                <div slot="header" class="clearfix">
                    <span>应用数</span>
                </div>

                <el-link href="<?php echo beAdminUrl('System.App.apps'); ?>" style="font-size:36px; ">
                    <?php echo $this->appCount; ?>
                </el-link>
            </el-card>
        </el-col>


        <el-col :span="4">
            <el-card shadow="hover" style="height: 180px; text-align:center;">
                <div slot="header" class="clearfix">
                    <span>主题数</span>
                </div>

                <el-link href="<?php echo beAdminUrl('System.Theme.themes'); ?>" style="font-size:36px; ">
                    <?php echo $this->themeCount;; ?>
                </el-link>
            </el-card>
        </el-col>


        <el-col :span="4">
            <el-card shadow="hover" style="height: 180px; text-align:center;">
                <div slot="header" class="clearfix">
                    <span>用户数</span>
                </div>

                <el-link href="<?php echo beAdminUrl('System.AdminUser.users'); ?>" style="font-size:36px; ">
                    <?php echo $this->adminUserCount; ?>
                </el-link>
            </el-card>
        </el-col>

    </el-row>




    <el-row :gutter="20" style="margin-top: 20px;">
        <el-col :span="12">

            <el-card shadow="hover">
                <div slot="header" class="clearfix">
                    <span>最近操作日志</span>
                    <el-button style="float: right; padding: 3px 0" type="text" @click="window.location.href='<?php echo beAdminUrl('System.SystemLog.logs')?>'">更多..</el-button>
                </div>

                <el-table :data="recentLogs" stripe size="mini">
                    <el-table-column
                            prop="create_time"
                            label="时间"
                            width="180"
                            align="center">
                        <template slot-scope="scope">
                            <div v-html="scope.row.create_time"></div>
                        </template>
                    </el-table-column>
                    <el-table-column
                            prop="content"
                            label="操作">
                    </el-table-column>
                </el-table>

            </el-card>

        </el-col>

        <el-col :span="12">

            <el-card shadow="hover">
                <div slot="header" class="clearfix">
                    <span>最近登录日志</span>
                    <el-button style="float: right; padding: 3px 0" type="text" @click="window.location.href='<?php echo beAdminUrl('System.UserLoginLog.logs')?>'">更多..</el-button>
                </div>

                <el-table :data="recentLoginLogs" stripe size="mini">
                    <el-table-column
                            prop="create_time"
                            label="时间"
                            width="180"
                            align="center">
                        <template slot-scope="scope">
                            <div v-html="scope.row.create_time"></div>
                        </template>
                    </el-table-column>
                    <el-table-column
                            prop="description"
                            label="操作">
                    </el-table-column>
                </el-table>

            </el-card>

        </el-col>
    </el-row>


</div>

<?php
foreach ($this->recentLogs as $log) {
    $log->create_time = date('Y-m-d H:i', strtotime($log->create_time));
}

foreach ($this->recentLoginLogs as $log) {
    $log->create_time = date('Y-m-d H:i', strtotime($log->create_time));
}
?>
<script>
    var vue = new Vue({
        el: '#app',
        data: {
            recentLogs : <?php echo json_encode($this->recentLogs); ?>,
            recentLoginLogs : <?php echo json_encode($this->recentLoginLogs); ?>
        },
        methods: {
        }
    });
</script>
</be-center>