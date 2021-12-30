<?php

namespace Be\App\System\Controller\Admin;

use Be\Be;

/**
 * Class AdminUserLogin
 * @package App\System\Controller
 */
class AdminUserLogin
{
    /**
     * 登陆页面
     */
    public function login()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if ($request->isPost()) {
            $username = $request->json('username', '');
            $password = $request->json('password', '');
            $ip = $request->getIp();
            try {
                $serviceUser = Be::getService('App.System.Admin.AdminUser');
                $serviceUser->login($username, $password, $ip);
                $response->success('登录成功！');
            } catch (\Exception $e) {
                $response->error($e->getMessage());
            }
        } else {
            $my = Be::getAdminUser();
            if ($my->id > 0) {
                $response->redirect(beAdminUrl('System.Index.index'));
                return;
            }

            $response->set('title', '登录');
            $response->display();
        }
    }

    /**
     * 退出登陆
     */
    public function logout()
    {
        $response = Be::getResponse();
        try {
            Be::getService('App.System.Admin.AdminUser')->logout();

            $redirectUrl = beAdminUrl('System.AdminUserLogin.login');
            $redirect = [
                'url' => $redirectUrl,
                'message' => '{timeout} 秒后跳转到 <a href="{url}">登录页</a>',
                'timeout' => 3,
            ];

            $response->success('成功退出！', $redirect);
        } catch (\Exception $e) {
            $response->error($e->getMessage());
        }
    }


}
