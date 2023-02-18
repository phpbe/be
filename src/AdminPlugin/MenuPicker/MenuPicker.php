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

    protected string $app;
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
        $request = Be::getRequest();
        $response = Be::getResponse();

        if ($request->isAjax()) {
            parent::Grid();
        } else {

            $response->set('title', $this->setting['grid']['title'] ?? '');
            $response->set('app', $this->app);
            $response->set('route', $this->route);
            $response->set('url', $request->getUrl());

            if (!isset($this->setting['grid']['form']['action'])) {
                $this->setting['grid']['form']['action'] = $request->getUrl();
            }

            if (!isset($this->setting['grid']['form']['actions'])) {
                $this->setting['grid']['form']['actions'] = [
                    'submit' => true,
                ];
            }

            if (!isset($this->setting['field'])) {
                $this->setting['field'] = $this->setting['name'];
            }

            $response->set('setting', $this->setting);

            $pageSize = null;
            if (isset($this->setting['grid']['pageSize']) &&
                is_numeric($this->setting['grid']['pageSize']) &&
                $this->setting['grid']['pageSize'] > 0
            ) {
                $pageSize = $this->setting['grid']['pageSize'];
            } else {
                $pageSize = Be::getConfig('App.System.Admin')->pageSize;;
            }
            $response->set('pageSize', $pageSize);

            $theme = $this->setting['grid']['theme'] ?? 'Blank';
            $response->display('AdminPlugin.MenuPicker.display', $theme);
        }

    }

}

