<?php

namespace Be\App\System\Controller\Admin;


use Be\Be;

/**
 * @BeMenuGroup("控制台")
 * @BePermissionGroup("控制台")
 */
class Server extends Auth
{

    /**
     * @BeMenu("服务器状态", icon="el-icon-info", ordering="2.8")
     * @BePermission("服务器状态", ordering="2.8")
     */
    public function stats()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        $runtime = Be::getRuntime();

        $isSwooleMode = $runtime->isSwooleMode();
        $response->set('isSwooleMode', $isSwooleMode);

        $response->set('phpversion', phpversion());

        $serverStats = [];
        if ($isSwooleMode) {
            $stats = $runtime->getSwooleHttpServer()->stats();

            $serverStats = [];
            $serverStats[] = ['name' => '器启动时间', 'value' => date('Y-m-d H:i:s', $stats['start_time'])];
            $serverStats[] = ['name' => '请求次数', 'value' => $stats['request_count']];

            $serverStats[] = [
                'name' => 'Worker 进程',
                'value' => $stats['worker_num'] . '（空闲：' . $stats['idle_worker_num'] . '）',
                'name2' => 'Task 进程',
                'value2' => $stats['task_worker_num'] . '（空闲：' . $stats['task_idle_worker_num'] . '）',
            ];

            $serverStats[] = [
                'name' => 'Task 进程',
                'value' => $stats['task_worker_num'] . '（空闲：' . $stats['task_idle_worker_num'] . '）',
            ];

            $serverStats[] = ['name' => '当前排队任务数', 'value' => $stats['tasking_num']];

            $serverStats[] = [
                'name' => '当前连接',
                'value' => $stats['connection_num'] . '（接受：' . $stats['accept_count'] . ' / 关闭：' . $stats['close_count'] . '）',
            ];

            $serverStats[] = ['name' => '当前协程数', 'value' => $stats['coroutine_num']];

            //$serverStats[] = ['name' => '发送到 Worker 的包数量', 'value' => $stats['dispatch_count']];
            //$serverStats[] = ['name' => '当前 Worker 进程收到的请求次数', 'value' => $stats['worker_request_count']];
            //$serverStats[] = ['name' => 'master 进程向当前 Worker 进程投递任务的计数', 'value' => $stats['worker_dispatch_count']];
        }

        $response->set('serverStats', $serverStats);
        $response->display();
    }

    public function phpinfo()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $runtime = Be::getRuntime();
        if ($runtime->isSwooleMode()) {
            ob_start();
            phpinfo();
            $phpinfo = ob_get_contents();
            ob_end_clean();

            $response->set('phpinfo', $phpinfo);
            $response->display();
        } else {
            phpinfo();
        }
    }

}