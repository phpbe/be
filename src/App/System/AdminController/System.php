<?php
namespace Be\App\System\AdminController;

use Be\Be;

/**
 * Class System
 * @package Be\App\System\Controller
 * @BePermissionGroup("*")
 */
class System
{

    /**
     * 登陆后首页
     *
     * @throws \Be\Db\DbException
     * @throws \Be\Runtime\RuntimeException
     */
    public function dashboard()
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

        $serviceApp = Be::getAdminService('System.App');
        $response->set('appCount', $serviceApp->getAppCount());

        $serviceTheme = Be::getAdminService('System.Theme');
        $response->set('themeCount', $serviceTheme->getThemeCount());

        $response->display();
    }


    /**
     * @throws \Be\Runtime\RuntimeException
     */
    public function historyBack()
    {
        $libHistory = Be::getLib('History');
        $libHistory->back();
    }

}