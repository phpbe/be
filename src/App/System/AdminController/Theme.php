<?php

namespace Be\App\System\AdminController;

use Be\AdminPlugin\Form\Item\FormItemDatePickerRange;
use Be\Be;
use Be\Db\Tuple;

/**
 * @BeMenuGroup("管理")
 * @BePermissionGroup("管理")
 */
class Theme
{

    /**
     * @BeMenu("主题", icon="el-icon-view", ordering="2.2")
     * @BePermission("主题列表", ordering="2.2")
     */
    public function themes()
    {
        Be::getAdminPlugin('Curd')->setting([

            'label' => '主题管理',
            'table' => 'system_theme',

            'lists' => [
                'title' => '已安装的主题列表',
                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '主题名',
                        ],
                        [
                            'name' => 'label',
                            'label' => '主题中文名',
                        ],
                        [
                            'name' => 'install_time',
                            'label' => '安装时间',
                            'driver' => FormItemDatePickerRange::class,
                        ],
                    ],
                ],


                'toolbar' => [

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
                            'name' => 'id',
                            'label' => 'ID',
                            'width' => '90',
                        ],
                        [
                            'name' => 'name',
                            'label' => '主题名',
                            'width' => '120',
                            'align' => 'left',
                        ],
                        [
                            'name' => 'label',
                            'label' => '主题中文名',
                        ],
                        [
                            'name' => 'install_time',
                            'label' => '安装时间',
                            'width' => '150',
                        ],
                        [
                            'name' => 'update_time',
                            'label' => '更新时间',
                            'width' => '150',
                        ],
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
                            'ui' => [
                                'type' => 'primary'
                            ]
                        ],
                        [
                            'label' => '配置',
                            'action' => 'goSetting',
                            'target' => 'blank',
                            'ui' => [
                                'type' => 'success'
                            ]
                        ],
                        [
                            'label' => '卸载',
                            'action' => 'uninstall',
                            'confirm' => '确认要卸载么？',
                            'target' => 'ajax',
                            'ui' => [
                                'type' => 'danger'
                            ]
                        ],
                    ]
                ],
            ],

            'edit' => [
                'title' => '编辑主题',
                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '主题名',
                            'disabled' => true,
                        ],
                        [
                            'name' => 'label',
                            'label' => '主题中文名',
                        ],
                    ]
                ],
                'events' => [
                    'before' => function (Tuple &$tuple) {
                        $tuple->update_time = date('Y-m-d H:i:s');
                    }
                ]
            ],
        ])->execute();
    }

    /**
     * 安装新主题
     *
     * @BePermission("安装主题", ordering="2.21")
     */
    public function install()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if ($request->isAjax()) {
            $postData = $request->json();

            if (!isset($postData['formData']['themeName'])) {
                $response->error('参数主题名缺失！');
            }

            $themeName = $postData['formData']['themeName'];

            try {
                $serviceApp = Be::getAdminService('System.Theme');
                $serviceApp->install($themeName);

                beAdminOpLog('安装新主题：' . $themeName);
                $response->success('主题安装成功！');
            } catch (\Throwable $t) {
                $response->error($t->getMessage());
            }
        } else {
            Be::getAdminPlugin('Form')
                ->setting([
                    'title' => '安装新主题',
                    'form' => [
                        'items' => [
                            [
                                'name' => 'themeName',
                                'label' => '主题名',
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
     * 卸载主题
     *
     * @BePermission("卸载主题", ordering="2.23")
     */
    public function uninstall()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $postData = $request->json();

        if (!isset($postData['row']['name'])) {
            $response->error('参数主题名缺失！');
        }

        $themeName = $postData['row']['name'];

        try {
            $serviceTheme = Be::getAdminService('System.Theme');
            $serviceTheme->uninstall($themeName);

            beAdminOpLog('卸载主题：' . $themeName);
            $response->success('主题卸载成功！');
        } catch (\Throwable $t) {
            $response->error($t->getMessage());
        }
    }

    /**
     * 配置主题
     *
     * @BePermission("配置主题", ordering="2.22")
     */
    public function goSetting()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        $postData = $request->post('data', '', '');
        $postData = json_decode($postData, true);
        $url = beAdminUrl('System.Theme.setting', ['themeName' => $postData['row']['name']]);
        $response->redirect($url);
    }

    /**
     * 配置主题
     *
     * @BePermission("配置主题", ordering="2.22")
     */
    public function setting()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', 'Home');

        $sectionType = $request->get('sectionType', '');
        $sectionKey = $request->get('sectionKey', '');
        $sectionName = $request->get('sectionName', '');

        $itemKey = $request->get('itemKey', '');
        $itemName = $request->get('itemName', '');

        $service = Be::getAdminService('System.Theme');
        $theme = $service->getTheme($themeName);

        $response->set('themeName', $themeName);
        $response->set('theme', $theme);

        $page = $service->getThemePage($themeName, $pageName);
        $response->set('pageName', $pageName);
        $response->set('page', $page);
        //print_r($page);

        if ($pageName != 'Home') {
            $pageHome = $service->getThemePage($themeName, 'Home');
            $response->set('pageHome', $pageHome);
        }

        $response->set('sectionType', $sectionType);
        $response->set('sectionKey', $sectionKey);
        $response->set('sectionName', $sectionName);

        $response->set('itemKey', $itemKey);
        $response->set('itemName', $itemName);

        $response->display();
    }

    public function enableSectionType()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $sectionType = $request->get('sectionType', '');

        $service = Be::getAdminService('System.Theme');
        $service->enableSectionType($themeName, $pageName, $sectionType);

        $url = beAdminUrl('System.Theme.setting', ['themeName' => $themeName, 'pageName' => $pageName]);
        $response->redirect($url);
    }

    public function disableSectionType()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $sectionType = $request->get('sectionType', '');

        $service = Be::getAdminService('System.Theme');
        $service->disableSectionType($themeName, $pageName, $sectionType);

        $url = beAdminUrl('System.Theme.setting', ['themeName' => $themeName, 'pageName' => $pageName]);
        $response->redirect($url);
    }

    /**
     * 新增组件
     */
    public function addSection()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $sectionType = $request->json('sectionType', '');
        $sectionName = $request->json('sectionName', '');

        $service = Be::getAdminService('System.Theme');
        $service->addSection($themeName, $pageName, $sectionType, $sectionName);

        $page = $service->getThemePage($themeName, $pageName);
        $response->set('page', $page);

        $response->success('保存成功！');
    }

    /**
     * 删除组件
     */
    public function deleteSection()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $sectionType = $request->json('sectionType', '');
        $sectionKey = $request->json('sectionKey', '');

        $service = Be::getAdminService('System.Theme');
        $service->deleteSection($themeName, $pageName, $sectionType, $sectionKey);

        $page = $service->getThemePage($themeName, $pageName);
        $response->set('page', $page);

        $response->success('保存成功！');
    }

    /**
     * 组件排序
     */
    public function sortSection()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $sectionType = $request->json('sectionType', '');

        $oldIndex = $request->json('oldIndex', '');
        $newIndex = $request->json('newIndex', '');

        $service = Be::getAdminService('System.Theme');
        $service->sortSection($themeName, $pageName, $sectionType, $oldIndex, $newIndex);

        $page = $service->getThemePage($themeName, $pageName);
        $response->set('page', $page);

        $response->success('保存成功！');
    }

    /**
     * 新增组件子项
     */
    public function addSectionItem()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $sectionType = $request->get('sectionType', '');
        $sectionKey = $request->get('sectionKey', '');
        $sectionName = $request->get('sectionName', '');

        $itemKey = $request->get('itemKey', '');
        $itemName = $request->get('itemName', '');

        $service = Be::getAdminService('System.Theme');
        $service->addSectionItem($themeName, $pageName, $sectionType, $sectionKey, $itemName);

        $page = $service->getThemePage($themeName, $pageName);
        $response->set('page', $page);

        $response->success('保存成功！');
    }

    /**
     * 删除子组件
     */
    public function deleteSectionItem()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $sectionType = $request->json('sectionType', '');
        $sectionKey = $request->json('sectionKey', '');

        $itemKey = $request->json('itemKey', '');

        $service = Be::getAdminService('System.Theme');
        $service->deleteSectionItem($themeName, $pageName, $sectionType, $sectionKey, $itemKey);

        $page = $service->getThemePage($themeName, $pageName);
        $response->set('page', $page);

        $response->success('保存成功！');
    }

    /**
     * 编辑组件子项
     */
    public function editSectionItem()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', 'Home');

        $sectionType = $request->get('sectionType', '');
        $sectionKey = $request->get('sectionKey', '');

        $itemKey = $request->get('itemKey', '');

        $response->set('themeName', $themeName);
        $response->set('pageName', $pageName);
        $response->set('sectionType', $sectionType);
        $response->set('sectionKey', $sectionKey);
        $response->set('itemKey', $itemKey);

        $service = Be::getAdminService('System.Theme');
        if ($sectionType && $sectionKey !== '') {
            if ($itemKey !== '') {
                $drivers = $service->getThemeSectionItemDrivers($themeName, $pageName, $sectionType, $sectionKey, $itemKey);
                $response->set('drivers', $drivers);
            } else {
                $drivers = $service->getThemeSectionDrivers($themeName, $pageName, $sectionType, $sectionKey);
                $response->set('drivers', $drivers);
            }
        } else {
            $drivers = $service->getThemeDrivers($themeName);
            $response->set('drivers', $drivers);
        }

        $response->display(null, 'Blank');
    }

    /**
     * 编辑组件子项保存
     */
    public function saveSectionItem()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $postData = $request->json();
        $formData = $postData['formData'];

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $sectionType = $request->get('sectionType', '');
        $sectionKey = $request->get('sectionKey', '');

        $itemKey = $request->get('itemKey', '');

        $service = Be::getAdminService('System.Theme');
        $service->saveSectionItem($themeName, $pageName, $sectionType, $sectionKey, $itemKey, $formData);

        $response->success('保存成功！');
    }

    /**
     * 组件排序
     */
    public function sortSectionItem()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $sectionType = $request->json('sectionType', '');
        $sectionKey = $request->json('sectionKey', '', 'int');

        $oldIndex = $request->json('oldIndex', '');
        $newIndex = $request->json('newIndex', '');

        $service = Be::getAdminService('System.Theme');
        $service->sortSectionItem($themeName, $pageName, $sectionType, $sectionKey, $oldIndex, $newIndex);

        $page = $service->getThemePage($themeName, $pageName);
        $response->set('page', $page);

        $response->success('保存成功！');
    }

}

