<?php

namespace Be\App\System\Controller\Admin;

use Be\AdminPlugin\Form\Item\FormItemDatePickerRange;
use Be\Be;
use Be\Db\Tuple;

/**
 * @BeMenuGroup("管理")
 * @BePermissionGroup("管理")
 */
class Theme
{

    private $themeEditor = null;

    public function __construct()
    {
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
     * 启用/禁用前台主题
     *
     * @BePermission("启用/禁用前台主题", ordering="2.22")
     */
    public function toggleEnable()
    {
        $this->themeEditor->toggleEnable();
    }

    /**
     * 设置默认主题
     *
     * @BePermission("设置默认前台主题", ordering="2.23")
     */
    public function toggleDefault()
    {
        $this->themeEditor->toggleDefault();
    }

    /**
     * 配置主题
     *
     * @BePermission("配置前台主题", ordering="2.24")
     */
    public function goSetting()
    {
        $this->themeEditor->goSetting();
    }

    /**
     * 配置主题
     *
     * @BePermission("配置前台主题", ordering="2.24")
     */
    public function setting()
    {
        $this->themeEditor->setting();
    }

    /**
     * 启用 section type
     *
     * @BePermission("配置前台主题", ordering="2.24")
     */
    public function enableSectionType()
    {
        $this->themeEditor->enableSectionType();
    }

    /**
     * 禁用 section type
     *
     * @BePermission("配置前台主题", ordering="2.24")
     */
    public function disableSectionType()
    {
        $this->themeEditor->disableSectionType();
    }

    /**
     * 新增组件
     *
     * @BePermission("配置前台主题", ordering="2.24")
     */
    public function addSection()
    {
        $this->themeEditor->addSection();
    }

    /**
     * 删除组件
     *
     * @BePermission("配置前台主题", ordering="2.24")
     */
    public function deleteSection()
    {
        $this->themeEditor->deleteSection();
    }

    /**
     * 组件排序
     *
     * @BePermission("配置前台主题", ordering="2.24")
     */
    public function sortSection()
    {
        $this->themeEditor->sortSection();
    }

    /**
     * 新增组件子项
     *
     * @BePermission("配置前台主题", ordering="2.24")
     */
    public function addSectionItem()
    {
        $this->themeEditor->addSectionItem();
    }

    /**
     * 删除子组件
     *
     * @BePermission("配置前台主题", ordering="2.24")
     */
    public function deleteSectionItem()
    {
        $this->themeEditor->deleteSectionItem();
    }

    /**
     * 编辑组件子项
     *
     * @BePermission("配置前台主题", ordering="2.24")
     */
    public function editSectionItem()
    {
        $this->themeEditor->editSectionItem();
    }

    /**
     * 编辑组件子项保存
     *
     * @BePermission("配置前台主题", ordering="2.24")
     */
    public function saveSectionItem()
    {
        $this->themeEditor->saveSectionItem();
    }

    /**
     * 组件排序
     *
     * @BePermission("配置前台主题", ordering="2.24")
     */
    public function sortSectionItem()
    {
        $this->themeEditor->sortSectionItem();
    }

}

