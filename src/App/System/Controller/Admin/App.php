<?php
namespace Be\App\System\Controller\Admin;

use Be\AdminPlugin\Table\Item\TableItemIcon;
use Be\Be;

/**
 * @BeMenuGroup("控制台", icon="el-icon-monitor", ordering="2")
 * @BePermissionGroup("控制台", ordering="2")
 */
class App extends Auth
{

    /**
     * @BeMenu("应用", icon="el-icon-files", ordering="2.1")
     * @BePermission("应用列表", ordering="2.1")
     */
    public function apps()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        if ($request->isAjax()) {
            try {
                $postData = $request->json();
                $apps = Be::getService('App.System.Admin.App')->getApps();
                $page = $postData['page'];
                $pageSize = $postData['pageSize'];
                $gridData = array_slice($apps, ($page - 1) * $pageSize, $pageSize);
                $response->set('success', true);
                $response->set('data', [
                    'total' => count($apps),
                    'gridData' => $gridData,
                ]);
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
                Be::getLog()->error($t);
            }

        } else {

            Be::getAdminPlugin('Grid')
                ->setting([
                    'title' => '应用管理',

                    'titleRightToolbar' => [
                        'items' => [
                            [
                                'label' => '安装',
                                'action' => 'install',
                                'target' => 'drawer',
                                'ui' => [
                                    'icon' => 'el-icon-plus',
                                    'type' => 'primary',
                                ]
                            ],
                        ]
                    ],

                    'table' => [
                        'items' => [
                            [
                                'name' => 'icon',
                                'label' => '图标',
                                'driver' => TableItemIcon::class,
                                'width' => '90',
                            ],
                            [
                                'name' => 'name',
                                'label' => '应用名',
                                'width' => '120',
                                'align' => 'left',
                            ],
                            [
                                'name' => 'label',
                                'label' => '应用中文名',
                            ],
                        ],
                        'operation' => [
                            'label' => '操作',
                            'width' => '120',
                            'items' => [
                                [
                                    'label' => '卸载',
                                    'action' => 'uninstall',
                                    'confirm' => '应用数据将被清除，且不可恢复，确认要卸载么？',
                                    'target' => 'ajax',
                                    'ui' => [
                                        'type' => 'danger'
                                    ]
                                ],
                            ]
                        ],
                    ],

                ])
                ->display();

            $response->createHistory();
        }
    }

    /**
     * 安装新应用
     *
     * @BePermission("安装应用", ordering="2.11")
     */
    public function install()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        if ($request->isAjax()) {
            $postData = $request->json();

            if (!isset($postData['formData']['appName'])) {
                $response->error('参数应用名缺失！');
            }

            $appName = $postData['formData']['appName'];

            try {
                $serviceApp = Be::getService('App.System.Admin.App');
                $serviceApp->install($appName);

                beAdminOpLog('安装新应用：' . $appName);
                $response->success('应用安装成功！');

                if (Be::getRuntime()->isSwooleMode()) {
                    Be::getRuntime()->reload();
                }
            } catch (\Throwable $t) {
                $response->error($t->getMessage());
            }

        } else {
            Be::getAdminPlugin('Form')
                ->setting([
                    'title' => '安装新应用',
                    'form' => [
                        'items' => [
                            [
                                'name' => 'appName',
                                'label' => '应用名',
                                'required' => true,
                            ],
                        ],
                        'actions' => [
                            'submit' => '安装',
                        ]
                    ],
                ])
                ->execute();
        }
    }

    /**
     * 卸载应用
     *
     * @BePermission("卸载应用", ordering="2.12")
     */
    public function uninstall()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $postData = $request->json();

        if (!isset($postData['row']['name'])) {
            $response->error('参数应用名缺失！');
        }

        $appName = $postData['row']['name'];

        try {
            $serviceApp = Be::getService('App.System.Admin.App');
            $serviceApp->uninstall($appName);

            beAdminOpLog('卸载应用：' . $appName);
            $response->success('应用卸载成功！');

            if (Be::getRuntime()->isSwooleMode()) {
                Be::getRuntime()->reload();
            }
        } catch (\Throwable $t) {
            $response->error($t->getMessage());
        }
    }


}

