<?php

namespace Be\App\System\Controller\Admin;

use Be\AdminPlugin\Form\Item\FormItemDatePickerRange;
use Be\Be;
use Be\Db\Tuple;

/**
 * @BeMenuGroup("网站装修", icon="el-icon-magic-stick", ordering="3")
 * @BePermissionGroup("网站装修", ordering="3")
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
     * @BeMenu("前台主题", icon="el-icon-view", ordering="2.1")
     * @BePermission("前台主题列表", ordering="2.1")
     */
    public function themes()
    {
        $this->themeEditor->themes();
    }

    /**
     * 发现
     *
     * @BePermission("发现前台主题", ordering="2.11")
     */
    public function discover()
    {
        $this->themeEditor->discover();
    }

    /**
     * 设置默认主题
     *
     * @BePermission("设置默认前台主题", ordering="2.12")
     */
    public function toggleDefault()
    {
        $this->themeEditor->toggleDefault();
    }

    /**
     * 配置主题
     *
     * @BePermission("配置前台主题", ordering="2.13")
     */
    public function goSetting()
    {
        $this->themeEditor->goSetting();
    }

    /**
     * 配置主题
     *
     * @BePermission("配置前台主题", ordering="2.13")
     */
    public function setting()
    {
        $this->themeEditor->setting();
    }

    /**
     * 配置主题
     *
     * @BePermission("配置前台主题", ordering="2.13")
     */
    public function editPosition()
    {
        $this->themeEditor->editPosition();
    }

    /**
     * 新增组件
     *
     * @BePermission("配置前台主题", ordering="2.13")
     */
    public function addSection()
    {
        $this->themeEditor->addSection();
    }

    /**
     * 删除组件
     *
     * @BePermission("配置前台主题", ordering="2.13")
     */
    public function deleteSection()
    {
        $this->themeEditor->deleteSection();
    }

    /**
     * 组件排序
     *
     * @BePermission("配置前台主题", ordering="2.13")
     */
    public function sortSection()
    {
        $this->themeEditor->sortSection();
    }

    /**
     * 新增组件子项
     *
     * @BePermission("配置前台主题", ordering="2.13")
     */
    public function addSectionItem()
    {
        $this->themeEditor->addSectionItem();
    }

    /**
     * 删除子组件
     *
     * @BePermission("配置前台主题", ordering="2.13")
     */
    public function deleteSectionItem()
    {
        $this->themeEditor->deleteSectionItem();
    }

    /**
     * 编辑 模板/部件/部件子项
     *
     * @BePermission("配置前台主题", ordering="2.13")
     */
    public function editSectionItem()
    {
        $this->themeEditor->editSectionItem();
    }
    /**
     * 编辑 模板/部件/部件子项 保存
     *
     * @BePermission("配置前台主题", ordering="2.13")
     */
    public function saveSectionItem()
    {
        $this->themeEditor->saveSectionItem();
    }

    /**
     * 模板/部件/部件子项 恢复默认值
     *
     * @BePermission("配置前台主题", ordering="2.13")
     */
    public function resetSectionItem()
    {
        $this->themeEditor->resetSectionItem();
    }

    /**
     * 组件排序
     *
     * @BePermission("配置前台主题", ordering="2.13")
     */
    public function sortSectionItem()
    {
        $this->themeEditor->sortSectionItem();
    }

    /**
     * 更新www
     *
     * @BePermission("更新www", ordering="2.14")
     */
    public function updateWww()
    {
        $this->themeEditor->updateWww();
    }

}

