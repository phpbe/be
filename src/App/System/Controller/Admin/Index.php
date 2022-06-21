<?php
namespace Be\App\System\Controller\Admin;

use Be\App\ControllerException;
use Be\Be;

/**
 * Class Index
 * @package Be\App\System\Controller
 */
class Index
{

    /**
     * 控制台
     *
     * @throws \Be\Db\DbException
     * @throws \Be\Runtime\RuntimeException
     */
    public function index()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $my = Be::getAdminUser();
        if ($my->id === '') {
            $response->redirect(beAdminUrl('System.AdminUserLogin.login'));
            return;
        }

        $response->set('title', '后台首页');

        $tupleAdminUser = Be::getTuple('system_admin_user');
        try {
            $tupleAdminUser->load($my->id);
        } catch (\Throwable $t) {
            $response->redirect(beAdminUrl('System.AdminUserLogin.logout'));
            return;
        }
        
        unset($tupleAdminUser->password, $tupleAdminUser->salt, $tupleAdminUser->remember_me_token);
        $response->set('adminUser', $tupleAdminUser);

        $tableAdminUser = Be::getTable('system_admin_user');
        $adminUserCount = $tableAdminUser->count();
        $response->set('adminUserCount', $adminUserCount);

        $serviceApp = Be::getService('App.System.Admin.App');
        $response->set('appCount', $serviceApp->getAppCount());

        $serviceTheme = Be::getService('App.System.Admin.Theme');
        $response->set('themeCount', $serviceTheme->getThemeCount());

        $serviceAdminTheme = Be::getService('App.System.Admin.AdminTheme');
        $response->set('adminThemeCount', $serviceAdminTheme->getThemeCount());

        $recentLogs = Be::getTable('system_admin_op_log')
            ->where('admin_user_id', $my->id)
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->getObjects();
        $response->set('recentLogs', $recentLogs);

        $recentLoginLogs = Be::getTable('system_admin_user_login_log')
            ->where('username', $my->username)
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->getObjects();
        $response->set('recentLoginLogs', $recentLoginLogs);

        $response->display();
    }


}