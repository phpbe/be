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

    private $app;
    private $route;

    /**
     * 配置项
     *
     * @param array $setting
     * @return Driver
     */
    public function setting($setting = [])
    {
        $this->app = $this->setting['app'];
        $this->route = $this->setting['route'];
        $annotation = $this->setting['annotation'];
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
            $response->set('title', $setting['title'] ?? '');
            $response->set('app', $this->app);
            $response->set('route', $this->route);
            $response->set('url', $request->getUrl());

            $setting = $this->setting['grid'];
            if (!isset($setting['form']['action'])) {
                $setting['form']['action'] = Be::getRequest()->getUrl();
            }

            if (!isset($setting['form']['actions'])) {
                $setting['form']['actions'] = [
                    'submit' => true,
                ];
            }
            $response->set('setting', $setting);

            $pageSize = null;
            if (isset($setting['pageSize']) &&
                is_numeric($setting['pageSize']) &&
                $setting['pageSize'] > 0
            ) {
                $pageSize = $setting['pageSize'];
            } else {
                $pageSize = Be::getConfig('App.System.Admin')->pageSize;;
            }
            $response->set('pageSize', $pageSize);

            $theme = $setting['theme'] ?? 'Blank';
            $response->display('AdminPlugin.MenuPicker.display', $theme);
        }

    }

}

