<?php

namespace Be\App\System\Controller\Admin;

use Be\AdminPlugin\Form\Item\FormItemRadioGroupButton;
use Be\AdminPlugin\Table\Item\TableItemLink;
use Be\Be;

/**
 * @BeMenuGroup("日志")
 * @BePermissionGroup("系统日志")
 */
class Log extends Auth
{

    /**
     * 运行日志
     *
     * @BeMenu("系统日志", icon="bi-file-earmark-excel", ordering="4.2")
     * @BePermission("列表", ordering="4.2")
     */
    public function lists()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        //$a = 1/0;
        //Be::getTable('op_log');

        $serviceSystemLog = Be::getService('App.System.Admin.Log');
        if ($request->isAjax()) {
            $postData = $request->json();
            $formData = $postData['formData'];

            $year = $formData['year'];
            $month = $formData['month'];
            $day = $formData['day'];

            $total = $serviceSystemLog->getlogCount($year, $month, $day);

            $page = $postData['page'] ?? 1;
            $pageSize = $postData['pageSize'] ?? 10;

            $offset = ($page - 1) * $pageSize;
            if ($offset > $total) $offset = $total;

            $gridData = $serviceSystemLog->getlogs($year, $month, $day, $offset, $pageSize);

            $response->set('success', true);
            $response->set('data', [
                'total' => $total,
                'gridData' => $gridData,
            ]);
            $response->json();

        } else {

            $years = $serviceSystemLog->getYears();

            $year = date('Y');
            $month = date('m');
            $day = date('d');

            if ($request->isPost()) {
                $postData = $request->post('data', '', '');
                $postData = json_decode($postData, true);
                $formData = $postData['formData'];

                $year = $formData['year'];
                $month = $formData['month'];
                $day = $formData['day'];
            }

            $months = $serviceSystemLog->getMonths($year);
            $days = $serviceSystemLog->getDays($year, $month);

            if (!in_array($day, $days) && count($days) > 0) {
                $day = $days[0];
            }

            if (!$years) {
                $years = [$year];
            }

            if (!$months) {
                $months = [$month];
            }

            if (!$days) {
                $days = [$day];
            }

            Be::getAdminPlugin('Grid')->setting([
                'title' => '系统日志',
                'pageSize' => 10,
                'form' => [
                    'items' => [
                        [
                            'name' => 'year',
                            'label' => '年份',
                            'driver' => FormItemRadioGroupButton::class,
                            'values' => $years,
                            'value' => $year,
                            'ui' => [
                                'form-item' => [
                                    'style' => 'display:block',
                                ],
                                '@change' => 'showLogs',
                            ],
                        ],
                        [
                            'name' => 'month',
                            'label' => '月份',
                            'driver' => FormItemRadioGroupButton::class,
                            'values' => $months,
                            'value' => $month,
                            'ui' => [
                                'form-item' => [
                                    'style' => 'display:block',
                                ],
                                '@change' => 'showLogs',
                            ],
                        ],
                        [
                            'name' => 'day',
                            'label' => '日期',
                            'driver' => FormItemRadioGroupButton::class,
                            'values' => $days,
                            'value' => $day,
                            'ui' => [
                                'form-item' => [
                                    'style' => 'display:block',
                                ],
                                '@change' => 'showLogs',
                            ],
                        ],
                    ],
                    'actions' => [
                        'submit' => false,
                    ]

                ],
                'toolbar' => [
                    'items' => [
                        [
                            'label' => '删除本日（' . $year . '-' . $month . '-' . $day . '）全部日志',
                            'url' => beAdminUrl('System.Log.delete', ['range' => 'day']),
                            'confirm' => '确认要删除本日（' . $year . '-' . $month . '-' . $day . '）全部日志么？',
                            'target' => 'ajax',
                            'ui' => [
                                'type' => 'warning'
                            ]
                        ],
                        [
                            'label' => '删除本月（' . $year . '-' . $month . '）全部日志',
                            'url' => beAdminUrl('System.Log.delete', ['range' => 'month']),
                            'confirm' => '确认要删除本月（' . $year . '-' . $month . '）全部日志么？',
                            'target' => 'ajax',
                            'ui' => [
                                'type' => 'danger'
                            ]
                        ],
                        [
                            'label' => '删除本年（' . $year . '）全部日志',
                            'url' => beAdminUrl('System.Log.delete', ['range' => 'year']),
                            'confirm' => '确认要删除本年（' . $year . '）全部日志么？',
                            'target' => 'ajax',
                            'ui' => [
                                'type' => 'danger'
                            ]
                        ],
                        [
                            'label' => '刷新',
                            'ui' => [
                                'type' => 'primary',
                                'icon' => 'el-icon-refresh',
                                '@click' => 'submit'
                            ]
                        ],
                    ],
                ],

                'table' => [
                    'items' => [
                        [
                            'name' => 'id',
                            'label' => '编号',
                            'width' => '300',
                            'driver' => TableItemLink::class,
                            'url' => beAdminUrl('System.Log.detail'),
                            'target' => 'drawer',
                            'drawer' => [
                                'width' => '75%',
                            ],
                        ],
                        [
                            'name' => 'file',
                            'label' => '文件',
                            'align' => 'left',
                        ],
                        [
                            'name' => 'line',
                            'label' => '行号',
                            'width' => '90',
                        ],
                        [
                            'name' => 'code',
                            'label' => '错误码',
                            'width' => '90',
                        ],
                        [
                            'name' => 'message',
                            'label' => '日志信息',
                            'align' => 'left',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '首次产生时间',
                            'width' => '180',
                        ],
                        [
                            'name' => 'update_time',
                            'label' => '产生时间',
                            'width' => '180',
                        ],
                    ],
                ],

                'vueMethods' => [
                    'showLogs' => 'function() {
                        this.formAction("", {url:"' . beAdminUrl('System.Log.lists') . '", target:"self", postData: []});
                    }',
                ],
            ])->execute();
        }
    }

    /**
     * 查看系统日志
     *
     * @BePermission("明细", ordering="4.21")
     */
    public function detail()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $data = $request->post('data', '', '');
        $data = json_decode($data, true);
        try {
            $servicebeAdminOpLog = Be::getService('App.System.Admin.Log');
            $log = $servicebeAdminOpLog->getlog($data['row']['year'], $data['row']['month'], $data['row']['day'], $data['row']['id']);
            $response->set('title', '系统日志明细');
            $response->set('log', $log);
            $response->display(null, 'Blank');
        } catch (\Exception $e) {
            $response->error($e->getMessage());
        }
    }

    /**
     * 删除系统日志
     *
     * @BePermission("删除", ordering="4.22")
     */
    public function delete()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $range = $request->get('range', 'day');

        $postData = $request->json();
        $formData = $postData['formData'];
        $year = $formData['year'];
        $month = $formData['month'];
        $day = $formData['day'];

        try {
            $servicebeAdminOpLog = Be::getService('App.System.Admin.Log');
            $servicebeAdminOpLog->deleteLogs($range, $year, $month, $day);
            $response->success('删除日志成功！');
        } catch (\Exception $e) {
            $response->error($e->getMessage());
        }
    }

}