<?php
namespace Be\Redis;


use Be\AdminPlugin\Form\Item\FormItemInput;
use Be\AdminPlugin\Form\Item\FormItemInputNumberInt;
use Be\AdminPlugin\Form\Item\FormItemsObject;

class Config
{

    public $master = [
        'host' => '127.0.0.1', // 主机名
        'port' => 6379, // 端口号
        'timeout' => 5, // 超时时间
        'auth' => '', // 密码，不需要时留空
        'db' => 0, // 默认选中的数据库
        'pool' => 0, // 连接池，<=0 时不启用
    ];

    public function _master() {
        return [
            'label' => '主库',
            'driver' => FormItemsObject::class,
            'items' => [
                [
                    'name' => 'host',
                    'label' => '主机名',
                    'driver' => FormItemInput::class,
                ],
                [
                    'name' => 'port',
                    'label' => '端口号',
                    'driver' => FormItemInputNumberInt::class,
                ],
                [
                    'name' => 'timeout',
                    'label' => '超时时间',
                    'driver' => FormItemInputNumberInt::class,
                ],
                [
                    'name' => 'auth',
                    'label' => '密码，不需要时留空',
                    'driver' => FormItemInput::class,
                ],
                [
                    'name' => 'db',
                    'label' => '默认选中的数据库',
                    'driver' => FormItemInputNumberInt::class,
                ],
                [
                    'name' => 'pool',
                    'label' => '连接池',
                    'driver' => FormItemInputNumberInt::class,
                    'description' => '<=0 时不启用，Swoole模式下有效',
                ],
            ],
        ];
    }

}
