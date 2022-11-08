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
            $themes = $service->getThemes();
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
                'title' => ($this->themeType === 'Theme' ? '前台' : '后台') . '主题管理',
                'pageSize' => 10,
                'titleRightToolbar' => [
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
                        'maxWidth' => '300',
                        'maxHeight' => '200',
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
                        /*
                        [
                            'name' => 'path',
                            'label' => '路径',
                        ],
                        */
                        [
                            'name' => 'is_default',
                            'label' => '启用',
                            'driver' => CardItemSwitch::class,
                            'target' => 'ajax',
                            'action' => 'toggleDefault',
                            'ui' => [
                                ':disabled' => 'item.is_default === \'1\'',
                                'active-text' => '是',
                                'inactive-text' => '否'
                            ]
                        ],
                    ],

                    'operation' => [
                        'items' => [
                            [
                                'label' => '更新www',
                                'action' => 'updateWww',
                                'target' => 'ajax',
                                'ui' => [
                                    'type' => 'success',
                                ],
                            ],
                            [
                                'label' => '配置',
                                'action' => 'goSetting',
                                'target' => 'blank',
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
                            'name' => 'relativePath',
                            'label' => '路径',
                            'align' => 'left',
                        ],
                        [
                            'name' => 'is_default',
                            'label' => '启用',
                            'driver' => TableItemSwitch::class,
                            'target' => 'ajax',
                            'action' => 'toggleDefault',
                            'width' => '90',
                            'ui' => [
                                ':disabled' => 'scope.row.is_default === \'1\'',
                            ]
                        ],
                    ],
                    'operation' => [
                        'label' => '操作',
                        'width' => '240',
                        'items' => [
                            [
                                'label' => '更新www',
                                'action' => 'updateWww',
                                'target' => 'ajax',
                                'ui' => [
                                    'type' => 'success',
                                ],
                            ],
                            [
                                'label' => '配置',
                                'action' => 'goSetting',
                                'target' => 'blank',
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
            $response->success('发现 ' . $n . ' 个新' . ($this->themeType === 'Theme' ? '前台' : '后台') . '主题！');

            Be::getRuntime()->reload();
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

            beAdminOpLog('启用' . ($this->themeType === 'Theme' ? '前台' : '后台') . '主题：' . $themeName);
            $response->success('启用' . ($this->themeType === 'Theme' ? '前台' : '后台') . '主题成功！');

            Be::getRuntime()->reload();

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
        $pageName = $request->get('pageName', 'default');
        $position = $request->get('position', '');

        $sectionIndex = $request->get('sectionIndex', -1, 'int');
        $sectionName = $request->get('sectionName', '');

        $itemIndex = $request->get('itemIndex', -1, 'int');
        $itemName = $request->get('itemName', '');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $theme = $service->getTheme($themeName);

        $response->set('themeType', $this->themeType);
        $response->set('themeName', $themeName);
        $response->set('theme', $theme);

        $pageTree = $service->getPageTree($themeName);
        $response->set('pageTree', $pageTree);

        $page = $service->getPage($themeName, $pageName);
        $response->set('pageName', $pageName);
        $response->set('page', $page);

        if ($pageName !== 'default') {
            $pageDefault = $service->getPage($themeName, 'default');
            $response->set('pageDefault', $pageDefault);
        }

        $response->set('position', $position);

        $response->set('sectionIndex', $sectionIndex);
        $response->set('sectionName', $sectionName);

        $response->set('itemIndex', $itemIndex);
        $response->set('itemName', $itemName);

        $response->display('App.System.Admin.ThemeEditor.setting');
    }

    /**
     * 编辑 模板
     */
    public function editTheme()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $service = Be::getService('App.System.Admin.' . $this->themeType);

        if ($request->isAjax()) {
            $service->editTheme($themeName, $request->json('formData'));
            $response->set('success', true);
            $response->json();

            Be::getRuntime()->reload();
        } else {
            $drivers = $service->getThemeDrivers($themeName);
            $response->set('drivers', $drivers);

            $response->set('editUrl', beAdminUrl('System.' . $this->themeType . '.editTheme', ['themeName' => $themeName]));
            $response->set('resetUrl', beAdminUrl('System.' . $this->themeType . '.resetTheme', ['themeName' => $themeName]));

            $response->display('App.System.Admin.ThemeEditor.edit', 'Blank');
        }
    }

    /**
     * 主题 恢复默认值
     */
    public function resetTheme()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->resetTheme($themeName);

        $response->success('重置成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 配置页面
     *
     * @return void
     */
    public function editPage()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $service = Be::getService('App.System.Admin.' . $this->themeType);

        if ($request->isAjax()) {
            $service->editPage($themeName, $pageName, $request->json('formData'));
            $response->set('success', true);
            $response->json();

            Be::getRuntime()->reload();
        } else {
            $drivers = $service->getPageDrivers($themeName, $pageName);
            $response->set('drivers', $drivers);

            $response->set('editUrl', beAdminUrl('System.' . $this->themeType . '.editPage', ['themeName' => $themeName, 'pageName' => $pageName]));
            $response->set('resetUrl', beAdminUrl('System.' . $this->themeType . '.resetPage', ['themeName' => $themeName, 'pageName' => $pageName]));

            $response->display('App.System.Admin.ThemeEditor.edit', 'Blank');
        }
    }

    /**
     * 页面 恢复默认值
     */
    public function resetPage()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->resetPage($themeName, $pageName);

        $response->success('重置成功！');

        Be::getRuntime()->reload();
    }


    /**
     * 配置方位
     *
     * @return void
     */
    public function editPosition()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');
        $position = $request->get('position', '');

        $service = Be::getService('App.System.Admin.' . $this->themeType);

        if ($request->isAjax()) {
            $service->editPosition($themeName, $pageName, $position, $request->json('formData'));
            $response->set('success', true);
            $response->json();

            Be::getRuntime()->reload();
        } else {
            $response->set('themeType', $this->themeType);
            $response->set('themeName', $themeName);
            $response->set('pageName', $pageName);
            $response->set('position', $position);

            $positionDescription = $service->getPositionDescription($position);
            $response->set('positionDescription', $positionDescription);

            // 获取当前页数的配置信息
            if ($pageName === 'default') {
                $configPage = Be::getConfig($this->themeType . '.' . $themeName . '.Page');
            } else {
                $configPage = Be::getConfig($this->themeType . '.' . $themeName . '.Page.' . $pageName);
            }
            $response->set('configPage', $configPage);

            $response->display('App.System.Admin.ThemeEditor.editPosition', 'Blank');
        }
    }

    /**
     * 方位 恢复默认值
     */
    public function resetPosition()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $position = $request->get('position', '');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->resetPosition($themeName, $pageName, $position);

        $response->success('重置成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 编辑 部件
     */
    public function editSection()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', 'Home');

        $position = $request->get('position', '');
        $sectionIndex = $request->get('sectionIndex', -1, 'int');

        $service = Be::getService('App.System.Admin.' . $this->themeType);

        if ($request->isAjax()) {
            $service->editSection($themeName, $pageName, $position, $sectionIndex, $request->json('formData'));
            $response->set('success', true);
            $response->json();

            Be::getRuntime()->reload();
        } else {
            $drivers = $service->getSectionDrivers($themeName, $pageName, $position, $sectionIndex);
            $response->set('drivers', $drivers);

            $response->set('editUrl', beAdminUrl('System.' . $this->themeType . '.editSection', ['themeName' => $themeName, 'pageName' => $pageName, 'position' => $position, 'sectionIndex' => $sectionIndex]));
            $response->set('resetUrl', beAdminUrl('System.' . $this->themeType . '.resetSection', ['themeName' => $themeName, 'pageName' => $pageName, 'position' => $position, 'sectionIndex' => $sectionIndex]));

            $response->display('App.System.Admin.ThemeEditor.edit', 'Blank');
        }
    }

    /**
     * 新增部件
     */
    public function addSection()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $position = $request->json('position', '');

        $sectionName = $request->json('sectionName', '');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->addSection($themeName, $pageName, $position, $sectionName);

        $page = $service->getPage($themeName, $pageName);
        $response->set('page', $page);

        $response->success('保存成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 删除部件
     */
    public function deleteSection()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $position = $request->json('position', '');

        $sectionIndex = $request->json('sectionIndex', -1, 'int');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->deleteSection($themeName, $pageName, $position, $sectionIndex);

        $page = $service->getPage($themeName, $pageName);
        $response->set('page', $page);

        $response->success('保存成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 部件排序
     */
    public function sortSection()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $position = $request->json('position', '');

        $oldIndex = $request->json('oldIndex', -1, 'int');
        $newIndex = $request->json('newIndex', -1, 'int');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->sortSection($themeName, $pageName, $position, $oldIndex, $newIndex);

        $page = $service->getPage($themeName, $pageName);
        $response->set('page', $page);

        $response->success('保存成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 部件 恢复默认值
     */
    public function resetSection()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $position = $request->get('position', '');
        $sectionIndex = $request->get('sectionIndex', -1, 'int');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->resetSection($themeName, $pageName, $position, $sectionIndex);

        $response->success('重置成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 编辑部件子项
     */
    public function editSectionItem()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', 'Home');

        $position = $request->get('position', '');
        $sectionIndex = $request->get('sectionIndex', -1, 'int');

        $itemIndex = $request->get('itemIndex', -1, 'int');

        $service = Be::getService('App.System.Admin.' . $this->themeType);

        if ($request->isAjax()) {
            $service->editSectionItem($themeName, $pageName, $position, $sectionIndex, $itemIndex, $request->json('formData'));
            $response->set('success', true);
            $response->json();

            Be::getRuntime()->reload();
        } else {
            $drivers = $service->getSectionItemDrivers($themeName, $pageName, $position, $sectionIndex, $itemIndex);
            $response->set('drivers', $drivers);

            $response->set('editUrl', beAdminUrl('System.' . $this->themeType . '.editSectionItem', ['themeName' => $themeName, 'pageName' => $pageName, 'position' => $position, 'sectionIndex' => $sectionIndex, 'itemIndex' => $itemIndex]));
            $response->set('resetUrl', beAdminUrl('System.' . $this->themeType . '.resetSectionItem', ['themeName' => $themeName, 'pageName' => $pageName, 'position' => $position, 'sectionIndex' => $sectionIndex, 'itemIndex' => $itemIndex]));

            $response->display('App.System.Admin.ThemeEditor.edit', 'Blank');
        }
    }

    /**
     * 新增部件子项
     */
    public function addSectionItem()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $position = $request->get('position', '');
        $sectionIndex = $request->get('sectionIndex', -1, 'int');

        $itemName = $request->get('itemName', '');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->addSectionItem($themeName, $pageName, $position, $sectionIndex, $itemName);

        $page = $service->getPage($themeName, $pageName);
        $response->set('page', $page);

        $response->success('保存成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 删除部件子项
     */
    public function deleteSectionItem()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $position = $request->json('position', '');
        $sectionIndex = $request->json('sectionIndex', -1, 'int');

        $itemIndex = $request->json('itemIndex', -1, 'int');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->deleteSectionItem($themeName, $pageName, $position, $sectionIndex, $itemIndex);

        $page = $service->getPage($themeName, $pageName);
        $response->set('page', $page);

        $response->success('保存成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 部件子项排序
     */
    public function sortSectionItem()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $position = $request->json('position', '');

        $sectionIndex = $request->json('sectionIndex', -1, 'int');

        $oldIndex = $request->json('oldIndex', -1, 'int');
        $newIndex = $request->json('newIndex', -1, 'int');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->sortSectionItem($themeName, $pageName, $position, $sectionIndex, $oldIndex, $newIndex);

        $page = $service->getPage($themeName, $pageName);
        $response->set('page', $page);

        $response->success('保存成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 部件子项恢复默认值
     */
    public function resetSectionItem()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $themeName = $request->get('themeName', '');
        $pageName = $request->get('pageName', '');

        $position = $request->get('position', '');
        $sectionIndex = $request->get('sectionIndex', -1, 'int');

        $itemIndex = $request->get('itemIndex', -1, 'int');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->resetSectionItem($themeName, $pageName, $position, $sectionIndex, $itemIndex);

        $response->success('重置成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 更新www
     *
     * @BePermission("更新www", ordering="2.13")
     */
    public function updateWww()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $postData = $request->json();

        if (!isset($postData['row']['name'])) {
            $response->error('参数主题名称缺失！');
        }

        $themeName = $postData['row']['name'];

        try {
            $serviceApp = Be::getService('App.System.Admin.Theme');
            $serviceApp->updateWww($this->themeType, $themeName);

            beAdminOpLog('更新主题www：' . $themeName);
            $response->success('更新主题www成功！');
        } catch (\Throwable $t) {
            $response->error($t->getMessage());
        }
    }
}

