<?php
namespace Be\App\System\Config;

use Be\AdminPlugin\Form\Item\FormItemInput;
use Be\AdminPlugin\Form\Item\FormItemInputNumberInt;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Form\Item\FormItemsObject;

/**
 * @BeConfig("邮件", test = "return beAdminUrl('System.Mail.test');")
 */
class Mail
{
    /**
     * @BeConfigItem("发件人邮箱", driver="FormItemInput")
     */
    public $fromMail = 'be@phpbe.com';

    /**
     * @BeConfigItem("发件人名称", driver="FormItemInput")
     */
    public $fromName = 'BE';

    /**
     * @BeConfigItem("邮件发送器",
     *     driver = "FormItemSelect",
     *     keyValues = "return ['Smtp' => 'SMTP'];")
     */
    public $driver = 'Smtp';

    public $smtp = [
        'host' => '',
        'port' => 25,
        'username' => '',
        'password' => '',
        'secure' => '0', // 0 : 不加密/ssl : SSL/tls : TLS
        'timeout' => 10,
    ];

    /**
     * @BeConfigItem("邮件队列重试次数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 1];")
     */
    public $mailQueueMaxTryTimes = 10;


    public function _smtp() {
        return [
            'label' => 'SMTP参数',
            'driver' => FormItemsObject::class,
            'ui' => [
                'form-item' => ['v-show' => 'formData.driver==\'Smtp\'']
            ],
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
                    'name' => 'secure',
                    'label' => '加密方式',
                    'driver' => FormItemSelect::class,
                    'keyValues' => [
                        '0' => '不加密', 'ssl' => 'SSL', 'tls' => 'TLS'
                    ]
                ],
                [
                    'name' => 'timeout',
                    'label' => '超时时间',
                    'driver' => FormItemInputNumberInt::class,
                ],
            ],
        ];
    }
}
