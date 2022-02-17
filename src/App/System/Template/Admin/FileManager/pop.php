<be-head>
    <style type="text/css">
        .file-manager {

        }

        .file-manager-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            padding: 5px 10px;
            border-top: 1px solid #f1f1f1;
            border-bottom: 1px solid #f1f1f1;
            background-color: #fff;
            z-index: 999;
        }

        .file-manager-breadcrumb {
            margin-top: 50px;
            padding: 10px;
            border-bottom: 1px solid #f6f6f6;
        }

        .file-manager-files {
            position: fixed;
            top: 80px;
            left: 0;
            right: 0;
            bottom: 0;
            overflow-y: auto;
        }


        .file-manager-view-thumbnail {
            padding: 10px;
        }

        .file-manager-view-thumbnail ul {
            padding: 0;
            margin: 0;
        }

        .file-manager-view-thumbnail li {
            display: inline-block;
            width: 120px;
            height: 150px;
        }

        .file-manager-view-thumbnail li:hover {
            background-color: #fafafa;
        }

        .file-manager-view-thumbnail .file {
            padding: 5px;
            position: relative;
        }

        .file-manager-view-thumbnail .file-selector {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 999;
            display: none;
        }

        .file-manager-view-thumbnail .file-selector i {
            font-size: 28px;
        }

        .file-manager-view-thumbnail li:hover .file-selector {
            display: block;
        }

        .file-manager-view-thumbnail .file-check .file-selector {
            display: block !important;
        }

        .file-manager-view-thumbnail .file-icon {
            width: 110px;
            height: 90px;
            line-height: 90px;
            overflow: hidden;
            text-align: center;
        }

        .file-manager-view-thumbnail .file-icon img {
            height: auto;
            max-width: 80px;
            max-height: 80px;
            vertical-align: middle;
        }

        .file-manager-view-thumbnail .file-name {
            height: 50px;
            line-height: 25px;
            overflow: hidden;
            text-align: center;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }


        .file-manager-view-list {
        }

        .file-manager-view-list .file-icon {
        }

        .file-manager-view-list .file-icon img {
            height: auto;
            max-width: 24px;
            max-height: 24px;
            vertical-align: middle;
        }

        .file-manager-view-list .file-size {
            color: #999;
        }

        .file-manager-view-list .file-update-time {
            color: #999;
        }

        .file-manager-view-list .file-operation i {
            font-size: 18px;
        }

        .file-manager-upload-image{
            max-height: 400px;
            overflow-y: auto;
        }

        .file-manager-upload-file .el-upload {
            width: 100%;
        }

        .file-manager-upload-file .el-upload-dragger {
            width: 100%;
            height: 120px;
        }

        .file-manager-upload-file .el-upload-dragger .el-icon-upload {
            margin-top: 10px;
        }

        .file-manager-upload-file .el-upload-list {
            max-height: 200px;
            overflow-y: auto;
        }

    </style>
</be-head>

<be-body>
    <?php
    $filterName = $this->filterImage === 1 ? '图片' : '文件';
    $configSystem = \Be\Be::getConfig('App.System.System');
    $configWatermark = \Be\Be::getConfig('App.System.Watermark');
    $templateUrl = \Be\Be::getProperty('App.System')->getUrl();
    ?>

    <div id="app" v-cloak>

        <div class="file-manager">

            <div class="file-manager-toolbar">
                <div class="be-row">
                    <div class="be-col-auto">
                        <el-button type="default" size="medium" icon="el-icon-folder-add" @click="createDir">新建文件夹</el-button>
                        <el-button type="default" size="medium" icon="el-icon-upload2" @click="uploadFile()">上传<?php echo $filterName; ?></el-button>
                    </div>
                    <div class="be-col"></div>
                    <div class="be-col-auto">
                        <el-tooltip class="item" effect="dark" content="缩略图视图" placement="bottom">
                            <el-button :type="formData.view === 'thumbnail' ? 'primary':'default'" size="medium" icon="el-icon-s-grid" @click="setView('thumbnail')"></el-button>
                        </el-tooltip>

                        <el-tooltip class="item" effect="dark" content="列表视图" placement="bottom">
                            <el-button :type="formData.view === 'list' ? 'primary':'default'" size="medium" icon="el-icon-s-fold" @click="setView('list')"></el-button>
                        </el-tooltip>
                    </div>
                </div>
            </div>

            <div class="file-manager-breadcrumb">
                <el-breadcrumb separator="/">
                    <el-breadcrumb-item><a href="javascript:void(0);" @click="setPath('/')"><i class="el-icon-s-home"></i></a></el-breadcrumb-item>
                    <el-breadcrumb-item v-for="item in breadcrumb"><a href="javascript:void(0);" @click="setPath(item[0])">{{item[1]}}</a></el-breadcrumb-item>
                </el-breadcrumb>
            </div>

            <div class="file-manager-files" v-loading="loading">
                <template v-if="formData.view==='thumbnail'">
                    <div class="file-manager-view-thumbnail">
                        <ul>
                            <template v-for="file in files">
                                <li>
                                    <div v-if="file.type === 'dir'" class="file">
                                        <div class="file-icon">
                                            <el-link :title="file.name" @click="setPath(formData.path + file.name + '/')" :underline="false">
                                                <el-image src="<?php echo $templateUrl; ?>/Template/Admin/FileManager/images/types/folder.png"></el-image>
                                            </el-link>
                                        </div>
                                        <div class="file-name">
                                            <el-link :title="file.name" @click="setPath(formData.path + file.name + '/')" :underline="false">{{file.name}}</el-link>
                                        </div>
                                    </div>
                                    <div v-else :class="{'file': true, 'file-check': file.selected}">
                                        <div class="file-selector">
                                            <el-link :type="file.selected?'success':'info'" icon="el-icon-success" @click="selectFile(file)" :underline="false"></el-link>
                                        </div>

                                        <div class="file-icon">
                                            <el-link :title="file.name" @click="selectFile(file)" :underline="false">
                                                <el-image v-if="imageTypes.indexOf(file.type) !== -1" :src="file.url"></el-image>
                                                <el-image v-else-if="fileTypes.indexOf(file.type) !== -1" :src="'<?php echo $templateUrl; ?>/Template/Admin/FileManager/images/types/' + file.type + '.png'"></el-image>
                                                <el-image v-else src="<?php echo $templateUrl; ?>/Template/Admin/FileManager/images/types/unknown.png"></el-image>
                                            </el-link>
                                        </div>
                                        <div class="file-name">
                                            <el-link :title="file.name" @click="selectFile(file)" :underline="false">{{file.name}}</el-link>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>
                </template>

                <template v-else-if="formData.view==='list'">
                    <div class="file-manager-view-list">

                        <el-table ref="filesTableRef" @selection-change="tableSelectionChange" :data="files" size="medium">

                            <template slot="empty">
                                <el-empty description="暂无文件"></el-empty>
                            </template>

                            <el-table-column type="selection" :selectable="fileSelectable" align="center" width="50"></el-table-column>

                            <el-table-column label="" align="center" width="50">
                                <template slot-scope="scope">
                                    <div v-if="scope.row.type === 'dir'" class="file-icon">
                                        <el-link @click="setPath(formData.path + scope.row.name + '/')" :underline="false">
                                            <el-image src="<?php echo $templateUrl; ?>/Template/Admin/FileManager/images/types/folder_s.png"></el-image>
                                        </el-link>
                                    </div>
                                    <div v-else class="file-icon">
                                        <el-link @click="selectFile(scope.row)" :underline="false">
                                            <el-image v-if="imageTypes.indexOf(scope.row.type) !== -1" :src="scope.row.url"></el-image>
                                            <el-image v-else-if="fileTypes.indexOf(scope.row.type) !== -1" :src="'<?php echo $templateUrl; ?>/Template/Admin/FileManager/images/types/' + scope.row.type + '_s.png'"></el-image>
                                            <el-image v-else src="<?php echo $templateUrl; ?>/Template/Admin/FileManager/images/types/unknown_s.png"></el-image>
                                        </el-link>
                                    </div>
                                </template>
                            </el-table-column>


                            <el-table-column label="文件名">
                                <template slot-scope="scope">
                                    <div class="file-name">
                                        <el-link @click="scope.row.type === 'dir' ? setPath(formData.path + scope.row.name + '/') : selectFile(scope.row)" :underline="false">{{scope.row.name}}</el-link>
                                    </div>
                                </template>
                            </el-table-column>


                            <el-table-column label="大小" width="120" align="center">
                                <template slot-scope="scope">
                                    <template v-if="scope.row.type !== 'dir'">
                                        <div class="file-size">{{scope.row.sizeString}}</div>
                                    </template>
                                </template>
                            </el-table-column>


                            <el-table-column label="最后更改时间" width="180" align="center">
                                <template slot-scope="scope">
                                    <div class="file-update-time">{{scope.row.updateTime}}</div>
                                </template>
                            </el-table-column>


                            <el-table-column label="操作" align="center" width="120">
                                <template slot-scope="scope">
                                    <div class="file-operation">
                                        <template v-if="scope.row.type === 'dir'">
                                            <el-link type="primary" icon="el-icon-edit" @click="renameDir(scope.row)"></el-link>
                                            <el-link type="danger" icon="el-icon-delete" @click="deleteDir(scope.row)"></el-link>
                                        </template>
                                        <template v-else>
                                            <el-link type="success" icon="el-icon-view" :href="scope.row.url" target="_blank"></el-link>
                                            <el-link type="primary" icon="el-icon-edit" @click="renameFile(scope.row)"></el-link>
                                            <el-link type="danger" icon="el-icon-delete" @click="deleteFile(scope.row)"></el-link>
                                        </template>
                                    </div>
                                </template>
                            </el-table-column>
                        </el-table>

                    </div>
                </template>
            </div>

        </div>


        <el-dialog v-loading="createDirLoading" :visible.sync="createDirVisible" title="新建文件夹" :center="true" :close-on-click-modal="false" :close-on-press-escape="false">
            <el-form ref="createDirForm" :model="createDirFormData" label-width="120px" size="medium">
                <el-form-item label="文件夹名">
                    <el-input v-model="createDirFormData.dirName"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="createDirVisible=false" size="medium">取 消</el-button>
                <el-button type="primary" @click="createDirSave" size="medium">确 定</el-button>
            </div>
        </el-dialog>


        <el-dialog v-loading="renameDirLoading" :visible.sync="renameDirVisible" title="修改文件夹名称" :center="true" :close-on-click-modal="false" :close-on-press-escape="false">
            <el-form ref="renameDirForm" :model="renameDirFormData" label-width="120px" size="medium">
                <el-form-item label="当前文件夹名称">
                    {{renameDirFormData.oldDirName}}
                </el-form-item>

                <el-form-item label="新文件夹名称">
                    <el-input v-model="renameDirFormData.newDirName"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="renameDirVisible=false" size="medium">取 消</el-button>
                <el-button type="primary" @click="renameDirSave" size="medium">确 定</el-button>
            </div>
        </el-dialog>


        <el-dialog v-loading="uploadFileLoading" :visible.sync="uploadFileVisible" title="上传<?php echo $filterName; ?>" :center="true" :close-on-click-modal="false" :close-on-press-escape="false">
            <?php
            if ($this->filterImage === 1) {
                ?>
                <div class="file-manager-upload-image">
                    <el-upload
                            ref="uploadFileRef"
                            v-loading="uploadFileUploading"
                            element-loading-text="上传中..."
                            action="<?php echo beAdminUrl('System.FileManager.uploadImage'); ?>"
                            list-type="picture-card"
                            :file-list="uploadFiles"
                            :limit="100"
                            :on-exceed="uploadFileExceed"
                            :on-success="uploadFileSuccess"
                            :data="uploadFileFormData"
                            multiple>
                        <i slot="default" class="el-icon-plus"></i>
                        <div class="el-upload__tip" slot="tip">可上传 <?php echo implode('/', $configSystem->allowUploadImageTypes); ?> 文件，不超过<?php echo $configSystem->uploadMaxSize; ?></div>
                    </el-upload>
                </div>
                <?php
            } else {
                ?>
                <div class="file-manager-upload-file">
                    <el-upload
                            ref="uploadFileRef"
                            v-loading="uploadFileUploading"
                            element-loading-text="上传中..."
                            action="<?php echo beAdminUrl('System.FileManager.uploadFile'); ?>"
                            :file-list="uploadFiles"
                            :limit="100"
                            :on-exceed="uploadFileExceed"
                            :on-success="uploadFileSuccess"
                            :data="uploadFileFormData"
                            drag
                            multiple>
                        <i class="el-icon-upload"></i>
                        <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
                        <div class="el-upload__tip" slot="tip">可上传 <?php echo implode('/', $configSystem->allowUploadFileTypes); ?> 文件，不超过<?php echo $configSystem->uploadMaxSize; ?></div>
                    </el-upload>
                </div>
                <?php
            }
            ?>
        </el-dialog>


        <el-dialog v-loading="renameFileLoading" :visible.sync="renameFileVisible" title="修改<?php echo $filterName; ?>名称" :center="true" :close-on-click-modal="false" :close-on-press-escape="false">
            <el-form ref="renameFileForm" :model="renameFileFormData" label-width="120px" size="medium">
                <el-form-item label="当前<?php echo $filterName; ?>名称">
                    {{renameFileFormData.oldFileName}}
                </el-form-item>

                <el-form-item label="新<?php echo $filterName; ?>名称">
                    <el-input v-model="renameFileFormData.newFileName"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="renameFileVisible=false" size="medium">取 消</el-button>
                <el-button type="primary" @click="renameFileSave" size="medium">确 定</el-button>
            </div>
        </el-dialog>

    </div>

    <script>
        let vueCenter = new Vue({
            el: '#app',
            data: {
                loading: false,
                formData: {
                    path: "<?php echo $this->path; ?>",
                    view: "<?php echo $this->view; ?>"
                },
                files: [],
                selectedFiles: [],

                createDirVisible: false,
                createDirLoading: false,
                createDirFormData: {
                    path: "<?php echo $this->path; ?>",
                    dirName: ""
                },


                renameDirVisible: false,
                renameDirLoading: false,
                renameDirFormData: {
                    path: "<?php echo $this->path; ?>",
                    oldDirName: "",
                    newDirName: ""
                },


                uploadFileVisible: false,
                uploadFileLoading: false,
                uploadFileFormData: {
                    path: "<?php echo $this->path; ?>"
                },
                uploadFileUploading: false,
                uploadFiles: [],
                uploadFileCounter: 0,


                renameFileVisible: false,
                renameFileLoading: false,
                renameFileFormData: {
                    path: "<?php echo $this->path; ?>",
                    oldFileName: "",
                    newFileName: ""
                },

                filterImage: <?php echo $this->filterImage; ?>,
                imageTypes: ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'webp'],
                fileTypes: ["ac3","avi","bmp","css","csv","csv","dmg","doc","docx","exe","fla","flv","gif","gz","htm","html","jpeg","jpg","json","log","mid","mov","mp3","mp4","mpeg","mpg","ogg","pdf","png","ppt","pptx","psd","rar","rtf","sql","svg","swf","tar","tiff","txt","wav","wma","xhtml","xls","xlsx","xml","zip"],
                t: false
            },

            computed: {
                breadcrumb: function () {
                    let breadcrumb = [];
                    let pathName, path = "/";
                    let pathNames = this.formData.path.split('/');
                    for (pathName of pathNames) {
                        if (pathName === "") continue;
                        path += pathName + '/';
                        breadcrumb.push([path, pathName]);
                    }
                    return breadcrumb;
                }
            },
            methods: {
                loadData: function () {
                    this.loading = true;
                    var _this = this;
                    this.$http.post("<?php echo beAdminUrl('System.FileManager.pop', ['filterImage' => $this->filterImage]); ?>", {
                        formData: _this.formData
                    }).then(function (response) {
                        _this.loading = false;
                        //console.log(response);
                        if (response.status === 200) {
                            let responseData = response.data;
                            if (responseData.success) {
                                let files = responseData.files;
                                for (let file of files) {
                                    file.selected = false;
                                }
                                _this.files = files;
                            } else {
                                _this.files = [];
                                if (responseData.message) {
                                    _this.$message.error(responseData.message);
                                }
                            }
                        }
                    }).catch(function (error) {
                        _this.loading = false;
                        _this.$message.error(error);
                    });
                },
                setPath: function (path) {
                    this.formData.path = path;
                    this.loadData();
                },
                setView: function (view) {
                    this.formData.view = view;

                    this.$http.post("<?php echo beAdminUrl('System.FileManager.pop', ['filterImage' => $this->filterImage]); ?>", {
                        toggleView: 1,
                        formData: this.formData
                    });

                    if (view === 'list') {
                        var _this = this;
                        setTimeout(function (){
                            if (_this.selectedFiles.length > 0) {
                                for (let file of _this.selectedFiles) {
                                    _this.$refs.filesTableRef.toggleRowSelection(file, true);
                                }
                            }
                        }, 100);
                    }
                },

                createDir: function () {
                    this.createDirVisible = true;

                    let date = new Date();
                    let y = date.getFullYear();
                    let m = date.getMonth();
                    let d = date.getDate();
                    let dirName = y;
                    dirName += "-";
                    if (m < 10) dirName += "0";
                    dirName += m;
                    dirName += "-";
                    if (d < 10) dirName += "0";
                    dirName += d;

                    this.createDirFormData.path = this.formData.path;
                    this.createDirFormData.dirName = dirName;
                },
                createDirSave: function () {
                    this.createDirLoading = true;
                    var _this = this;
                    this.$http.post("<?php echo beAdminUrl('System.FileManager.createDir'); ?>", {
                        formData: _this.createDirFormData
                    }).then(function (response) {
                        _this.createDirLoading = false;
                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                _this.loadData();
                                _this.createDirVisible = false;
                            } else {
                                if (responseData.message) {
                                    _this.$message.error(responseData.message);
                                }
                            }
                        }
                    }).catch(function (error) {
                        _this.createDirLoading = false;
                        _this.$message.error(error);
                    });
                },

                renameDir: function (file) {
                    this.renameDirVisible = true;
                    this.renameDirFormData.path = this.formData.path;
                    this.renameDirFormData.oldDirName = file.name;
                    this.renameDirFormData.newDirName = file.name;
                },
                renameDirSave: function () {
                    this.renameDirLoading = true;
                    var _this = this;
                    this.$http.post("<?php echo beAdminUrl('System.FileManager.renameDir'); ?>", {
                        formData: _this.renameDirFormData
                    }).then(function (response) {
                        _this.renameDirLoading = false;
                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                _this.loadData();
                                _this.renameDirVisible = false;
                            } else {
                                if (responseData.message) {
                                    _this.$message.error(responseData.message);
                                }
                            }
                        }
                    }).catch(function (error) {
                        _this.renameDirLoading = false;
                        _this.$message.error(error);
                    });
                },

                deleteDir: function (file) {

                    var _this = this;

                    this.$confirm('此操作将永久删除该文件夹及文件夹下的所有文件, 是否继续?', '操作确认', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(function() {
                        _this.loading = true;
                        _this.$http.post("<?php echo beAdminUrl('System.FileManager.deleteDir'); ?>", {
                            formData: {
                                path: _this.formData.path,
                                dirName: file.name,
                            }
                        }).then(function (response) {
                            _this.loading = false;
                            if (response.status === 200) {
                                var responseData = response.data;
                                if (responseData.success) {
                                    _this.loadData();
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
                    }).catch(function(){});
                },

                uploadFile: function () {
                    this.uploadFileFormData.path = this.formData.path;

                    this.uploadFileVisible = true;
                    this.uploadFiles = [];
                    this.uploadFileCounter = 0;
                },
                uploadFileExceed: function (files, fileList) {
                    this.$message.warning("一次最多可上传100个文件！");
                },
                uploadFileSave: function () {
                    this.$refs.uploadFileRef.submit();
                },
                uploadFileSuccess: function (response, file, fileList) {
                    if (!response.success) {
                        this.$message({
                            showClose: true,
                            message: response.message,
                            type: "error",
                            duration: 10000
                        });
                        console.log(response);
                    }

                    this.uploadFileCounter++;

                    // 全部上传完成
                    if (this.uploadFileCounter >= fileList.length) {
                        this.loadData();
                        this.uploadFiles = [];
                        this.uploadFileVisible = false;
                    }
                },

                renameFile: function (file) {
                    this.renameFileVisible = true;
                    this.renameFileFormData.path = this.formData.path;
                    this.renameFileFormData.oldFileName = file.name;
                    this.renameFileFormData.newFileName = file.name;
                },

                renameFileSave: function () {
                    this.renameFileLoading = true;
                    var _this = this;
                    this.$http.post("<?php echo beAdminUrl('System.FileManager.renameFile', ['filterImage' => $this->filterImage]); ?>", {
                        formData: _this.renameFileFormData
                    }).then(function (response) {
                        _this.renameFileLoading = false;
                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                _this.loadData();
                                _this.renameFileVisible = false;
                            } else {
                                if (responseData.message) {
                                    _this.$message.error(responseData.message);
                                }
                            }
                        }
                    }).catch(function (error) {
                        _this.renameFileLoading = false;
                        _this.$message.error(error);
                    });
                },

                deleteFile: function (file) {
                    var _this = this;

                    this.$confirm('此操作将永久删除该文件, 是否继续?', '操作确认', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(function() {
                        _this.loading = true;
                        _this.$http.post("<?php echo beAdminUrl('System.FileManager.deleteFile'); ?>", {
                            formData: {
                                path: _this.formData.path,
                                fileName: file.name,
                            }
                        }).then(function (response) {
                            _this.loading = false;
                            if (response.status === 200) {
                                var responseData = response.data;
                                if (responseData.success) {
                                    _this.loadData();
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
                    }).catch(function(){});
                },

                fileSelectable: function (row, index) {
                    return row.type !== 'dir';
                },

                // 列表祋图下的表格选择
                tableSelectionChange: function (val) {
                    this.selectedFiles = val;

                    for (let file of this.files) {
                        if (val.indexOf(file) !== -1) {
                            file.selected = true;
                        } else {
                            file.selected = false;
                        }
                    }

                    this.callback(this.selectedFiles);
                },


                // 缩略图祋图下选择文件
                selectFile: function (file) {
                    file.selected = !file.selected;

                    let selectedFiles = [];
                    for (let file of this.files) {
                        if (file.selected) {
                            selectedFiles.push(file);
                        }
                    }

                    this.selectedFiles = selectedFiles;
                    this.callback(selectedFiles);
                },

                // 回调
                callback: function (files) {
                    <?php echo $this->callback; ?>
                }

            },
            created: function () {
                this.loadData();
            }
        });
    </script>

</be-body>