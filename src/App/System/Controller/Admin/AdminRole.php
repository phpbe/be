<?php

namespace Be\App\System\Controller\Admin;

use Be\AdminPlugin\Detail\Item\DetailItemSwitch;
use Be\AdminPlugin\Detail\Item\DetailItemTree;
use Be\AdminPlugin\Form\Item\FormItemRadioGroupButton;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Form\Item\FormItemSwitch;
use Be\AdminPlugin\Form\Item\FormItemTree;
use Be\AdminPlugin\Table\Item\TableItemLink;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\AdminPlugin\Table\Item\TableItemSwitch;
use Be\AdminPlugin\Toolbar\Item\ToolbarItemButtonDropDown;
use Be\AdminPlugin\Toolbar\Item\ToolbarItemDropDown;
use Be\Be;
use Be\Db\Tuple;
use Be\AdminPlugin\AdminPluginException;

/**
 * Class Role
 * @package App\System\Controller
 * @BeMenuGroup("管理员")
 * @BePermissionGroup("管理员")
 */
class AdminRole extends Auth
{

    /**
     * @BeMenu("角色管理", icon="el-icon-fa fa-user-secret", ordering="1.2")
     * @BePermission("角色管理", ordering="1.2")
     */
    public function roles()
    {
        Be::getAdminPlugin('Curd')->setting([

            'label' => '角色管理',
            'table' => 'system_admin_role',

            'grid' => [
                'title' => '角色管理',

                'filter' => [
                    ['is_delete', '=', '0'],
                ],

                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '名称',
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用状态',
                            'driver' => FormItemSelect::class,
                            'keyValues' => [
                                '1' => '启用',
                                '0' => '禁用',
                            ],
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

                'titleRightToolbar' => [
                    'items' => [
                        [
                            'label' => '新建角色',
                            'task' => 'create',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                            'ui' => [
                                'icon' => 'el-icon-plus',
                                'type' => 'primary',
                            ]
                        ],
                    ]
                ],

                'tableToolbar' => [
                    'items' => [
                        [
                            'label' => '批量启用',
                            'task' => 'fieldEdit',
                            'postData' => [
                                'field' => 'is_enable',
                                'value' => '1',
                            ],
                            'target' => 'ajax',
                            'ui' => [
                                'icon' => 'el-icon-check',
                                'type' => 'success',
                            ]
                        ],
                        [
                            'label' => '批量禁用',
                            'task' => 'fieldEdit',
                            'postData' => [
                                'field' => 'is_enable',
                                'value' => '0',
                            ],
                            'target' => 'ajax',
                            'confirm' => '确认要禁用么？',
                            'ui' => [
                                'icon' => 'el-icon-close',
                                'type' => 'warning',
                            ]
                        ],
                        [
                            'label' => '批量删除',
                            'task' => 'fieldEdit',
                            'target' => 'ajax',
                            'confirm' => '确认要删除么？',
                            'postData' => [
                                'field' => 'is_delete',
                                'value' => '1',
                            ],
                            'ui' => [
                                'icon' => 'el-icon-delete',
                                'type' => 'danger'
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
                            'name' => 'name',
                            'label' => '名称',
                            'align' => 'left',
                            'driver' => TableItemLink::class,
                            'task' => 'detail',
                            'target' => 'drawer',
                        ],
                        [
                            'name' => 'permission',
                            'label' => '权限',
                            'keyValues' => [
                                '0' => '无权限',
                                '1' => '所有权限',
                                '-1' => '自定义',
                            ],
                        ],
                        [
                            'name' => 'remark',
                            'label' => '备注',
                            'align' => 'left',
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
                                'label' => '编辑',
                                'task' => 'edit',
                                'target' => 'drawer',
                            ],
                            [
                                'label' => '删除',
                                'task' => 'fieldEdit',
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
                            'name' => 'name',
                            'label' => '名称',
                        ],
                        [
                            'name' => 'permission',
                            'label' => '权限',
                            'keyValues' => [
                                '0' => '无权限',
                                '1' => '所有权限',
                                '-1' => '自定义',
                            ],
                        ],
                        [
                            'name' => 'permission_keys',
                            'label' => '自定义权限',
                            'driver' => DetailItemTree::class,
                            'ui' => [
                                'form-item' => [
                                    'v-show' => 'formData.permission === \'-1\'',
                                ]
                            ],
                            'value' => function ($row) {
                                return explode(',', $row['permission_keys']);
                            },
                            'treeData' => function () {
                                return Be::getService('App.System.Admin.AdminPermission')->getPermissionTree();
                            },
                        ],
                        [
                            'name' => 'remark',
                            'label' => '备注',
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
                    ]
                ],
            ],

            'create' => [
                'title' => '新建角色',
                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'required' => true,
                        ],

                        [
                            'name' => 'permission',
                            'label' => '权限',
                            'driver' => FormItemRadioGroupButton::class,
                            'keyValues' => [
                                '0' => '无权限',
                                '1' => '所有权限',
                                '-1' => '自定义',
                            ],
                            'value' => '-1',
                        ],
                        [
                            'name' => 'permission_keys',
                            'label' => '自定义权限',
                            'driver' => FormItemTree::class,
                            'ui' => [
                                'form-item' => [
                                    'v-show' => 'formData.permission === \'-1\'',
                                ]
                            ],
                            'treeData' => function () {
                                return Be::getService('App.System.Admin.AdminPermission')->getPermissionTree();
                            },
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用/禁用',
                            'value' => 1,
                            'driver' => FormItemSwitch::class,
                        ],
                        [
                            'name' => 'remark',
                            'label' => '备注',
                        ],
                    ]
                ],
                'events' => [
                    'before' => function (Tuple &$tuple) {
                        if ($tuple->permission === '-1') {
                            if (is_array($tuple->permission_keys)) {
                                $permissionKeys = [];
                                foreach ($tuple->permission_keys as $permission) {
                                    $arr = explode('.', $permission);
                                    if (count($arr) === 3) {
                                        $permissionKeys[] = $permission;
                                    }
                                }

                                $tuple->permission_keys = implode(',', $permissionKeys);
                            } else {
                                $tuple->permission_keys = '';
                            }
                        } else {
                            $tuple->permission_keys = '';
                        }

                        $tuple->create_time = date('Y-m-d H:i:s');
                        $tuple->update_time = date('Y-m-d H:i:s');
                    },
                ],
            ],

            'edit' => [
                'title' => '编辑角色',
                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'required' => true,
                        ],
                        [
                            'name' => 'permission',
                            'label' => '权限',
                            'driver' => FormItemRadioGroupButton::class,
                            'keyValues' => [
                                '0' => '无权限',
                                '1' => '所有权限',
                                '-1' => '自定义',
                            ],
                        ],
                        [
                            'name' => 'permission_keys',
                            'label' => '自定义权限',
                            'driver' => FormItemTree::class,
                            'ui' => [
                                'form-item' => [
                                    'v-show' => 'formData.permission === \'-1\'',
                                ]
                            ],
                            'value' => function ($row) {
                                return explode(',', $row['permission_keys']);
                            },
                            'treeData' => function () {
                                return Be::getService('App.System.Admin.AdminPermission')->getPermissionTree();
                            },
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用/禁用',
                            'driver' => FormItemSwitch::class,
                        ],
                        [
                            'name' => 'remark',
                            'label' => '备注',
                        ],
                    ]
                ],
                'events' => [
                    'before' => function (Tuple &$tuple) {
                        if ($tuple->permission === -1) {
                            if (is_array($tuple->permission_keys)) {
                                $permissionKeys = [];
                                foreach ($tuple->permission_keys as $permission) {
                                    $arr = explode('.', $permission);
                                    if (count($arr) === 3) {
                                        $permissionKeys[] = $permission;
                                    }
                                }

                                $tuple->permission_keys = implode(',', $permissionKeys);
                            } else {
                                $tuple->permission_keys = '';
                            }
                        } else {
                            $tuple->permission_keys = '';
                        }

                        if ($tuple->hasChange()) {
                            $tuple->update_time = date('Y-m-d H:i:s');
                        }
                    }
                ]
            ],

            'fieldEdit' => [
                'events' => [
                    'before' => function (Tuple &$tuple) {
                        $request = Be::getRequest();
                        $postData = $request->json();
                        $field = $postData['postData']['field'];
                        if ($field === 'is_enable') {
                            if ($tuple->is_enable === 0) {
                                $n = Be::getTable('system_admin_user')
                                    ->where('admin_role_id', $tuple->id)
                                    ->where('is_delete', 0)
                                    ->count();
                                if ($n > 0) {
                                    throw new AdminPluginException('有' . $n . '个用户属于该角色（' . $tuple->name . '），不能禁用！');
                                }
                            }
                        } elseif ($field === 'is_delete') {
                            if ($tuple->is_delete === 1) {
                                $n = Be::getTable('system_admin_user')
                                    ->where('admin_role_id', $tuple->id)
                                    ->where('is_delete', 0)
                                    ->count();
                                if ($n > 0) {
                                    throw new AdminPluginException('有' . $n . '个用户属于该角色（' . $tuple->name . '），不能删除！');
                                }
                            }
                        }

                        if ($tuple->hasChange()) {
                            $tuple->update_time = date('Y-m-d H:i:s');
                        }
                    },
                ],
            ],

            'export' => [],

        ])->execute();

    }


}
