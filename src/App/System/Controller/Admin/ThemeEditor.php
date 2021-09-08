<?php

namespace Be\App\System\Controller\Admin;

use Be\AdminPlugin\Card\Item\CardItemSwitch;
use Be\AdminPlugin\Table\Item\TableItemImage;
use Be\AdminPlugin\Table\Item\TableItemSwitch;
use Be\Be;

/**
 * 主题管理
 */
class ThemeEditor
{

    protected $themeType = 'Theme';

    public function __construct($themeType)
    {
        $this->themeType = $themeType;
    }

    /**
     * 主题列表
     */
    public function themes()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        if ($request->isAjax()) {
            $postData = $request->json();
            $service = Be::getService('App.System.Admin.' . $this->themeType);
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
                'title' => ($this->themeType == 'Theme' ? '前台' : '后台') . '主题列表',
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
                        [
                            'name' => 'is_enable',
                            'label' => '',
                            'driver' => CardItemSwitch::class,
                            'target' => 'ajax',
                            'action' => 'toggleEnable',
                            'ui' => [
                                'active-text' => '启用',
                                'inactive-text' => '禁用'
                            ]
                        ],
                        [
                            'name' => 'is_default',
                            'label' => '当前主题',
                            'driver' => CardItemSwitch::class,
                            'target' => 'ajax',
                            'action' => 'toggleDefault',
                            'ui' => [
                                ':disabled' => 'item.is_default == \'1\'',
                                'active-text' => '是',
                                'inactive-text' => '否'
                            ]
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
                            'action' => 'toggleDefault',
                            'width' => '90',
                            'ui' => [
                                ':disabled' => 'scope.row.is_default == \'1\'',
                            ]
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
     */
    public function discover()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        try {
            $serviceTheme = Be::getService('App.System.Admin.' . $this->themeType);
            $n = $serviceTheme->discover();
            $response->success('发现 ' . $n . ' 个新' . ($this->themeType == 'Theme' ? '前台' : '后台') . '主题！');
        } catch (\Throwable $t) {
            $response->error($t->getMessage());
        }
    }

    /**
     * 启用/禁用主题
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
            $serviceTheme = Be::getService('App.System.Admin.' . $this->themeType);
            $serviceTheme->toggleEnable($themeName, $isEnable);

            beAdminOpLog(($isEnable ? '启用' : '禁用') . ($this->themeType == 'Theme' ? '前台' : '后台') . '主题：' . $themeName);
            $response->success(($isEnable ? '启用' : '禁用') . ($this->themeType == 'Theme' ? '前台' : '后台') . '主题成功！');
        } catch (\Throwable $t) {
            $response->error($t->getMessage());
        }
    }

    /**
     * 设置默认主题
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
            $serviceTheme = Be::getService('App.System.Admin.' . $this->themeType);
            $serviceTheme->toggleDefault($themeName);

            beAdminOpLog('设置默认' . ($this->themeType == 'Theme' ? '前台' : '后台') . '主题：' . $themeName);
            $response->success('设置默认' . ($this->themeType == 'Theme' ? '前台' : '后台') . '主题成功！');
        } catch (\Throwable $t) {
            $response->error($t->getMessage());
        }
    }

    /**
     * 配置主题
     */
    public function goSetting()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        $postData = $request->post('data', '', '');
        $postData = json_decode($postData, true);
        $url = beAdminUrl('System.' . $this->themeType . '.setting', ['themeName' => $postData['row']['name']]);
        $response->redirect($url);
    }

    /**
     * 配置主题
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

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $theme = $service->getTheme($themeName);

        $response->set('themeType', $this->themeType);
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

        $response->display('App.System.Admin.ThemeEditor.setting');
    }


    public function enableSectionType()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $sectionType = $request->get('sectionType', '');

        $service = Be::getService('App.System.Admin.Theme');
        $service->enableSectionType($themeName, $pageName, $sectionType);

        $url = beAdminUrl('System.' . $this->themeType . '.setting', ['themeName' => $themeName, 'pageName' => $pageName]);
        $response->redirect($url);
    }

    public function disableSectionType()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $sectionType = $request->get('sectionType', '');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->disableSectionType($themeName, $pageName, $sectionType);

        $url = beAdminUrl('System.' . $this->themeType . '.setting', ['themeName' => $themeName, 'pageName' => $pageName]);
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

        $service = Be::getService('App.System.Admin.' . $this->themeType);
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

        $service = Be::getService('App.System.Admin.' . $this->themeType);
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

        $service = Be::getService('App.System.Admin.' . $this->themeType);
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

        $service = Be::getService('App.System.Admin.' . $this->themeType);
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

        $service = Be::getService('App.System.Admin.' . $this->themeType);
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

        $response->set('themeType', $this->themeType);
        $response->set('themeName', $themeName);
        $response->set('pageName', $pageName);
        $response->set('sectionType', $sectionType);
        $response->set('sectionKey', $sectionKey);
        $response->set('itemKey', $itemKey);

        $service = Be::getService('App.System.Admin.' . $this->themeType);
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

        $response->display('App.System.Admin.ThemeEditor.editSectionItem', 'Blank');
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

        $service = Be::getService('App.System.Admin.' . $this->themeType);
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

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->sortSectionItem($themeName, $pageName, $sectionType, $sectionKey, $oldIndex, $newIndex);

        $page = $service->getThemePage($themeName, $pageName);
        $response->set('page', $page);

        $response->success('保存成功！');
    }

}

