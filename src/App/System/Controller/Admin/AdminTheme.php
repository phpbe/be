<?php
namespace Be\App\System\Controller\Admin;

use Be\AdminPlugin\Form\Item\FormItemDatePickerRange;
use Be\AdminPlugin\Table\Item\TableItemCustom;
use Be\AdminPlugin\Table\Item\TableItemIcon;
use Be\AdminPlugin\Table\Item\TableItemImage;
use Be\AdminPlugin\Table\Item\TableItemSwitch;
use Be\AdminPlugin\Table\Item\TableItemTag;
use Be\Be;
use Be\Db\Tuple;

/**
 * @BeMenuGroup("管理")
 * @BePermissionGroup("管理")
 */
class AdminTheme
{

    private $themeEditor = null;

    public function __construct()
    {
        $this->themeEditor = new ThemeEditor('AdminTheme');
    }

    /**
     * @BeMenu("后台主题", icon="el-icon-view", ordering="2.3")
     * @BePermission("后台主题列表", ordering="2.3")
     */
    public function themes()
    {
        $this->themeEditor->themes();
    }

    /**
     * 发现
     *
     * @BePermission("发现主题", ordering="2.32")
     */
    public function discover()
    {
        $this->themeEditor->discover();
    }

    /**
     * 启用/禁用主题
     *
     * @BePermission("启用/禁用主题", ordering="2.32")
     */
    public function toggleEnable()
    {
        $this->themeEditor->toggleEnable();
    }

    /**
     * 设置默认主题
     *
     * @BePermission("设置默认主题", ordering="2.32")
     */
    public function toggleDefault()
    {
        $this->themeEditor->toggleDefault();
    }

    /**
     * 配置主题
     *
     * @BePermission("配置主题", ordering="2.32")
     */
    public function goSetting()
    {
        $this->themeEditor->goSetting();
    }

    /**
     * 配置主题
     *
     * @BePermission("配置主题", ordering="2.32")
     */
    public function setting()
    {
        $this->themeEditor->setting();
    }

    public function enableSectionType()
    {
        $this->themeEditor->enableSectionType();
    }

    public function disableSectionType()
    {
        $this->themeEditor->disableSectionType();
    }

    /**
     * 新增组件
     */
    public function addSection()
    {
        $this->themeEditor->addSection();
    }

    /**
     * 删除组件
     */
    public function deleteSection()
    {
        $this->themeEditor->deleteSection();
    }

    /**
     * 组件排序
     */
    public function sortSection()
    {
        $this->themeEditor->sortSection();
    }

    /**
     * 新增组件子项
     */
    public function addSectionItem()
    {
        $this->themeEditor->addSectionItem();
    }

    /**
     * 删除子组件
     */
    public function deleteSectionItem()
    {
        $this->themeEditor->deleteSectionItem();
    }

    /**
     * 编辑组件子项
     */
    public function editSectionItem()
    {
        $this->themeEditor->editSectionItem();
    }

    /**
     * 编辑组件子项保存
     */
    public function saveSectionItem()
    {
        $this->themeEditor->saveSectionItem();
    }

    /**
     * 组件排序
     */
    public function sortSectionItem()
    {
        $this->themeEditor->sortSectionItem();
    }



}

