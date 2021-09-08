<?php
namespace Be\App\System\Controller\Admin;

use Be\Be;

/**
 * Class Index
 * @package Be\App\System\Controller
 * @BePermissionGroup("*")
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
        if ($my->id == 0) {
            $response->redirect(beAdminUrl('System.AdminUser.login'));
            return;
        }

        $response->set('title', '后台首页');

        $tupleAdminUser = Be::newTuple('system_admin_user');
        $tupleAdminUser->load($my->id);
        unset($tupleAdminUser->password, $tupleAdminUser->salt, $tupleAdminUser->remember_me_token);
        $response->set('adminUser', $tupleAdminUser);

        $tableAdminUser = Be::getTable('system_admin_user');
        $adminUserCount = $tableAdminUser->count();
        $response->set('adminUserCount', $adminUserCount);

        $serviceApp = Be::getService('App.System.Admin.App');
        $response->set('appCount', $serviceApp->getAppCount());

        $serviceTheme = Be::getService('App.System.Admin.Theme');
        $response->set('themeCount', $serviceTheme->getAvailableThemeCount());

        $serviceAdminTheme = Be::getService('App.System.Admin.AdminTheme');
        $response->set('adminThemeCount', $serviceAdminTheme->getAvailableThemeCount());

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