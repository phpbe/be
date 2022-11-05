<?php

namespace Be\App\System\Controller\Admin;

use Be\AdminPlugin\Form\Item\FormItemDatePickerRange;
use Be\Be;
use Be\Db\Tuple;

/**
 * @BeMenuGroup("网站装修")
 * @BePermissionGroup("网站装修")
 */
class Theme extends Auth
{

    private $themeEditor = null;

    public function __construct()
    {
        parent::__construct();

        $this->themeEditor = new ThemeEditor('Theme');
    }

    /**
     * @BeMenu("前台主题", icon="el-icon-view", ordering="2.2")
     * @BePermission("前台主题列表", ordering="2.2")
     */
    public function themes()
    {
        $this->themeEditor->themes();
    }

    /**
     * 发现
     *
     * @BePermission("发现前台主题", ordering="2.21")
     */
    public function discover()
    {
        $this->themeEditor->discover();
    }

    /**
     * 设置默认主题
     *
     * @BePermission("设置默认前台主题", ordering="2.22")
     */
    public function toggleDefault()
    {
        $this->themeEditor->toggleDefault();
    }

    /**
     * 配置主题
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function goSetting()
    {
        $this->themeEditor->goSetting();
    }

    /**
     * 配置主题
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function setting()
    {
        $this->themeEditor->setting();
    }

    /**
     * 配置主题
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function editTheme()
    {
        $this->themeEditor->editTheme();
    }

    /**
     * 重置主题
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function resetTheme()
    {
        $this->themeEditor->resetTheme();
    }

    /**
     * 配置页面
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function editPage()
    {
        $this->themeEditor->editPage();
    }

    /**
     * 重置页面
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function resetPage()
    {
        $this->themeEditor->resetPage();
    }

    /**
     * 配置方位
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function editPosition()
    {
        $this->themeEditor->editPosition();
    }

    /**
     * 重置方位
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function resetPosition()
    {
        $this->themeEditor->resetPosition();
    }

    /**
     * 编辑部件
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function editSection()
    {
        $this->themeEditor->editSection();
    }

    /**
     * 新增部件
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function addSection()
    {
        $this->themeEditor->addSection();
    }

    /**
     * 删除部件
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function deleteSection()
    {
        $this->themeEditor->deleteSection();
    }

    /**
     * 部件排序
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function sortSection()
    {
        $this->themeEditor->sortSection();
    }

    /**
     * 部件重置
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function resetSection()
    {
        $this->themeEditor->resetSection();
    }

    /**
     * 编辑部件子项
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function editSectionItem()
    {
        $this->themeEditor->editSectionItem();
    }

    /**
     * 新增部件子项
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function addSectionItem()
    {
        $this->themeEditor->addSectionItem();
    }

    /**
     * 删除部件子项
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function deleteSectionItem()
    {
        $this->themeEditor->deleteSectionItem();
    }

    /**
     * 部件子项排序
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function sortSectionItem()
    {
        $this->themeEditor->sortSectionItem();
    }

    /**
     * 部件子项恢复默认值
     *
     * @BePermission("配置前台主题", ordering="2.23")
     */
    public function resetSectionItem()
    {
        $this->themeEditor->resetSectionItem();
    }

    /**
     * 更新www
     *
     * @BePermission("更新www", ordering="2.24")
     */
    public function updateWww()
    {
        $this->themeEditor->updateWww();
    }

}

