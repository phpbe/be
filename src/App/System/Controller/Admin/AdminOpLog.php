<?php

namespace Be\App\System\Controller\Admin;

use Be\AdminPlugin\Detail\Item\DetailItemCode;
use Be\AdminPlugin\Form\Item\FormItemDatePickerRange;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Toolbar\Item\ToolbarItemDropDown;
use Be\Be;

/**
 * @BeMenuGroup("日志", icon="el-icon-tickets", ordering="4")
 * @BePermissionGroup("日志", ordering="4")
 */
class AdminOpLog extends Auth
{

    /**
     * 操作日志
     *
     * @BeMenu("后台操作日志", icon="el-icon-fa fa-video-camera", ordering="4.1")
     * @BePermission("查看后台操作日志", ordering="4.1")
     */
    public function logs()
    {
        $adminUserKeyValues = Be::getDb()->getKeyValues('SELECT id, `name` FROM `system_admin_user` WHERE is_delete=0');
        $appKeyValues = Be::getService('App.System.Admin.App')->getAppNameLabelKeyValues();

        Be::getAdminPlugin('Curd')->setting([
            'label' => '后台操作日志',
            'table' => 'system_admin_op_log',
            'grid' => [
                'title' => '后台操作日志',
                'orderBy' => 'create_time',
                'orderByDir' => 'DESC',
                'form' => [
                    'items' => [
                        [
                            'name' => 'admin_user_id',
                            'label' => '用户',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $adminUserKeyValues,
                        ],
                        [
                            'name' => 'app',
                            'label' => '应用',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $appKeyValues,
                        ],
                        [
                            'name' => 'content',
                            'label' => '内容',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'driver' => FormItemDatePickerRange::class,
                        ],
                    ],
                ],

                'titleToolbar' => [
                    'items' => [
                        [
                            'label' => '导出',
                            'driver' => ToolbarItemDropDown::class,
                            'ui' => [
                                'icon' => 'el-icon-download',
                            ],
                            'menus' => [
                                [
                                    'label' => 'CSV',
                                    'task' => 'export',
                                    'postData' => [
                                        'driver' => 'csv',
                                    ],
                                    'target' => 'blank',
                                ],
                                [
                                    'label' => 'EXCEL',
                                    'task' => 'export',
                                    'postData' => [
                                        'driver' => 'excel',
                                    ],
                                    'target' => 'blank',
                                ],
                            ]
                        ],
                    ]
                ],

                'toolbar' => [
                    'items' => [
                        [
                            'label' => '删除三个月前系统日志',
                            'url' => beAdminUrl('System.OpLog.deleteLogs'),
                            'confirm' => '确认要删除么？',
                            "target" => 'ajax',
                            'ui' => [
                                'icon' => 'el-icon-delete',
                                'type' => 'danger'
                            ],
                        ],
                    ]
                ],

                'table' => [
                    'items' => [
                        [
                            'name' => 'admin_user_id',
                            'label' => '用户',
                            'width' => '120',
                            'keyValues' => $adminUserKeyValues,
                        ],
                        [
                            'name' => 'app',
                            'label' => '应用',
                            'width' => '120',
                            'keyValues' => $appKeyValues,
                        ],
                        [
                            'name' => 'route',
                            'label' => '访问路径',
                            'value' => function($row) {
                                return $row['app'] . '.' .$row['controller'] . '.' .$row['action'];
                            },
                            'width' => '240'
                        ],
                        [
                            'name' => 'content',
                            'label' => '内容',
                            'align' => 'left',
                        ],
                        [
                            'name' => 'ip',
                            'label' => 'IP地址',
                            'width' => '160',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '180',
                            'sortable' => true,
                        ],
                    ],
                    'operation' => [
                        'label' => '操作',
                        'width' => '120',
                        'items' => [
                            [
                                'label' => '查看',
                                'task' => 'detail',
                                'ui' => [
                                    'icon' => 'el-icon-search',
                                ]
                            ],
                        ]
                    ],
                ],
            ],

            'detail' => [
                'form' => [
                    'items' => [
                        [
                            'name' => 'id',
                            'label' => 'ID',
                        ],
                        [
                            'name' => 'admin_user_id',
                            'label' => '用户',
                            'value' => function ($row) use ($adminUserKeyValues) {
                                if (isset($adminUserKeyValues[$row['admin_user_id']])) {
                                    return $adminUserKeyValues[$row['admin_user_id']];
                                }
                                return '';
                            },
                        ],
                        [
                            'name' => 'app',
                            'label' => '应用名',
                        ],
                        [
                            'name' => 'controller',
                            'label' => '控制器名',
                        ],
                        [
                            'name' => 'action',
                            'label' => '动作名',
                        ],
                        [
                            'name' => 'content',
                            'label' => '内容',
                        ],
                        [
                            'name' => 'details',
                            'label' => '明细',
                            'driver' => DetailItemCode::class,
                            'language' => 'json',
                            'value' => function ($row) {
                                return json_encode(json_decode($row['details']),JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                            },
                        ],
                        [
                            'name' => 'ip',
                            'label' => 'IP地址',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                        ],
                    ]
                ],
            ],
        ])->execute();
    }

    /**
     * 删除操作日志
     *
     * @BePermission("后台操作日志", ordering="4.11")
     */
    public function deleteLogs()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $db = Be::getDb();
        $db->startTransaction();
        try {
            Be::getTable('system_admin_op_log')
                ->where('create_time', '<', date('Y-m-d H:i:s', time() - 90 * 86400))
                ->delete();
            beAdminOpLog('删除三个月前操作日志！');
            $db->commit();
            $response->success('删除三个月前操作日志成功！');
        } catch (\Exception $e) {
            $db->rollback();
            $response->error($e->getMessage());
        }
    }

}