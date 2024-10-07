<?php

namespace Be\AdminPlugin\MenuPicker;

use Be\AdminPlugin\Curd\Curd;
use Be\AdminPlugin\Driver;
use Be\Be;

/**
 * 菜单选择器
 *
 * Class MenuPicker
 * @package Be\AdminPlugin\MenuPicker
 */
class MenuPicker extends Curd
{

    protected \Be\App\Property $app;
    protected string $route;

    /**
     * 配置项
     *
     * @param array $setting
     * @return Driver
     */
    public function setting(array $setting = []): Driver
    {
        $this->app = $setting['app'];
        $this->route = $setting['route'];
        $annotation = $setting['annotation'];
        parent::setting($annotation->picker);
        return $this;
    }

    /**
     * 列表展示
     */
    public function Grid()
    {
        
        

        if (Request::isAjax()) {
            parent::Grid();
        } else {

            Resonse::set('title', $this->setting['grid']['title'] ?? '');
            Resonse::set('app', $this->app);
            Resonse::set('route', $this->route);
            Resonse::set('url', Request::getUrl());

            if (!isset($this->setting['grid']['form']['action'])) {
                $this->setting['grid']['form']['action'] = Request::getUrl();
            }

            if (!isset($this->setting['grid']['form']['actions'])) {
                $this->setting['grid']['form']['actions'] = [
                    'submit' => true,
                ];
            }

            if (!isset($this->setting['field'])) {
                $this->setting['field'] = $this->setting['name'];
            }

            Resonse::set('setting', $this->setting);

            $pageSize = null;
            if (isset($this->setting['grid']['pageSize']) &&
                is_numeric($this->setting['grid']['pageSize']) &&
                $this->setting['grid']['pageSize'] > 0
            ) {
                $pageSize = $this->setting['grid']['pageSize'];
            } else {
                $pageSize = Be::getConfig('App.System.Admin')->pageSize;;
            }
            Resonse::set('pageSize', $pageSize);

            $theme = $this->setting['grid']['theme'] ?? 'Blank';
            Resonse::display('AdminPlugin.MenuPicker.display', $theme);
        }

    }

}

