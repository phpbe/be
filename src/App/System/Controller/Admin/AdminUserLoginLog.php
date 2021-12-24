<?php

namespace Be\App\System\Controller\Admin;

use Be\AdminPlugin\Form\Item\FormItemDatePickerRange;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Table\Item\TableItemCustom;
use Be\Be;
use Be\Response;


/**
 * @BeMenuGroup("管理员")
 * @BePermissionGroup("管理员")
 */
class AdminUserLoginLog extends Auth
{

    /**
     * 系统日志
     *
     * @BeMenu("登录日志", icon="el-icon-fa fa-user-circle", ordering="1.3")
     * @BePermission("查看登录日志", ordering="1.3")
     */
    public function logs()
    {
        Be::getAdminPlugin('Curd')->setting([
            'label' => '管理员登录日志',
            'table' => 'system_admin_user_login_log',
            'grid' => [
                'title' => '管理员登录日志',
                'orderBy' => 'create_time',
                'orderByDir' => 'DESC',
                'form' => [
                    'items' => [
                        [
                            'name' => 'username',
                            'label' => '用户名',
                        ],
                        [
                            'name' => 'success',
                            'label' => '登录结果',
                            'driver' => FormItemSelect::class,
                            'keyValues' => [
                                '0' => '失败',
                                '1' => '成功',
                            ],
                        ],
                        [
                            'name' => 'description',
                            'label' => '描述',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'driver' => FormItemDatePickerRange::class,
                        ],
                    ],
                ],

                'toolbar' => [
                    'items' => [
                        [
                            'label' => '删除三个月前系统日志',
                            'url' => beAdminUrl('System.AdminUserLoginLog.deleteLogs'),
                            'confirm' => '确认要删除么？',
                            "target" => 'ajax',
                            'ui' => [
                                'icon' => 'el-icon-delete',
                                'type' => 'danger'
                            ],
                        ],
                        [
                            'label' => '导出',
                            'task' => 'export',
                            'target' => 'blank',
                            'ui' => [
                                'icon' => 'el-icon-fa fa-download',
                            ]
                        ],
                    ]
                ],

                'table' => [

                    'items' => [
                        [
                            'name' => 'id',
                            'label' => 'ID',
                            'width' => '80',
                            'sortable' => true,
                        ],
                        [
                            'name' => 'username',
                            'label' => '用户名',
                            'width' => '120',
                        ],
                        [
                            'name' => 'success',
                            'label' => '登录结果',
                            'driver' => TableItemCustom::class,
                            'width' => '150',
                            'keyValues' => [
                                '0' => '<span class="el-tag el-tag--danger el-tag--light">失败</span>',
                                '1' => '<span class="el-tag el-tag--success el-tag--light">成功</span>',
                            ],
                            'exportValue' => function($row){
                                return $row['success'] ? '成功' : '失败';
                            },
                        ],
                        [
                            'name' => 'description',
                            'label' => '描述',
                            'align' => 'left',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '180',
                            'sortable' => true,
                        ],
                        [
                            'name' => 'ip',
                            'label' => 'IP地址',
                            'width' => '160',
                        ],
                    ],
                ],
            ],
        ])->execute();
    }

    /**
     * 删除登录日志
     *
     * @BePermission("删除登录日志", ordering="1.31")
     */
    public function deleteLogs()
    {
        $response = Be::getResponse();

        $db = Be::getDb();
        $db->startTransaction();
        try {
            Be::newTable('system_admin_user_login_log')
                ->where('create_time', '<', date('Y-m-d H:i:s', time() - 90 * 86400))
                ->delete();
            beAdminOpLog('删除三个月前用户登录日志！');
            $db->commit();
            $response->success('删除三个月前用户登录日志成功！');
        } catch (\Exception $e) {
            $db->rollback();
            $response->error($e->getMessage());
        }
    }

}

