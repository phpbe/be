<?php
namespace Be\Db;

use Be\AdminPlugin\Form\Item\FormItemInput;
use Be\AdminPlugin\Form\Item\FormItemInputNumberInt;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Form\Item\FormItemsObject;

class Config
{

    public $master = [
        'driver' => 'mysql',
        'host' => '127.0.0.1', // 主机名
        'port' => 3306, // 端口号
        'username' => 'root', // 用户名
        'password' => 'root', // 密码
        'name' => 'be', // 数据库名称
        'charset' => 'UTF8', // 字符集
        'pool' => 0, // 连接池，<=0 时不启用
    ]; // 主数据库

    public function _master() {
        return [
            'label' => '主库',
            'driver' => FormItemsObject::class,
            'items' => [
                [
                    'name' => 'driver',
                    'label' => '驱动',
                    'driver' => FormItemSelect::class,
                    'keyValues' => ['mysql' => 'Mysql', 'mssql' => 'MSSQL', 'oracel' => 'Oracle'],
                ],
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
                    'name' => 'username',
                    'label' => '用户名',
                    'driver' => FormItemInput::class,
                ],
                [
                    'name' => 'password',
                    'label' => '密码',
                    'driver' => FormItemInput::class,
                ],
                [
                    'name' => 'name',
                    'label' => '数据库名称',
                    'driver' => FormItemInput::class,
                ],
                [
                    'name' => 'charset',
                    'label' => '字符集',
                    'driver' => FormItemSelect::class,
                    'values' => ['UTF8', 'GBK'],
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
