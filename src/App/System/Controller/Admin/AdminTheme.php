<?php
namespace Be\App\System\Controller\Admin;

/**
 * @BeMenuGroup("网站装修")
 * @BePermissionGroup("网站装修")
 */
class AdminTheme extends Auth
{

    private $themeEditor = null;

    public function __construct()
    {
        parent::__construct();

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
     * @BePermission("发现后台主题", ordering="2.31")
     */
    public function discover()
    {
        $this->themeEditor->discover();
    }

    /**
     * 设置默认主题
     *
     * @BePermission("设置默认后台主题", ordering="2.32")
     */
    public function toggleDefault()
    {
        $this->themeEditor->toggleDefault();
    }

    /**
     * 配置主题
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function goSetting()
    {
        $this->themeEditor->goSetting();
    }

    /**
     * 配置主题
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function setting()
    {
        $this->themeEditor->setting();
    }

    /**
     * 配置主题
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function editTheme()
    {
        $this->themeEditor->editTheme();
    }

    /**
     * 重置主题
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function resetTheme()
    {
        $this->themeEditor->resetTheme();
    }

    /**
     * 配置页面
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function editPage()
    {
        $this->themeEditor->editPage();
    }

    /**
     * 重置页面
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function resetPage()
    {
        $this->themeEditor->resetPage();
    }

    /**
     * 配置方位
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function editPosition()
    {
        $this->themeEditor->editPosition();
    }

    /**
     * 重置方位
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function resetPosition()
    {
        $this->themeEditor->resetPosition();
    }

    /**
     * 编辑部件
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function editSection()
    {
        $this->themeEditor->editSection();
    }

    /**
     * 新增部件
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function addSection()
    {
        $this->themeEditor->addSection();
    }

    /**
     * 删除部件
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function deleteSection()
    {
        $this->themeEditor->deleteSection();
    }

    /**
     * 部件排序
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function sortSection()
    {
        $this->themeEditor->sortSection();
    }

    /**
     * 部件重置
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function resetSection()
    {
        $this->themeEditor->resetSection();
    }

    /**
     * 编辑部件子项
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function editSectionItem()
    {
        $this->themeEditor->editSectionItem();
    }

    /**
     * 新增部件子项
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function addSectionItem()
    {
        $this->themeEditor->addSectionItem();
    }

    /**
     * 删除子部件
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function deleteSectionItem()
    {
        $this->themeEditor->deleteSectionItem();
    }

    /**
     * 部件排序
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function sortSectionItem()
    {
        $this->themeEditor->sortSectionItem();
    }

    /**
     * 部件子项恢复默认值
     *
     * @BePermission("配置后台主题", ordering="2.33")
     */
    public function resetSectionItem()
    {
        $this->themeEditor->resetSectionItem();
    }

    /**
     * 更新 www
     *
     * @BePermission("更新www", ordering="2.34")
     */
    public function updateWww()
    {
        $this->themeEditor->updateWww();
    }

}

