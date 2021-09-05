<?php

namespace Be\AdminPlugin\Grid;

use Be\Be;
use Be\AdminPlugin\Driver;

/**
 * 列表器
 *
 * Class Grid
 * @package Be\AdminPlugin\Lists
 */
class Grid extends Driver
{


    public function setting($setting = [])
    {
        $request = Be::getRequest();

        if (!isset($setting['form']['action'])) {
            $setting['form']['action'] = Be::getRequest()->getUrl();
        }

        if (!isset($setting['form']['actions'])) {
            $setting['form']['actions'] = [
                'submit' => true,
            ];
        }

        if (!isset($setting['layout'])) {
            if (isset($setting['card'])) {
                if (isset($setting['table'])) {
                    $setting['layout'] = 'toggle';
                } else {
                    $setting['layout'] = 'card';
                }
            } else {
                $setting['layout'] = 'table';
            }
        }

        if ($setting['layout'] == 'card' || ($setting['layout'] == 'toggle' && $request->get('layout', 'table') == 'card')) {

            // 设置几列
            if (isset($setting['card']['cols'])) {
                if ($setting['card']['cols'] < 12) {
                    $setting['card']['ui']['row'] = [
                        ':gutter' => $setting['card']['cols'],
                    ];

                    $setting['card']['ui']['col'] = [
                        ':span' => 1,
                    ];
                } else {
                    $setting['card']['ui']['row'] = [
                        ':gutter' => 12,
                    ];

                    $setting['card']['ui']['col'] = [
                        ':span' => 1,
                    ];
                }
            } else {

                if (!isset($setting['card']['ui']['row'])) {
                    $setting['card']['ui']['row'] = [
                        ':gutter' => 12,
                    ];
                }

                if (!isset($setting['card']['ui']['col'])) {
                    $setting['card']['ui']['col'] = [
                        ':span' => 4,
                    ];
                }
            }

            if (!isset($setting['card']['ui']['shadow'])) {
                $setting['card']['ui']['shadow'] = 'hover';
            }

            if (!isset($setting['card']['template'])) {
                $setting['card']['template'] = '';
            }

            if (isset($this->setting['card']['image'])) {
                if (!isset($this->setting['card']['image']['space'])) {
                    $this->setting['card']['image']['space'] = '15';
                }

                if (!isset($this->setting['card']['image']['position'])) {
                    $this->setting['card']['image']['position'] = 'left';
                }

                if (!isset($this->setting['card']['image']['maxWidth'])) {
                    $this->setting['card']['image']['maxWidth'] = '400';
                }

                if (!isset($this->setting['card']['image']['maxHeight'])) {
                    $this->setting['card']['image']['maxHeight'] = '300';
                }
            }
        }

        return parent::setting($setting);
    }


    public function display()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $pageSize = null;
        if (isset($this->setting['pageSize']) &&
            is_numeric($this->setting['pageSize']) &&
            $this->setting['pageSize'] > 0
        ) {
            $pageSize = $this->setting['pageSize'];
        } else {
            $pageSize = Be::getConfig('App.System.Admin')->pageSize;;
        }

        $response->set('title', $this->setting['title'] ?? '');
        $response->set('url', $request->getUrl());
        $response->set('setting', $this->setting);
        $response->set('pageSize', $pageSize);

        $theme = null;
        if (isset($this->setting['theme'])) {
            $theme = $this->setting['theme'];
        }
        $response->display('AdminPlugin.Grid.display', $theme);
        $response->createHistory();
    }


}

