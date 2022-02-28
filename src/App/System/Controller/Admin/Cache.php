<?php

namespace Be\App\System\Controller\Admin;

use Be\AdminPlugin\Table\Item\TableItemIcon;
use Be\AdminPlugin\Table\Item\TableItemTag;
use Be\Be;


/**
 * @BeMenuGroup("控制台")
 * @BePermissionGroup("控制台")
 */
class Cache extends Auth
{

    /**
     * @BeMenu("缓存", icon = "el-icon-fa fa-database", ordering="2.6")
     * @BePermission("缓存列表", ordering="2.6")
     */
    public function index()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        if ($request->isAjax()) {
            $service = Be::getService('App.System.Admin.Cache');
            $gridData = $service->getCategories();
            $response->set('success', true);
            $response->set('data', [
                'total' => 0,
                'gridData' => $gridData,
            ]);
            $response->json();
        } else {
            Be::getAdminPlugin('Grid')->setting([
                'title' => '缓存',
                'pageSize' => 10,
                'toolbar' => [
                    'items' => [
                        [
                            'label' => '清除所有缓存',
                            'action' => 'delete',
                            'confirm' => '确认要清除所有缓存么？',
                            'target' => 'ajax',
                            'ui' => [
                                'icon' => 'el-icon-delete',
                                'type' => 'danger'
                            ]
                        ],
                    ],
                ],

                'table' => [
                    'items' => [
                        [
                            'name' => 'icon',
                            'label' => '',
                            'driver' => TableItemIcon::class,
                            'width' => '60',
                        ],
                        [
                            'name' => 'name',
                            'label' => '缓存类型',
                            'driver' => TableItemTag::class,
                            'ui' => [
                                'type' => 'success',
                                ],
                            'width' => '120',
                            'align' => 'left',
                        ],
                        [
                            'name' => 'label',
                            'label' => '缓存名称',
                            'width' => '120',
                            'align' => 'left',
                        ],
                        [
                            'name' => 'description',
                            'label' => '描述',
                            'align' => 'left',
                        ],
                        [
                            'name' => 'count',
                            'label' => '文件数',
                            'width' => '120',
                        ],
                        [
                            'name' => 'sizeStr',
                            'label' => '空间占用',
                            'width' => '120',
                        ],
                    ],
                    'ui' => [
                        'show-summary' => null,
                        ':summary-method' => 'getSummaries',
                    ],
                    'operation' => [
                        'label' => '操作',
                        'width' => '120',
                        'items' => [
                            [
                                'label' => '清除',
                                'action' => 'delete',
                                'target' => 'ajax',
                                'confirm' => '确认要清除缓存么？',
                                'ui' => [
                                    'type' => 'danger'
                                ]
                            ],
                        ]
                    ],
                ],


                'vueMethods' => [
                    'getSummaries' => 'function(param) {
                        var summaries = [];
                        param.columns.forEach(function(column, index) {
                            if (index === 0) {
                                summaries[index] = "总计";
                                return;
                            }
                            
                            var total;
                            if (column.property === "count") {
                                total = 0;
                                param.data.forEach(function(x){
                                    total += Number(x.count);
                                })
                                summaries[index] = total;
                            } else if (column.property === "sizeStr") {
                                total = 0;
                                param.data.forEach(function(x){
                                    total += Number(x.size);
                                })

                                if (total < 1024) {
                                    total = total + " B";
                                } else if (total < (1024*1024)) {
                                    total = total / 1024;
                                    total = total.toFixed(2) + " KB";
                                } else if (total < (1024*1024*1024)) {
                                    total = total / (1024*1024);
                                    total = total.toFixed(2) + " MB";
                                } else {
                                    total = total / (1024*1024*1024);
                                    total = total.toFixed(2) + " GB";
                                }
                                
                                summaries[index] = total;
                            } else {
                                summaries[index] = "";
                            }
                        });
                        return summaries;
                    }',
                ],
            ])->execute();
        }
    }

    /**
     * @BePermission("删除缓存", ordering="2.61")
     */
    public function delete()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        try {
            $postData = $request->json();
            $name = $postData['row']['name'] ?? null;
            $serviceSystemCache = Be::getService('App.System.Admin.Cache');
            $serviceSystemCache->delete($name);
            beAdminOpLog($name ? ('清除缓存（' . $name. '）') : '清除所有缓存' );
            $response->success('清除缓存成功！');
        } catch (\Exception $e) {
            $response->error($e->getMessage());
        }
    }


}