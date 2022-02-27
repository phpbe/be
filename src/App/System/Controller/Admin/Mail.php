<?php

namespace Be\App\System\Controller\Admin;

use Be\AdminPlugin\Form\Item\FormItemInputTextArea;
use Be\AdminPlugin\Form\Item\FormItemTinymce;
use Be\Be;

/**
 * @BeMenuGroup("管理")
 * @BePermissionGroup("管理")
 */
class Mail extends Auth
{

    /**
     * @BePermission("发送邮件测试", ordering="2.71")
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
                'title' => '发送邮件测试',
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
                            'driver' => FormItemTinymce::class,
                            //'layout' => 'basic',
                            'value' => '这是一封测试邮件。',
                        ],
                    ],
                ],
                'theme' => 'Admin',
            ])->execute();
        }
    }


}