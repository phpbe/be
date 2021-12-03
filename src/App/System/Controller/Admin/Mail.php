<?php

namespace Be\App\System\Controller\Admin;

use Be\AdminPlugin\Form\Item\FormItemInputTextArea;
use Be\Be;

/**
 * @BeMenuGroup("系统配置")
 * @BePermissionGroup("系统配置")
 */
class Mail extends Auth
{

    /**
     * @BeMenu("发送邮件测试", icon="el-icon-fa fa-envelope-o", ordering="3.2")
     * @BePermission("发送邮件测试", ordering="3.2")
     */
    public function test()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if ($request->isAjax()) {

            $toEmail = $request->json('formData.toEmail', '');
            $subject = $request->json('formData.subject', '');
            $body = $request->json('formData.body', '', 'html');

            $redirectUrl = beAdminUrl('System.Mail.test', ['toEmail' => $toEmail]);
            $redirect = [
                'url' => $redirectUrl,
                'message' => '{timeout} 秒后跳转到 {link}',
                'timeout' => 3,
            ];

            try {
                Be::getService('App.System.Mail')->send($toEmail, $subject, $body);
                beAdminOpLog('发送测试邮件到 ' . $toEmail . ' -成功',  $request->json('formData'));
                $response->success('发送邮件成功！', $redirect);
            } catch (\Exception $e) {
                beAdminOpLog('发送测试邮件到 ' . $toEmail . ' -失败：' . $e->getMessage());
                $response->error('发送邮件失败：' . $e->getMessage(), $redirect);
            }

        } else {
            Be::getAdminPlugin('Form')->setting([
                'form' => [
                    'items' => [
                        [
                            'name' => 'toEmail',
                            'label' => '收件邮箱',
                            'required' => true,
                        ],
                        [
                            'name' => 'subject',
                            'label' => '标题',
                            'value' => '系统邮件测试',
                            'required' => true,
                        ],
                        [
                            'name' => 'body',
                            'label' => '内容',
                            'driver' => FormItemInputTextArea::class,
                            'value' => '这是一封测试邮件。',
                        ],
                    ],
                    'ui' => [
                        'style' => 'max-width: 800px;'
                    ]
                ],
                'theme' => 'Admin',
            ])->execute();
        }
    }


}