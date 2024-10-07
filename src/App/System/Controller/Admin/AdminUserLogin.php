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
        
        

        if (Request::isPost()) {
            $username = Request::json('username', '');
            $password = Request::json('password', '');
            $ip = Request::getIp();
            try {
                $serviceUser = Be::getService('App.System.Admin.AdminUser');
                $serviceUser->login($username, $password, $ip);
                Resonse::success('登录成功！');
            } catch (\Exception $e) {
                Resonse::error($e->getMessage());
            }
        } else {
            $my = Be::getAdminUser();
            if ($my->id !== '') {
                Resonse::redirect(beAdminUrl('System.Home.index'));
                return;
            }

            Resonse::set('title', '登录');
            Resonse::display();
        }
    }

    /**
     * 退出登陆
     */
    public function logout()
    {
        
        try {
            Be::getService('App.System.Admin.AdminUser')->logout();

            $redirectUrl = beAdminUrl('System.AdminUserLogin.login');
            $redirect = [
                'url' => $redirectUrl,
                'message' => '{timeout} 秒后跳转到 <a href="{url}">登录页</a>',
                'timeout' => 3,
            ];

            Resonse::success('成功退出！', $redirect);
        } catch (\Exception $e) {
            Resonse::error($e->getMessage());
        }
    }


}
