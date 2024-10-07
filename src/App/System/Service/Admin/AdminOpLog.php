<?php
namespace Be\App\System\Service\Admin;

use Be\Be;
use Be\Runtime\RuntimeException;

class AdminOpLog
{
    /**
     * @param $content
     * @param string $details
     * @throws RuntimeException
     */
    public function addLog($content, $details = '')
    {
        
        $my = Be::getAdminUser();
        $tupleAdminLog = Be::getTuple('system_admin_op_log');
        $tupleAdminLog->admin_user_id = $my->id;
        $tupleAdminLog->app = Request::getAppName();
        $tupleAdminLog->controller = Request::getControllerName();
        $tupleAdminLog->action = Request::getActionName();
        $tupleAdminLog->content = $content;
        $tupleAdminLog->details = json_encode($details);
        $tupleAdminLog->ip = Request::getIp();
        $tupleAdminLog->create_time = date('Y-m-d H:i:s');
        $tupleAdminLog->save();
    }

}
