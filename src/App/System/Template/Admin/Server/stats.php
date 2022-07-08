<be-center>
    <div id="app" v-cloak>
        <div class="be-center">
            <div class="be-content-title">服务器状态</div>

            <div class="be-content-body">

                <div class="be-row be-fs-120">
                    <div class="be-col-auto">
                        <div class="be-pr-100 be-fw-bold">
                            工作模式：
                        </div>
                    </div>
                    <div class="be-col">
                        <?php echo $this->isSwooleMode? 'Swoole 模式': '普通PHP模式'; ?>
                    </div>
                </div>

                <div class="be-row be-mt-150">
                    <div class="be-col-auto">
                        <div class="be-pr-100 be-fw-bold">
                            PHP版本：
                        </div>
                    </div>
                    <div class="be-col">
                        <?php echo $this->phpversion; ?>
                        <el-link class="be-ml-100" type="primary" href="<?php echo beAdminUrl('System.Server.phpinfo'); ?>" target="_blank">phpinfo 信息</el-link>
                    </div>
                </div>

                <?php
                if ($this->isSwooleMode) {
                    ?>
                    <el-table class="be-mt-150" :data="serverStats">
                        <el-table-column prop="name" label="Swoole 服务器参数项" align="right" width="300"></el-table-column>
                        <el-table-column prop="value" label="参数值" align="left"></el-table-column>
                    </el-table>
                    <?php
                }
                ?>

            </div>
        </div>
    </div>
    <script>
        let vueCenter = new Vue({
            el: '#app',
            data: {
                serverStats: <?php echo json_encode($this->serverStats); ?>,
                t: false
            },
            methods: {
                t: function () {
                }
            },
        });
    </script>
</be-center>