<?php

namespace Be\App\System\Controller\Admin;

use Be\App\ControllerException;
use Be\Config\ConfigHelper;
use Be\AdminPlugin\Detail\Item\DetailItemIcon;
use Be\AdminPlugin\Form\Item\FormItemAutoComplete;
use Be\AdminPlugin\Form\Item\FormItemCustom;
use Be\AdminPlugin\Form\Item\FormItemInputNumberInt;
use Be\Be;
use Be\Util\Random;


/**
 * 安装器
 */
class Installer
{

    private $steps = null;

    public function __construct()
    {
        $config = Be::getConfig('App.System.System');
        if (!$config->developer || !$config->installable) {
            throw new ControllerException('请先开启系统配置中的 "开发者模式" 和 "可安装及重装" 配置项！');
        }

        $this->steps = ['环境检测', '配置数据库', '安装应用', '初始化系统', '完成'];
    }

    /**
     * 安装首页
     */
    public function index()
    {
        $response = Be::getResponse();
        $response->redirect(beAdminUrl('System.Installer.detect'));
    }

    /**
     * 检测环境
     */
    public function detect()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if ($request->isPost()) {
            $response->redirect(beAdminUrl('System.Installer.configDb'));
        } else {
            $runtime = Be::getRuntime();
            $value = [];
            $value['isPhpVersionGtMatch'] = version_compare(PHP_VERSION, '7.1.0') >= 0 ? 1 : 0;
            $value['isPdoMysqlInstalled'] = extension_loaded('pdo_mysql') ? 1 : 0;
            $value['isRedisInstalled'] = extension_loaded('redis') ? 1 : 0;
            $value['isCacheDirWritable'] = is_writable($runtime->getCachePath()) ? 1 : 0;
            $value['isDataDirWritable'] = is_writable($runtime->getDataPath()) ? 1 : 0;
            $value['isUploadDirWritable'] = is_writable($runtime->getUploadPath()) ? 1 : 0;
            $isAllPassed = array_sum($value) == count($value);

            $response->set('steps', $this->steps);
            $response->set('step', 0);

            Be::getAdminPlugin('Detail')
                ->setting([
                    'title' => '系统数据库配置',
                    'theme' => 'Installer',
                    'form' => [
                        'ui' => [
                            'label-width' => '300px',
                        ],
                        'items' => [
                            [
                                'label' => 'PHP版本（7.1+）',
                                'driver' => DetailItemIcon::class,
                                'value' => $value['isPhpVersionGtMatch'] ? 'el-icon-check' : 'el-icon-close',
                                'ui' => [
                                    'style' => 'color:' . ($value['isPhpVersionGtMatch'] ? '#67C23A' : '#F56C6C')
                                ]
                            ],
                            [
                                'label' => 'PDO Mysql 扩展',
                                'driver' => DetailItemIcon::class,
                                'value' => $value['isPdoMysqlInstalled'] ? 'el-icon-check' : 'el-icon-close',
                                'ui' => [
                                    'style' => 'color:' . ($value['isPdoMysqlInstalled'] ? '#67C23A' : '#F56C6C')
                                ]
                            ],
                            [
                                'label' => 'Redis 扩展',
                                'driver' => DetailItemIcon::class,
                                'value' => $value['isRedisInstalled'] ? 'el-icon-check' : 'el-icon-close',
                                'ui' => [
                                    'style' => 'color:' . ($value['isRedisInstalled'] ? '#67C23A' : '#F56C6C')
                                ]
                            ],
                            [
                                'label' => 'cache 目录可写',
                                'driver' => DetailItemIcon::class,
                                'value' => $value['isCacheDirWritable'] ? 'el-icon-check' : 'el-icon-close',
                                'ui' => [
                                    'style' => 'color:' . ($value['isCacheDirWritable'] ? '#67C23A' : '#F56C6C')
                                ]
                            ],
                            [
                                'label' => 'data 目录可写',
                                'driver' => DetailItemIcon::class,
                                'value' => $value['isDataDirWritable'] ? 'el-icon-check' : 'el-icon-close',
                                'ui' => [
                                    'style' => 'color:' . ($value['isDataDirWritable'] ? '#67C23A' : '#F56C6C')
                                ]
                            ],
                            [
                                'label' => 'upload 目录可写',
                                'driver' => DetailItemIcon::class,
                                'value' => $value['isUploadDirWritable'] ? 'el-icon-check' : 'el-icon-close',
                                'ui' => [
                                    'style' => 'color:' . ($value['isDataDirWritable'] ? '#67C23A' : '#F56C6C')
                                ]
                            ],
                        ],
                        'actions' => [
                            [
                                'label' => '继续安装',
                                'target' => 'self',
                                'ui' => [
                                    'type' => $isAllPassed ? 'primary' : 'danger',
                                    ':disabled' => $isAllPassed ? 'false' : 'true',
                                ]
                            ]
                        ]

                    ],
                ])
                ->execute();
        }
    }

    /**
     * 数据库配置
     */
    public function configDb()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if ($request->isPost()) {
            try {
                $postData = $request->json();
                $formData = $postData['formData'];
                Be::getService('App.System.Admin.Installer')->testDb($formData);

                $configDb = Be::getConfig('App.System.Db');

                $configDbDefault = new \Be\Db\Config();
                foreach ($configDbDefault->master as $k => $v) {
                    if (isset($formData[$k])) {
                        $configDb->master[$k] = $formData[$k];
                    }
                }

                ConfigHelper::update('System.Db', $configDb);

                $response->set('success', true);
                $response->set('redirectUrl', beAdminUrl('System.Installer.installApp'));
                $response->json();

            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }
        } else {
            $response->set('steps', $this->steps);
            $response->set('step', 1);

            $configDb = Be::getConfig('App.System.Db');
            Be::getAdminPlugin('Form')
                ->setting([
                    'title' => '系统数据库配置',
                    'theme' => 'Installer',
                    'form' => [
                        'ui' => [
                            'label-width' => '200px',
                            'style' => 'width: 600px',
                        ],
                        'items' => [
                            [
                                'name' => 'host',
                                'label' => '主机名',
                                'required' => true,
                                'value' => $configDb->master['host'],
                            ],
                            [
                                'name' => 'port',
                                'label' => '端口号',
                                'driver' => FormItemInputNumberInt::class,
                                'required' => true,
                                'value' => $configDb->master['port'],
                                'ui' => [':min' => 1, ':max' => 65535],
                            ],
                            [
                                'name' => 'username',
                                'label' => '用户名',
                                'required' => true,
                                'value' => $configDb->master['username'],
                            ],
                            [
                                'name' => 'password',
                                'label' => '密码',
                                'required' => true,
                                'value' => $configDb->master['password'],
                            ],
                            [
                                'name' => 'testDb',
                                'driver' => FormItemCustom::class,
                                'html' => '<el-form-item><el-button type="success" @click="testDb" v-loading="testDbLoading" size="mini" plain>测试连接，并获取库名列表</el-button></el-form-item>'
                            ],
                            [
                                'name' => 'name',
                                'label' => '库名',
                                'driver' => FormItemAutoComplete::class,
                                'required' => true,
                            ],
                            [
                                'name' => 'pool',
                                'label' => '连接池大小（0：不启用）',
                                'driver' => FormItemInputNumberInt::class,
                                'required' => true,
                                'value' => $configDb->master['pool'],
                                'ui' => [':min' => 0, ':max' => 10000],
                            ],
                        ],
                        'actions' => [
                            [
                                'label' => '上一步',
                                'ui' => [
                                    '@click' => 'window.location.href=\''.beAdminUrl('System.Installer.detect').'\'',
                                ]
                            ],
                            [
                                'label' => '继续安装',
                                'ui' => [
                                    'type' => 'primary',
                                    '@click' => 'saveDb',
                                ]
                            ]
                        ]
                    ],
                    'vueData' => [
                        'testDbLoading' => false, // 测试数据库连接中
                    ],
                    'vueMethods' => [
                        'testDb' => 'function() {
                            var _this = this;
                            this.testDbLoading = true;
                            this.$http.post("'.beAdminUrl('System.Installer.testDb').'", {
                                    formData: _this.formData
                                }).then(function (response) {
                                    _this.testDbLoading = false;
                                    //console.log(response);
                                    if (response.status == 200) {
                                        var responseData = response.data;
                                        if (responseData.success) {
                                            var message;
                                            if (responseData.message) {
                                                message = responseData.message;
                                            } else {
                                                message = \'连接成功！\';
                                            }
                                            _this.$message.success(message);
                                            var suggestions = [];
                                            for(var x in responseData.data.databases) {
                                                suggestions.push({
                                                    "value" : responseData.data.databases[x]
                                                });
                                            }
                                            _this.formItems.name.suggestions = suggestions;
                                        } else {
                                            if (responseData.message) {
                                                _this.$message.error(responseData.message);
                                            }
                                        }
                                    }
                                }).catch(function (error) {
                                    _this.testDbLoading = false;
                                    _this.$message.error(error);
                                });
                        }',

                        'saveDb' => 'function () {
                            var _this = this;
                            this.$refs["formRef"].validate(function (valid) {
                                if (valid) {
                                    _this.loading = true;
                                    _this.$http.post("'.beAdminUrl('System.Installer.configDb').'", {
                                        formData: _this.formData
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
        
                                } else {
                                    return false;
                                }
                            });
                        }',
                    ],
                ])
                ->setValue(Be::getConfig('App.System.Db')->master)
                ->execute();
        }
    }

    /**
     * 测试数据库连接
     */
    public function testDb()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        try {
            $postData = $request->json();
            $databases = Be::getService('App.System.Admin.Installer')->testDb($postData['formData']);
            $response->set('success', true);
            $response->set('data', [
                'databases' => $databases,
            ]);
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * 安装应用
     */
    public function installApp()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        if ($request->isPost()) {
            try {
                $postData = $request->json();
                $formData = $postData['formData'];
                $service = Be::getService('App.System.Admin.Installer');
                if (isset($formData['appNames']) && is_array($formData['appNames']) && count($formData['appNames'])) {
                    foreach ($formData['appNames'] as $appName) {
                        $service->installApp($appName);
                    }
                }
                $response->set('success', true);
                $response->set('redirectUrl', beAdminUrl('System.Installer.setting'));
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }
        } else {
            $response->set('steps', $this->steps);
            $response->set('step', 2);

            $appProperties = [];
            $property = Be::getProperty('App.System');
            $appProperties[] = [
                'name' => $property->getName(),
                'icon' => $property->getIcon(),
                'label' => $property->getLabel(),
                'description' => $property->getDescription(),
            ];
            $appNames = Be::getService('App.System.Admin.Installer')->getAppNames();
            foreach ($appNames as $appName) {
                $property = Be::getProperty('App.' . $appName);
                $appProperties[] = [
                    'name' => $property->getName(),
                    'icon' => $property->getIcon(),
                    'label' => $property->getLabel(),
                    'description' => $property->getDescription(),
                ];
            }

            $response->set('appProperties', $appProperties);
            $response->display('App.System.Admin.Installer.installApp', 'Installer');
        }
    }

    /**
     * 配置系统
     */
    public function setting()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $tuple = Be::getTuple('system_admin_user');
        $tuple->load(1);

        if ($request->isPost()) {
            $postData = $request->json();
            $formData = $postData['formData'];

            $tuple->username = $formData['username'];
            $tuple->salt = Random::complex(32);
            $tuple->password = Be::getService('App.System.Admin.AdminUser')->encryptPassword($formData['password'], $tuple->salt);
            $tuple->name = $formData['name'];
            $tuple->email = $formData['email'];
            $tuple->update_time = date('Y-m-d H:i:s');
            $tuple->update();

            $response->set('success', true);
            $response->set('redirectUrl', beAdminUrl('System.Installer.complete'));
            $response->json();

        } else {
            $response->set('steps', $this->steps);
            $response->set('step', 3);

            Be::getAdminPlugin('Form')
                ->setting([
                    'title' => '后台账号',
                    'theme' => 'Installer',
                    'form' => [
                        'ui' => [
                            'label-width' => '200px',
                        ],
                        'items' => [
                            [
                                'name' => 'username',
                                'label' => '超级管理员账号',
                                'required' => true,
                            ],
                            [
                                'name' => 'password',
                                'label' => '密码',
                                'required' => true,
                                'value' => 'admin',
                            ],
                            [
                                'name' => 'name',
                                'label' => '名称',
                            ],
                            [
                                'name' => 'email',
                                'label' => '邮箱',
                            ],
                        ],
                        'actions' => [
                            [
                                'label' => '完成安装',
                                'ui' => [
                                    'type' => 'success',
                                    '@click' => 'setting',
                                ]
                            ]
                        ]
                    ],
                    'vueMethods' => [
                        'setting' => 'function () {
                            var _this = this;
                            this.$refs["formRef"].validate(function (valid) {
                                if (valid) {
                                    _this.loading = true;
                                    _this.$http.post("'.beAdminUrl('System.Installer.setting').'", {
                                        formData: _this.formData
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
        
                                } else {
                                    return false;
                                }
                            });
                        }',
                    ],
                ])
                ->setValue($tuple)
                ->execute();
        }
    }

    /**
     * 安装完成
     */
    public function complete()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $response->set('steps', $this->steps);
        $response->set('step', 4);
        $response->set('url', beAdminUrl());
        $response->display('App.System.Admin.Installer.complete', 'Installer');

        $config = Be::getConfig('App.System.System');
        $config->installable = false;
        ConfigHelper::update('System.System', $config);

        if (Be::getRuntime()->getMode() == 'Swoole') {
            Be::getRuntime()->reload();
        }
    }


}