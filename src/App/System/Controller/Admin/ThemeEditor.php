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
        
        
        if (Request::isAjax()) {
            $postData = Request::json();
            $service = Be::getService('App.System.Admin.' . $this->themeType);
            $themes = $service->getThemes();
            $page = $postData['page'];
            $pageSize = $postData['pageSize'];
            $gridData = array_slice($themes, ($page - 1) * $pageSize, $pageSize);
            Resonse::set('success', true);
            Resonse::set('data', [
                'total' => count($themes),
                'gridData' => $gridData,
            ]);
            Resonse::json();
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
        
        
        try {
            $serviceTheme = Be::getService('App.System.Admin.' . $this->themeType);
            $n = $serviceTheme->discover();
            Resonse::success('发现 ' . $n . ' 个新' . ($this->themeType === 'Theme' ? '前台' : '后台') . '主题！');

            Be::getRuntime()->reload();
        } catch (\Throwable $t) {
            Resonse::error($t->getMessage());
        }
    }

    /**
     * 设置默认主题
     */
    public function toggleDefault()
    {
        
        

        $postData = Request::json();

        if (!isset($postData['row']['name'])) {
            Resonse::error('参数主题名缺失！');
        }

        $themeName = $postData['row']['name'];

        try {
            $serviceTheme = Be::getService('App.System.Admin.' . $this->themeType);
            $serviceTheme->toggleDefault($themeName);

            beAdminOpLog('启用' . ($this->themeType === 'Theme' ? '前台' : '后台') . '主题：' . $themeName);
            Resonse::success('启用' . ($this->themeType === 'Theme' ? '前台' : '后台') . '主题成功！');

            Be::getRuntime()->reload();

        } catch (\Throwable $t) {
            Resonse::error($t->getMessage());
        }
    }

    /**
     * 配置主题
     */
    public function goSetting()
    {
        
        
        $postData = Request::post('data', '', '');
        $postData = json_decode($postData, true);
        $url = beAdminUrl('System.' . $this->themeType . '.setting', ['themeName' => $postData['row']['name']]);
        Resonse::redirect($url);
    }

    /**
     * 配置主题
     */
    public function setting()
    {
        
        

        $themeName = Request::get('themeName', '');
        $pageName = Request::get('pageName', 'default');
        $position = Request::get('position', '');

        $sectionIndex = Request::get('sectionIndex', -1, 'int');
        $sectionName = Request::get('sectionName', '');

        $itemIndex = Request::get('itemIndex', -1, 'int');
        $itemName = Request::get('itemName', '');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $theme = $service->getTheme($themeName);

        Resonse::set('themeType', $this->themeType);
        Resonse::set('themeName', $themeName);
        Resonse::set('theme', $theme);

        $pageTree = $service->getPageTree($themeName);
        Resonse::set('pageTree', $pageTree);

        $page = $service->getPage($themeName, $pageName);
        Resonse::set('pageName', $pageName);
        Resonse::set('page', $page);

        if ($pageName !== 'default') {
            $pageDefault = $service->getPage($themeName, 'default');
            Resonse::set('pageDefault', $pageDefault);
        }

        Resonse::set('position', $position);

        Resonse::set('sectionIndex', $sectionIndex);
        Resonse::set('sectionName', $sectionName);

        Resonse::set('itemIndex', $itemIndex);
        Resonse::set('itemName', $itemName);

        Resonse::display('App.System.Admin.ThemeEditor.setting');
    }

    /**
     * 编辑 模板
     */
    public function editTheme()
    {
        
        

        $themeName = Request::get('themeName', '');
        $service = Be::getService('App.System.Admin.' . $this->themeType);

        if (Request::isAjax()) {
            $service->editTheme($themeName, Request::json('formData'));
            Resonse::set('success', true);
            Resonse::json();

            Be::getRuntime()->reload();
        } else {
            $drivers = $service->getThemeDrivers($themeName);
            Resonse::set('drivers', $drivers);

            Resonse::set('editUrl', beAdminUrl('System.' . $this->themeType . '.editTheme', ['themeName' => $themeName]));
            Resonse::set('resetUrl', beAdminUrl('System.' . $this->themeType . '.resetTheme', ['themeName' => $themeName]));

            Resonse::display('App.System.Admin.ThemeEditor.edit', 'Blank');
        }
    }

    /**
     * 主题 恢复默认值
     */
    public function resetTheme()
    {
        
        

        $themeName = Request::get('themeName', '');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->resetTheme($themeName);

        Resonse::success('重置成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 配置页面
     *
     * @return void
     */
    public function editPage()
    {
        
        

        $themeName = Request::get('themeName', '');
        $pageName = Request::get('pageName', '');

        $service = Be::getService('App.System.Admin.' . $this->themeType);

        if (Request::isAjax()) {
            $service->editPage($themeName, $pageName, Request::json('formData'));
            Resonse::set('success', true);
            Resonse::json();

            Be::getRuntime()->reload();
        } else {
            $drivers = $service->getPageDrivers($themeName, $pageName);
            Resonse::set('drivers', $drivers);

            Resonse::set('editUrl', beAdminUrl('System.' . $this->themeType . '.editPage', ['themeName' => $themeName, 'pageName' => $pageName]));
            Resonse::set('resetUrl', beAdminUrl('System.' . $this->themeType . '.resetPage', ['themeName' => $themeName, 'pageName' => $pageName]));

            Resonse::display('App.System.Admin.ThemeEditor.edit', 'Blank');
        }
    }

    /**
     * 页面 恢复默认值
     */
    public function resetPage()
    {
        
        

        $themeName = Request::get('themeName', '');
        $pageName = Request::get('pageName', '');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->resetPage($themeName, $pageName);

        Resonse::success('重置成功！');

        Be::getRuntime()->reload();
    }


    /**
     * 配置方位
     *
     * @return void
     */
    public function editPosition()
    {
        
        

        $themeName = Request::get('themeName', '');
        $pageName = Request::get('pageName', '');
        $position = Request::get('position', '');

        $service = Be::getService('App.System.Admin.' . $this->themeType);

        if (Request::isAjax()) {
            $service->editPosition($themeName, $pageName, $position, Request::json('formData'));
            Resonse::set('success', true);
            Resonse::json();

            Be::getRuntime()->reload();
        } else {
            Resonse::set('themeType', $this->themeType);
            Resonse::set('themeName', $themeName);
            Resonse::set('pageName', $pageName);
            Resonse::set('position', $position);

            $positionDescription = $service->getPositionDescription($position);
            Resonse::set('positionDescription', $positionDescription);

            // 获取当前页数的配置信息
            if ($pageName === 'default') {
                $configPage = Be::getConfig($this->themeType . '.' . $themeName . '.Page');
            } else {
                $configPage = Be::getConfig($this->themeType . '.' . $themeName . '.Page.' . $pageName);
            }
            Resonse::set('configPage', $configPage);

            Resonse::display('App.System.Admin.ThemeEditor.editPosition', 'Blank');
        }
    }

    /**
     * 方位 恢复默认值
     */
    public function resetPosition()
    {
        
        

        $themeName = Request::get('themeName', '');
        $pageName = Request::get('pageName', '');

        $position = Request::get('position', '');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->resetPosition($themeName, $pageName, $position);

        Resonse::success('重置成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 编辑 部件
     */
    public function editSection()
    {
        
        

        $themeName = Request::get('themeName', '');
        $pageName = Request::get('pageName', 'Home');

        $position = Request::get('position', '');
        $sectionIndex = Request::get('sectionIndex', -1, 'int');

        $service = Be::getService('App.System.Admin.' . $this->themeType);

        if (Request::isAjax()) {
            $service->editSection($themeName, $pageName, $position, $sectionIndex, Request::json('formData'));
            Resonse::set('success', true);
            Resonse::json();

            Be::getRuntime()->reload();
        } else {
            $drivers = $service->getSectionDrivers($themeName, $pageName, $position, $sectionIndex);
            Resonse::set('drivers', $drivers);

            Resonse::set('editUrl', beAdminUrl('System.' . $this->themeType . '.editSection', ['themeName' => $themeName, 'pageName' => $pageName, 'position' => $position, 'sectionIndex' => $sectionIndex]));
            Resonse::set('resetUrl', beAdminUrl('System.' . $this->themeType . '.resetSection', ['themeName' => $themeName, 'pageName' => $pageName, 'position' => $position, 'sectionIndex' => $sectionIndex]));

            Resonse::display('App.System.Admin.ThemeEditor.edit', 'Blank');
        }
    }

    /**
     * 新增部件
     */
    public function addSection()
    {
        
        

        $themeName = Request::get('themeName', '');
        $pageName = Request::get('pageName', '');

        $position = Request::json('position', '');

        $sectionName = Request::json('sectionName', '');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->addSection($themeName, $pageName, $position, $sectionName);

        $page = $service->getPage($themeName, $pageName);
        Resonse::set('page', $page);

        Resonse::success('保存成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 删除部件
     */
    public function deleteSection()
    {
        
        

        $themeName = Request::get('themeName', '');
        $pageName = Request::get('pageName', '');

        $position = Request::json('position', '');

        $sectionIndex = Request::json('sectionIndex', -1, 'int');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->deleteSection($themeName, $pageName, $position, $sectionIndex);

        $page = $service->getPage($themeName, $pageName);
        Resonse::set('page', $page);

        Resonse::success('保存成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 部件排序
     */
    public function sortSection()
    {
        
        

        $themeName = Request::get('themeName', '');
        $pageName = Request::get('pageName', '');

        $position = Request::json('position', '');

        $oldIndex = Request::json('oldIndex', -1, 'int');
        $newIndex = Request::json('newIndex', -1, 'int');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->sortSection($themeName, $pageName, $position, $oldIndex, $newIndex);

        $page = $service->getPage($themeName, $pageName);
        Resonse::set('page', $page);

        Resonse::success('保存成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 部件 恢复默认值
     */
    public function resetSection()
    {
        
        

        $themeName = Request::get('themeName', '');
        $pageName = Request::get('pageName', '');

        $position = Request::get('position', '');
        $sectionIndex = Request::get('sectionIndex', -1, 'int');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->resetSection($themeName, $pageName, $position, $sectionIndex);

        Resonse::success('重置成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 编辑部件子项
     */
    public function editSectionItem()
    {
        
        

        $themeName = Request::get('themeName', '');
        $pageName = Request::get('pageName', 'Home');

        $position = Request::get('position', '');
        $sectionIndex = Request::get('sectionIndex', -1, 'int');

        $itemIndex = Request::get('itemIndex', -1, 'int');

        $service = Be::getService('App.System.Admin.' . $this->themeType);

        if (Request::isAjax()) {
            $service->editSectionItem($themeName, $pageName, $position, $sectionIndex, $itemIndex, Request::json('formData'));
            Resonse::set('success', true);
            Resonse::json();

            Be::getRuntime()->reload();
        } else {
            $drivers = $service->getSectionItemDrivers($themeName, $pageName, $position, $sectionIndex, $itemIndex);
            Resonse::set('drivers', $drivers);

            Resonse::set('editUrl', beAdminUrl('System.' . $this->themeType . '.editSectionItem', ['themeName' => $themeName, 'pageName' => $pageName, 'position' => $position, 'sectionIndex' => $sectionIndex, 'itemIndex' => $itemIndex]));
            Resonse::set('resetUrl', beAdminUrl('System.' . $this->themeType . '.resetSectionItem', ['themeName' => $themeName, 'pageName' => $pageName, 'position' => $position, 'sectionIndex' => $sectionIndex, 'itemIndex' => $itemIndex]));

            Resonse::display('App.System.Admin.ThemeEditor.edit', 'Blank');
        }
    }

    /**
     * 新增部件子项
     */
    public function addSectionItem()
    {
        
        

        $themeName = Request::get('themeName', '');
        $pageName = Request::get('pageName', '');

        $position = Request::get('position', '');
        $sectionIndex = Request::get('sectionIndex', -1, 'int');

        $itemName = Request::get('itemName', '');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->addSectionItem($themeName, $pageName, $position, $sectionIndex, $itemName);

        $page = $service->getPage($themeName, $pageName);
        Resonse::set('page', $page);

        Resonse::success('保存成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 删除部件子项
     */
    public function deleteSectionItem()
    {
        
        

        $themeName = Request::get('themeName', '');
        $pageName = Request::get('pageName', '');

        $position = Request::json('position', '');
        $sectionIndex = Request::json('sectionIndex', -1, 'int');

        $itemIndex = Request::json('itemIndex', -1, 'int');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->deleteSectionItem($themeName, $pageName, $position, $sectionIndex, $itemIndex);

        $page = $service->getPage($themeName, $pageName);
        Resonse::set('page', $page);

        Resonse::success('保存成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 部件子项排序
     */
    public function sortSectionItem()
    {
        
        

        $themeName = Request::get('themeName', '');
        $pageName = Request::get('pageName', '');

        $position = Request::json('position', '');

        $sectionIndex = Request::json('sectionIndex', -1, 'int');

        $oldIndex = Request::json('oldIndex', -1, 'int');
        $newIndex = Request::json('newIndex', -1, 'int');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->sortSectionItem($themeName, $pageName, $position, $sectionIndex, $oldIndex, $newIndex);

        $page = $service->getPage($themeName, $pageName);
        Resonse::set('page', $page);

        Resonse::success('保存成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 部件子项恢复默认值
     */
    public function resetSectionItem()
    {
        
        

        $themeName = Request::get('themeName', '');
        $pageName = Request::get('pageName', '');

        $position = Request::get('position', '');
        $sectionIndex = Request::get('sectionIndex', -1, 'int');

        $itemIndex = Request::get('itemIndex', -1, 'int');

        $service = Be::getService('App.System.Admin.' . $this->themeType);
        $service->resetSectionItem($themeName, $pageName, $position, $sectionIndex, $itemIndex);

        Resonse::success('重置成功！');

        Be::getRuntime()->reload();
    }

    /**
     * 更新www
     *
     * @BePermission("更新www", ordering="2.13")
     */
    public function updateWww()
    {
        
        

        $postData = Request::json();

        if (!isset($postData['row']['name'])) {
            Resonse::error('参数主题名称缺失！');
        }

        $themeName = $postData['row']['name'];

        try {
            $serviceApp = Be::getService('App.System.Admin.Theme');
            $serviceApp->updateWww($this->themeType, $themeName);

            beAdminOpLog('更新主题www：' . $themeName);
            Resonse::success('更新主题www成功！');
        } catch (\Throwable $t) {
            Resonse::error($t->getMessage());
        }
    }
}

