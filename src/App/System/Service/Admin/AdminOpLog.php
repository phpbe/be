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
        $request = Be::getRequest();
        $my = Be::getAdminUser();
        $tupleAdminLog = Be::newTuple('system_admin_op_log');
        $tupleAdminLog->admin_user_id = $my->id;
        $tupleAdminLog->app = $request->getAppName();
        $tupleAdminLog->controller = $request->getControllerName();
        $tupleAdminLog->action = $request->getActionName();
        $tupleAdminLog->content = $content;
        $tupleAdminLog->details = json_encode($details);
        $tupleAdminLog->ip = $request->getIp();
        $tupleAdminLog->create_time = date('Y-m-d H:i:s');
        $tupleAdminLog->save();
    }

}
