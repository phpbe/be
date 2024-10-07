<?php
namespace Be\App\System\Controller\Admin;

use Be\App\ControllerException;
use Be\Be;

/**
 * Class Home
 * @package Be\App\System\Controller
 */
class Home
{

    /**
     * 控制台
     *
     * @BeMenu("首页", icon="bi-house-door", ordering="0")
     */
    public function index()
    {
        
        ;

        $my = Be::getAdminUser();
        if ($my->id === '') {
            Resonse::redirect(beAdminUrl('System.AdminUserLogin.login'));
            return;
        }

        Resonse::set('title', '后台首页');

        $tupleAdminUser = Be::getTuple('system_admin_user');
        try {
            $tupleAdminUser->load($my->id);
        } catch (\Throwable $t) {
            Resonse::redirect(beAdminUrl('System.AdminUserLogin.logout'));
            return;
        }

        unset($tupleAdminUser->password, $tupleAdminUser->salt, $tupleAdminUser->remember_me_token);
        Resonse::set('adminUser', $tupleAdminUser);

        $tableAdminUser = Be::getTable('system_admin_user');
        $adminUserCount = $tableAdminUser->count();
        Resonse::set('adminUserCount', $adminUserCount);

        $serviceApp = Be::getService('App.System.Admin.App');
        Resonse::set('appCount', $serviceApp->getAppCount());

        $serviceTheme = Be::getService('App.System.Admin.Theme');
        Resonse::set('themeCount', $serviceTheme->getThemeCount());

        $serviceAdminTheme = Be::getService('App.System.Admin.AdminTheme');
        Resonse::set('adminThemeCount', $serviceAdminTheme->getThemeCount());

        $recentLogs = Be::getTable('system_admin_op_log')
            ->where('admin_user_id', $my->id)
            ->orderBy('create_time', 'DESC')
            ->limit(5)
            ->getObjects();
        Resonse::set('recentLogs', $recentLogs);

        $recentLoginLogs = Be::getTable('system_admin_user_login_log')
            ->where('username', $my->username)
            ->orderBy('create_time', 'DESC')
            ->limit(5)
            ->getObjects();
        Resonse::set('recentLoginLogs', $recentLoginLogs);

        Resonse::display();
    }


}