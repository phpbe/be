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

        if ($setting['layout'] == 'toggle') {
            if (!isset($setting['defaultLayout'])) {
                $setting['defaultLayout'] = 'card';
            }

            $setting['actualLayout'] = $request->get('layout', $setting['defaultLayout']);
        } else {
            $setting['actualLayout'] = $setting['layout'];
        }

        if ($setting['actualLayout'] == 'card') {
            if (!isset($setting['card']['ui']['row'])) {
                $setting['card']['ui']['row'] = [
                    ':gutter' => 20,
                ];
            }

            // 设置一行展示几列
            if (isset($setting['card']['cols'])) {
                $setting['card']['ui']['col'] = [
                    ':span' => (int) 24 / $setting['card']['cols'],
                ];
            } else {
                if (!isset($setting['card']['ui']['col'])) {
                    $setting['card']['ui']['col'] = [
                        ':span' => 8,
                    ];
                }
            }

            if (!isset($setting['card']['ui']['shadow'])) {
                $setting['card']['ui']['shadow'] = 'hover';
            }

            if (isset($setting['card']['image'])) {

                if (!isset($setting['card']['image']['position'])) {
                    $setting['card']['image']['position'] = 'left';
                }

                if ($setting['card']['image']['position'] == 'left')
                {
                    if (!isset($setting['card']['image']['space'])) {
                        $setting['card']['image']['space'] = '20';
                    }

                    if (!isset($setting['card']['image']['maxWidth'])) {
                        $setting['card']['image']['maxWidth'] = '400';
                    }

                    if (!isset($setting['card']['image']['maxHeight'])) {
                        $setting['card']['image']['maxHeight'] = '300';
                    }
                } else {
                    if (!isset($setting['card']['image']['space'])) {
                        $setting['card']['image']['space'] = '10';
                    }
                }
            }

            if (isset($setting['card']['operation'])) {
                if (!isset($setting['card']['operation']['position'])) {
                    $setting['card']['operation']['position'] = 'right';
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

