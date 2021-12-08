<?php

namespace Be\App\System\Controller\Admin;

use Be\Db\Tuple;
use Be\Util\Random;
use Be\AdminPlugin\Table\Item\TableItemLink;
use Be\AdminPlugin\Toolbar\Item\ToolbarItemButtonDropDown;
use Be\AdminPlugin\Detail\Item\DetailItemAvatar;
use Be\AdminPlugin\Detail\Item\DetailItemSwitch;
use Be\AdminPlugin\Form\Item\FormItemAvatar;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Form\Item\FormItemSwitch;
use Be\AdminPlugin\Table\Item\TableItemAvatar;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\AdminPlugin\Table\Item\TableItemSwitch;
use Be\AdminPlugin\AdminPluginException;
use Be\Be;

/**
 * Class AdminUser
 * @package App\System\Controller
 *
 * @BeMenuGroup("管理员", icon="el-icon-user-solid", ordering="1")
 * @BePermissionGroup("管理员", ordering="1")
 */
class AdminUser extends Auth
{

    /**
     * 管理员管理
     *
     * @BeMenu("管理员管理", icon="el-icon-user-solid", ordering="1.1")
     * @BePermission("管理员管理", ordering="1.1")
     */
    public function adminUsers()
    {
        $configAdminUser = Be::getConfig('App.System.AdminUser');
        $adminRoleKeyValues = Be::getService('App.System.Admin.AdminRole')->getAdminRoleKeyValues();
        $genderKeyValues = [
            '-1' => '保密',
            '0' => '女',
            '1' => '男',
        ];

        Be::getAdminPlugin('Curd')->setting([

            'label' => '管理员管理',
            'table' => 'system_admin_user',

            'grid' => [
                'title' => '管理员管理',

                'filter' => [
                    ['is_delete', '=', '0'],
                ],

                'form' => [
                    'items' => [
                        [
                            'name' => 'admin_role_id',
                            'label' => '角色',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $adminRoleKeyValues,
                        ],
                        [
                            'name' => 'username',
                            'label' => '用户名',
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                        ],
                        [
                            'name' => 'email',
                            'label' => '邮箱',
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用状态',
                            'driver' => FormItemSelect::class,
                            'keyValues' => [
                                '1' => '启用',
                                '0' => '禁用',
                            ]
                        ],
                    ],
                ],


                'toolbar' => [

                    'items' => [
                        [
                            'label' => '新建用户',
                            'task' => 'create',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                            'ui' => [
                                'icon' => 'el-icon-fa fa-user-plus',
                                'type' => 'primary',
                            ]
                        ],
                        [
                            'label' => '启用',
                            'task' => 'fieldEdit',
                            'postData' => [
                                'field' => 'is_enable',
                                'value' => '1',
                            ],
                            'target' => 'ajax',
                            'ui' => [
                                'icon' => 'el-icon-fa fa-check',
                                'type' => 'success',
                            ]
                        ],
                        [
                            'label' => '禁用',
                            'task' => 'fieldEdit',
                            'postData' => [
                                'field' => 'is_enable',
                                'value' => '0',
                            ],
                            'target' => 'ajax',
                            'ui' => [
                                'icon' => 'el-icon-fa fa-lock',
                                'type' => 'warning',
                            ]
                        ],
                        [
                            'label' => '删除',
                            'task' => 'fieldEdit',
                            'target' => 'ajax',
                            'postData' => [
                                'field' => 'is_delete',
                                'value' => '1',
                            ],
                            'ui' => [
                                'icon' => 'el-icon-delete',
                                'type' => 'danger'
                            ]
                        ],
                        [
                            'label' => '导出',
                            'driver' => ToolbarItemButtonDropDown::class,
                            'ui' => [
                                'icon' => 'el-icon-fa fa-download',
                            ],
                            'menus' => [
                                [
                                    'label' => 'CSV',
                                    'task' => 'export',
                                    'postData' => [
                                        'driver' => 'csv',
                                    ],
                                    'target' => 'blank',
                                    'ui' => [
                                        'icon' => 'el-icon-fa fa-file-text-o',
                                    ],
                                ],
                                [
                                    'label' => 'EXCEL',
                                    'task' => 'export',
                                    'postData' => [
                                        'driver' => 'excel',
                                    ],
                                    'target' => 'blank',
                                    'ui' => [
                                        'icon' => 'el-icon-fa fa-file-excel-o',
                                    ],
                                ],
                            ]
                        ],
                    ]
                ],

                'table' => [

                    // 未指定时取表的所有字段
                    'items' => [
                        [
                            'driver' => TableItemSelection::class,
                            'width' => '50',
                        ],
                        [
                            'name' => 'id',
                            'label' => 'ID',
                            'width' => '60',
                            'sortable' => true,
                        ],
                        [
                            'name' => 'avatar',
                            'label' => '头像',
                            'driver' => TableItemAvatar::class,
                            'value' => function ($row) {
                                if ($row['avatar'] == '') {
                                    return Be::getProperty('App.System')->getUrl() . '/Template/Admin/AdminUser/images/avatar.png';
                                } else {
                                    return Be::getRequest()->getUploadUrl() . '/System/AdminUser/Avatar/' . $row['avatar'];
                                }
                            },
                            'ui' => [
                                ':size' => '32',
                            ],
                            'width' => '50',
                        ],
                        [
                            'name' => 'username',
                            'label' => '用户名',
                            'driver' => TableItemLink::class,
                            'task' => 'detail',
                            'target' => 'drawer',
                            'width' => '120',
                            'sortable' => true,
                        ],
                        [
                            'name' => 'admin_role_id',
                            'label' => '角色',
                            'keyValues' => $adminRoleKeyValues,
                        ],
                        [
                            'name' => 'email',
                            'label' => '邮箱',
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '150',
                            'sortable' => true,
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用/禁用',
                            'driver' => TableItemSwitch::class,
                            'target' => 'ajax',
                            'task' => 'fieldEdit',
                            'width' => '90',
                            'exportValue' => function ($row) {
                                return $row['is_enable'] ? '启用' : '禁用';
                            },
                        ],
                    ],
                    'exclude' => ['password', 'salt'],
                    'operation' => [
                        'label' => '操作',
                        'width' => '120',
                        'items' => [
                            [
                                'label' => '编辑',
                                'task' => 'edit',
                                'target' => 'drawer',
                                'ui' => [
                                    'type' => 'success'
                                ]
                            ],
                            [
                                'label' => '删除',
                                'task' => 'fieldEdit',
                                'confirm' => '确认要删除么？',
                                'target' => 'ajax',
                                'postData' => [
                                    'field' => 'is_delete',
                                    'value' => 1,
                                ],
                                'ui' => [
                                    'type' => 'danger'
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
                            'name' => 'avatar',
                            'label' => '头像',
                            'driver' => DetailItemAvatar::class,
                            'value' => function ($row) {
                                if ($row['avatar'] == '') {
                                    return Be::getProperty('App.System')->getUrl() . '/Template/Admin/AdminUser/images/avatar.png';
                                } else {
                                    return Be::getRequest()->getUploadUrl() . '/System/AdminUser/Avatar/' . $row['avatar'];
                                }
                            },
                        ],
                        [
                            'name' => 'username',
                            'label' => '用户名',
                        ],
                        [
                            'name' => 'admin_role_id',
                            'label' => '角色',
                            'keyValues' => $adminRoleKeyValues,
                        ],
                        [
                            'name' => 'email',
                            'label' => '邮箱',
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                        ],
                        [
                            'name' => 'gender',
                            'label' => '性别',
                            'value' => function ($row) {
                                switch ($row['gender']) {
                                    case '-1':
                                        return '保密';
                                    case '0':
                                        return '女';
                                    case '1':
                                        return '男';
                                }
                                return '';
                            },
                        ],
                        [
                            'name' => 'phone',
                            'label' => '电话',
                        ],
                        [
                            'name' => 'mobile',
                            'label' => '手机',
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用/禁用',
                            'driver' => DetailItemSwitch::class,
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                        ],
                        [
                            'name' => 'update_time',
                            'label' => '更新时间',
                        ],
                        [
                            'name' => 'last_login_time',
                            'label' => '上次登陆时间',
                        ],
                        [
                            'name' => 'last_login_ip',
                            'label' => '上次登录的IP',
                        ],
                        [
                            'name' => 'this_login_time',
                            'label' => '本次登陆时间',
                        ],
                        [
                            'name' => 'this_login_ip',
                            'label' => '本次登录的IP',
                        ],
                    ]
                ],
            ],

            'create' => [
                'title' => '新建用户',
                'form' => [
                    'items' => [
                        [
                            'name' => 'avatar',
                            'label' => '头像',
                            'driver' => FormItemAvatar::class,
                            'path' => '/System/AdminUser/Avatar/',
                            'maxWidth' => $configAdminUser->avatarWidth,
                            'maxHeight' => $configAdminUser->avatarHeight,
                            'defaultValue' => Be::getProperty('App.System')->getUrl() . '/Template/Admin/AdminUser/images/avatar.png',
                        ],
                        [
                            'name' => 'username',
                            'label' => '用户名',
                            'required' => true,
                            'unique' => true,
                        ],
                        [
                            'name' => 'password',
                            'label' => '密码',
                            'required' => true,
                        ],
                        [
                            'name' => 'admin_role_id',
                            'label' => '角色',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $adminRoleKeyValues,
                            'required' => true,
                        ],
                        [
                            'name' => 'email',
                            'label' => '邮箱',
                            'unique' => true,
                            'required' => true,
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'required' => true,
                        ],
                        [
                            'name' => 'gender',
                            'label' => '性别',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $genderKeyValues,
                            'required' => true,
                        ],
                        [
                            'name' => 'phone',
                            'label' => '电话',
                        ],
                        [
                            'name' => 'mobile',
                            'label' => '手机',
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用/禁用',
                            'value' => 1,
                            'driver' => FormItemSwitch::class,
                        ],
                    ]
                ],
                'events' => [
                    'before' => function (Tuple &$tuple) {
                        $tuple->salt = Random::complex(32);
                        $tuple->password = Be::getService('App.System.Admin.AdminUser')->encryptPassword($tuple->password, $tuple->salt);
                        $tuple->create_time = date('Y-m-d H:i:s');
                        $tuple->update_time = date('Y-m-d H:i:s');
                    },
                ],
            ],

            'edit' => [
                'title' => '编辑用户',
                'form' => [
                    'items' => [
                        [
                            'name' => 'avatar',
                            'label' => '头像',
                            'driver' => FormItemAvatar::class,
                            'path' => '/System/AdminUser/Avatar/',
                            'maxWidth' => $configAdminUser->avatarWidth,
                            'maxHeight' => $configAdminUser->avatarHeight,
                            'defaultValue' => Be::getProperty('App.System')->getUrl() . '/Template/Admin/AdminUser/images/avatar.png',
                        ],
                        [
                            'name' => 'username',
                            'label' => '用户名',
                            'disabled' => true,
                            'required' => true,
                        ],
                        [
                            'name' => 'password',
                            'label' => '密码',
                            'value' => '',
                        ],
                        [
                            'name' => 'admin_role_id',
                            'label' => '角色',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $adminRoleKeyValues,
                            'required' => true,
                        ],
                        [
                            'name' => 'email',
                            'label' => '邮箱',
                            'disabled' => true,
                            'required' => true,
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'required' => true,
                        ],
                        [
                            'name' => 'gender',
                            'label' => '性别',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $genderKeyValues,
                            'required' => true,
                        ],
                        [
                            'name' => 'phone',
                            'label' => '电话',
                        ],
                        [
                            'name' => 'mobile',
                            'label' => '手机',
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用/禁用',
                            'driver' => FormItemSwitch::class,
                        ],
                    ]
                ],
                'events' => [
                    'before' => function (Tuple &$tuple) {
                        if ($tuple->password != '') {
                            $tuple->salt = Random::complex(32);
                            $tuple->password = Be::getService('App.System.Admin.AdminUser')->encryptPassword($tuple->password, $tuple->salt);
                        } else {
                            unset($tuple->password);
                        }
                        $tuple->update_time = date('Y-m-d H:i:s');
                    }
                ]
            ],

            'fieldEdit' => [
                'events' => [
                    'before' => function ($tuple) {
                        $request = Be::getRequest();
                        $postData = $request->json();
                        $field = $postData['postData']['field'];
                        if ($field == 'is_enable') {
                            if ($tuple->is_enable == 0) {
                                if ($tuple->id == 1) {
                                    throw new AdminPluginException('默认用户不能禁用');
                                }

                                $my = Be::getAdminUser();
                                if ($tuple->id == $my->id) {
                                    throw new AdminPluginException('不能禁用自已的账号');
                                }
                            }
                        } elseif ($field == 'is_delete') {
                            if ($tuple->is_delete == 1) {
                                if ($tuple->id == 1) {
                                    throw new AdminPluginException('默认用户不能删除');
                                }

                                $my = Be::getAdminUser();
                                if ($tuple->id == $my->id) {
                                    throw new AdminPluginException('不能删除自已');
                                }
                            }
                        }

                        $tuple->update_time = date('Y-m-d H:i:s');
                    },
                ],
            ],

            'export' => [],

        ])->execute();
    }


}
