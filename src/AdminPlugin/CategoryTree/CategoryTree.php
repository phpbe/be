<?php

namespace Be\AdminPlugin\CategoryTree;

use Be\Db\Table;
use Be\Be;
use Be\AdminPlugin\Detail\Item\DetailItemAvatar;
use Be\AdminPlugin\Detail\Item\DetailItemCustom;
use Be\AdminPlugin\Detail\Item\DetailItemImage;
use Be\AdminPlugin\Detail\Item\DetailItemProgress;
use Be\AdminPlugin\Detail\Item\DetailItemSwitch;
use Be\AdminPlugin\Detail\Item\DetailItemText;
use Be\AdminPlugin\Form\Item\FormItemDatePickerMonthRange;
use Be\AdminPlugin\Form\Item\FormItemDatePickerRange;
use Be\AdminPlugin\Form\Item\FormItemHidden;
use Be\AdminPlugin\Form\Item\FormItemInput;
use Be\AdminPlugin\Form\Item\FormItemTimePickerRange;
use Be\AdminPlugin\Table\Item\TableItemAvatar;
use Be\AdminPlugin\Table\Item\TableItemCustom;
use Be\AdminPlugin\Table\Item\TableItemImage;
use Be\AdminPlugin\Table\Item\TableItemProgress;
use Be\AdminPlugin\Table\Item\TableItemSwitch;
use Be\AdminPlugin\AdminPluginException;
use Be\AdminPlugin\Driver;

/**
 * 增删改查
 *
 * Class CategoryTree
 * @package Be\Mf\Plugin
 */
class CategoryTree extends Driver
{

    /**
     * 配置项
     *
     * @param array $setting
     * @return Driver
     */
    public function setting(array $setting = []): Driver
    {
        if (!isset($setting['db'])) {
            $setting['db'] = 'master';
        }

        $this->setting = $setting;
        return $this;
    }

    /**
     * 执行指定任务
     *
     * @param string $task
     */
    public function execute($task = null)
    {
        if ($task === null) {
            $task = Be::getRequest()->get('task', 'display');
        }

        if (method_exists($this, $task)) {
            $this->$task();
        }
    }

    /**
     * 列表展示
     *
     */
    public function display()
    {
        
        


        $theme = null;
        if (isset($this->setting['theme'])) {
            $theme = $this->setting['theme'];
        }
        Resonse::display('AdminPlugin.CategoryTree.display', $theme);
        Resonse::createHistory();

    }

    public function save() {

    }

    /**
     * 删除
     *
     */
    public function delete()
    {
    }


}

