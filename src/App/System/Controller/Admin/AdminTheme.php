<?php
namespace Be\App\System\Controller\Admin;

use Be\AdminPlugin\Form\Item\FormItemDatePickerRange;
use Be\AdminPlugin\Table\Item\TableItemCustom;
use Be\AdminPlugin\Table\Item\TableItemIcon;
use Be\AdminPlugin\Table\Item\TableItemImage;
use Be\AdminPlugin\Table\Item\TableItemSwitch;
use Be\AdminPlugin\Table\Item\TableItemTag;
use Be\Be;
use Be\Db\Tuple;

/**
 * @BeMenuGroup("管理")
 * @BePermissionGroup("管理")
 */
class AdminTheme
{

    /**
     * @BeMenu("后台主题", icon="el-icon-view", ordering="2.2")
     * @BePermission("后台主题列表", ordering="2.2")
     */
    public function themes()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        if ($request->isAjax()) {
            $postData = $request->json();
            $service = Be::getService('App.System.Admin.AdminTheme');
            $themes = $service->getAvailableThemes();
            $page = $postData['page'];
            $pageSize = $postData['pageSize'];
            $gridData = array_slice($themes, ($page - 1) * $pageSize, $pageSize);
            $response->set('success', true);
            $response->set('data', [
                'total' => count($themes),
                'gridData' => $gridData,
            ]);
            $response->json();
        } else {
            Be::getAdminPlugin('Grid')->setting([
                'title' => '后台主题列表',
                'pageSize' => 10,
                'toolbar' => [
                    'items' => [
                        [
                            'label' => '发现',
                            'action' => 'discover',
                            'target' => 'ajax',
                            'ui' => [
                                'type' => 'primary',
                                'icon' => 'el-icon-search'
                            ]
                        ],
                    ],
                ],

                'layout' => 'toggle',

                'card' => [
                    'cols' => 2,
                    'image' => [
                        'name' => 'previewImageUrl',
                        //'maxHeight' => '200',
                        'position' => 'left'
                    ],
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '名称',
                        ],
                        [
                            'name' => 'label',
                            'label' => '中文名称',
                        ],
                        [
                            'name' => 'path',
                            'label' => '路径',
                        ],
                    ],

                    'operation' => [
                        'items' => [
                            [
                                'label' => '配置',
                                'action' => 'goSetting',
                                'target' => 'blank',
                                'ui' => [
                                    'type' => 'success'
                                ]
                            ],
                        ]
                    ],
                ],

                'table' => [
                    'items' => [
                        [
                            'name' => 'previewImageUrl',
                            'label' => '缩略图',
                            'driver' => TableItemImage::class,
                            'width' => '160',
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'width' => '160',
                        ],
                        [
                            'name' => 'label',
                            'label' => '中文名称',
                            'width' => '160',
                        ],
                        [
                            'name' => 'path',
                            'label' => '路径',
                            'align' => 'left',
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用/禁用',
                            'driver' => TableItemSwitch::class,
                            'target' => 'ajax',
                            'action' => 'toggleEnable',
                            'width' => '90',
                        ],
                        [
                            'name' => 'is_default',
                            'label' => '当前主题',
                            'driver' => TableItemSwitch::class,
                            'target' => 'ajax',
                            'task' => 'toggleDefault',
                            'width' => '90',
                        ],
                    ],
                    'operation' => [
                        'label' => '操作',
                        'width' => '120',
                        'items' => [
                            [
                                'label' => '配置',
                                'action' => 'goSetting',
                                'target' => 'blank',
                                'ui' => [
                                    'type' => 'success'
                                ]
                            ],
                        ]
                    ],
                ],

            ])->execute();
        }
    }

    /**
     * 发现
     *
     * @BePermission("发现主题", ordering="2.22")
     */
    public function discover()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        try {
            $serviceTheme = Be::getService('App.System.Admin.AdminTheme');
            $n = $serviceTheme->discover();
            $response->success('发现 ' . $n . ' 个新主题！');
        } catch (\Throwable $t) {
            $response->error($t->getMessage());
        }
    }

    /**
     * 启用/禁用主题
     *
     * @BePermission("启用/禁用主题", ordering="2.22")
     */
    public function toggleEnable()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $postData = $request->json();

        if (!isset($postData['row']['name'])) {
            $response->error('参数主题名缺失！');
        }

        $themeName = $postData['row']['name'];
        $isEnable = $postData['row']['is_enable'];

        try {
            $serviceTheme = Be::getService('App.System.Admin.AdminTheme');
            $serviceTheme->toggleEnable($themeName, $isEnable);

            beAdminOpLog(($isEnable ? '启用' : '禁用' ) . '主题：' . $themeName);
            $response->success(($isEnable ? '启用' : '禁用' ) . '主题成功！');
        } catch (\Throwable $t) {
            $response->error($t->getMessage());
        }
    }


    /**
     * 设置默认主题
     *
     * @BePermission("设置默认主题", ordering="2.22")
     */
    public function toggleDefault()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $postData = $request->json();

        if (!isset($postData['row']['name'])) {
            $response->error('参数主题名缺失！');
        }

        $themeName = $postData['row']['name'];

        try {
            $serviceTheme = Be::getService('App.System.Admin.AdminTheme');
            $serviceTheme->toggleDefault($themeName);

            beAdminOpLog('设置默认主题：' . $themeName);
            $response->success('设置默认主题成功！');
        } catch (\Throwable $t) {
            $response->error($t->getMessage());
        }
    }


}

