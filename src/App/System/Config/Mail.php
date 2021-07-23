<?php
namespace Be\App\System\Config;

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

    /**
     * @BeConfigItem("SMTP参数",
     *     driver="FormItemCode",
     *     language="json",
     *     ui="return ['form-item' => ['v-show' => 'formData.driver==\'Smtp\'']];")
     */
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

}
