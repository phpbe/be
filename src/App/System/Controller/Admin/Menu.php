<?php

namespace Be\App\System\Controller\Admin;

use Be\AdminPlugin\Table\Item\TableItemToggleIcon;
use Be\App\ControllerException;
use Be\Be;
use Be\Db\Tuple;

/**
 * @BeMenuGroup("控制台")
 * @BePermissionGroup("控制台")
 */
class Menu extends Auth
{

    /**
     * @BeMenu("菜单导航", icon="el-icon-position", ordering="2.4")
     * @BePermission("菜单导航", ordering="2.4")
     */
    public function menus()
    {
        Be::getAdminPlugin('Curd')->setting([
            'label' => '菜单导航',
            'table' => 'system_menu',
            'grid' => [
                'title' => '菜单导航',

                'filter' => [
                ],

                'titleRightToolbar' => [
                    'items' => [
                        [
                            'label' => '新增菜单',
                            'task' => 'create',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                            'ui' => [
                                'icon' => 'el-icon-plus',
                                'type' => 'primary',
                            ]
                        ],
                    ],
                ],

                'table' => [

                    // 未指定时取表的所有字段
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '调用类名',
                            'align' => 'left',
                            'width' => '120',
                        ],
                        [
                            'name' => 'label',
                            'label' => '菜单名称',
                            'align' => 'left',
                        ],
                        [
                            'name' => 'items',
                            'label' => '菜单项',
                            'value' => function ($row) {
                                return Be::getService('App.System.Admin.Menu')->getSummary($row['name']);
                            },
                        ],
                        [
                            'name' => 'is_system',
                            'label' => '系统菜单',
                            'width' => '120',
                            'driver' => TableItemToggleIcon::class,
                        ],
                    ],

                    'operation' => [
                        'label' => '操作',
                        'width' => '240',
                        'items' => [
                            [
                                'label' => '管理菜单项',
                                'action' => 'goItems',
                                'target' => 'self',
                                'ui' => [
                                    'type' => 'success',
                                ],
                            ],
                            [
                                'label' => '编辑',
                                'task' => 'edit',
                                'target' => 'drawer',
                                'ui' => [
                                    'type' => 'primary',
                                ],
                            ],
                            [
                                'label' => '删除',
                                'task' => 'delete',
                                'confirm' => '确认要删除么？',
                                'target' => 'ajax',
                                'ui' => [
                                    'type' => 'danger',
                                    ':disabled' => 'scope.row.is_system === \'1\' ? true : false',
                                ],
                            ],
                        ]
                    ],
                ],
            ],

            'create' => [
                'theme' => 'Blank',
                'title' => '新建菜单',
                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '调用类名',
                            'required' => true,
                            'unique' => true,
                        ],
                        [
                            'name' => 'label',
                            'label' => '菜单名称',
                        ],
                    ]
                ],
                'events' => [
                    'before' => function (Tuple &$tuple) {
                        if (Be::newTable('system_menu')
                                ->where('name', $tuple->name)
                                ->count() > 0) {
                            throw new ControllerException('菜单调用类名' . $tuple->name . '已存在！');
                        }
                        $tuple->is_system = 0;
                        $tuple->create_time = date('Y-m-d H:i:s');
                        $tuple->update_time = date('Y-m-d H:i:s');
                    },
                ],
            ],

            'edit' => [
                'title' => '编辑菜单',
                'theme' => 'Blank',
                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '调用类名',
                            'required' => true,
                            'unique' => true,
                            'ui' => function ($row) {
                                return [
                                    ':disabled' => $row['is_system'] === 1 ? 'true' : 'false',
                                ];
                            },
                        ],
                        [
                            'name' => 'label',
                            'label' => '菜单名称',
                        ],
                    ]
                ],
                'events' => [
                    'before' => function (Tuple &$tuple) {
                        $tuple->update_time = date('Y-m-d H:i:s');
                    },
                ]
            ],

        ])->execute();
    }

    /**
     * 菜单项
     *
     * @BePermission("菜单项", ordering="2.41")
     */
    public function goItems()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        $postData = $request->post('data', '', '');
        if ($postData) {
            $postData = json_decode($postData, true);
            if (isset($postData['row']['id']) && $postData['row']['id']) {
                $response->redirect(beAdminUrl('System.Menu.items', ['id' => $postData['row']['id']]));
            }
        }
    }

    /**
     * 菜单项
     *
     * @BePermission("菜单项", ordering="2.41")
     */
    public function items()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $menuId = $request->get('id');
        $service = Be::getService('App.System.Admin.Menu');
        $menu = $service->getMenuById($menuId);

        if ($request->isAjax()) {
            try {
                $service->saveItems($menuId, $request->json('formData'));
                $response->set('success', true);
                $response->set('message', '保存成功！');
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }
        } else {
            $flatTree = $service->getFlatTree($menu->name);
            $response->set('flatTree', $flatTree);

            $menuPickers = $service->getMenuPickers();
            $response->set('menuPickers', $menuPickers);

            $response->set('title', $menu->label . ' - 菜单项管理');
            $response->set('menu', $menu);

            $response->display('App.System.Admin.Menu.items');
        }
    }

    /**
     * 菜单项 - 设置网址
     *
     * @BePermission("菜单项", ordering="2.41")
     */
    public function picker()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $pickerRoute = $request->get('pickerRoute');
            $service = Be::getService('App.System.Admin.Menu');
            $menuPicker = $service->getMenuPicker($pickerRoute);
            Be::getAdminPlugin('MenuPicker')
                ->setting($menuPicker)
                ->execute();
        } catch (\Throwable $t) {
            $response->error($t->getMessage());
        }
    }

    /**
     * 菜单项 - 设置网址
     *
     * @BePermission("菜单项", ordering="2.41")
     */
    public function setUrl()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $response->display('App.System.Admin.Menu.setUrl', 'Blank');
    }



}
