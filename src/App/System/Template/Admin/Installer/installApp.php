<be-head>
    <link type="text/css" rel="stylesheet" href="<?php echo \Be\Be::getProperty('App.System')->getUrl(); ?>/Template/Admin/Installer/css/complete.css">
</be-head>

<be-center>
    <div id="app" v-cloak>
        <el-form size="medium" label-width="150px" ref="formRef">
            <div style="padding: 20px 0">
                <el-table :data="tableData" ref="tableRef" @selection-change="selectionChange">
                    <el-table-column type="selection" width="50"></el-table-column>
                    <el-table-column prop="icon" label="图标" width="90" align="center">
                        <template slot-scope="scope">
                            <el-icon :class="scope.row.icon"></el-icon>
                        </template>
                    </el-table-column>
                    <el-table-column prop="name" label="名称" width="150" align="center"></el-table-column>
                    <el-table-column prop="label" label="中文名称" width="150" align="center"></el-table-column>
                    <el-table-column prop="description" label="描述"></el-table-column>
                </el-table>
            </div>

            <el-form-item>
                <el-button type="primary" @click="submit" :disabled="loading">继续安装</el-button>
            </el-form-item>

        </el-form>
    </div>
    <script>
        new Vue({
            el: '#app',
            data: {
                formData:{
                    appNames: []
                },
                loading: false,
                tableData: <?php echo json_encode($this->appProperties); ?>
            },
            methods: {
                selectionChange: function(rows) {
                    var arrAppNames = [];
                    for (var i=0; i<rows.length; i++) {
                        var row = rows[i];
                        arrAppNames.push(row.name);
                    }

                    if (arrAppNames.indexOf("System") === -1) {
                        arrAppNames.push("System");
                        this.$refs.tableRef.toggleRowSelection(this.tableData[0]);
                    }

                    this.formData.appNames = arrAppNames;
                },
                submit: function () {
                    var _this = this;
                    this.loading = true;
                    this.$http.post("<?php echo beAdminUrl('System.Installer.installApp'); ?>", {
                        formData: this.formData
                    }).then(function (response) {
                        _this.loading = false;
                        console.log(response);
                        if (response.status == 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                window.location.href=responseData.redirectUrl;
                            } else {
                                if (responseData.message) {
                                    _this.$message.error(responseData.message);
                                }
                            }
                        }
                    }).catch(function (error) {
                        _this.loading = false;
                        _this.$message.error(error);
                    });

                }
            },
            mounted: function () {
                for (var i=0; i<this.tableData.length; i++) {
                    this.$refs.tableRef.toggleRowSelection(this.tableData[i]);
                }
            }
        });
    </script>
</be-center>
