<?php
namespace Be\App\System\Controller\Admin;

use Be\App\ControllerException;
use Be\Be;

/**
 * 身份识别
 *
 * Class Auth
 * @package Be\App\System\Controller\Admin
 */
class Auth
{

    public function __construct()
    {
        $request = Be::getRequest();

        $appName = $request->getAppName();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();

        $my = Be::getAdminUser();
        if ($my->id == 0) {
            Be::getService('App.System.Admin.AdminUser')->rememberMe();
            $my = Be::getAdminUser();
        }

        // 校验权限
        if ($my->id == 0) {
            $redirectUrl = beAdminUrl('System.AdminUserLogin.login', ['return' => base64_encode($request->getUrl())]);
            $redirect = [
                'url' => $redirectUrl,
                'message' => '{timeout} 秒后跳转到 <a href="{url}">登录页</a>',
                'timeout' => 3,
            ];
            throw new ControllerException('登录超时，请重新登录！', 0, $redirect);
        } else {
            if (!$my->hasPermission($appName, $controllerName, $actionName)) {
                throw new ControllerException('您没有权限操作该功能！');
            }

            // 已登录用户，IP锁定功能校验
            $configAdminUser = Be::getConfig('App.System.AdminUser');
            if ($configAdminUser->ipLock) {
                if ($my->this_login_ip != $request->getIp()) {
                    Be::getService('App.System.Admin.AdminUser')->logout();
                    $redirectUrl = beAdminUrl('System.AdminUserLogin.login');
                    $redirect = [
                        'url' => $redirectUrl,
                        'message' => '{timeout} 秒后跳转到 <a href="{url}">登录页</a>',
                        'timeout' => 3,
                    ];
                    throw new ControllerException('检测到您的账号在其它地点（' . $my->this_login_ip . ' ' . $my->this_login_time . '）登录！', 0, $redirect);
                }
            }
        }
    }

}

