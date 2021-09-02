<?php

namespace Be\App\System\Controller;

use Be\Be;

class Task
{

    public function index()
    {
        $response = Be::getResponse();

        if (Be::getRuntime()->getMode() == 'Swoole') {
            $response->error('Swoole 模式下，不需要使用本功能。');
            return;
        }

        Be::getResponse()->success('任务程序运行正常。');
    }

    public function dispatch()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if (Be::getRuntime()->getMode() == 'Swoole') {
            $response->error('Swoole 模式下，不需要使用本功能。');
            return;
        }

        $password = $request->get('password', '');
        $config = Be::getConfig('App.System.Task');
        if ($config->password != $password) {
            $response->error('密码错误, 任务调度中止！');
            return;
        }

        Be::getService('App.System.Task')->dispatch();
    }


    public function run()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if (Be::getRuntime()->getMode() == 'Swoole') {
            $response->error('Swoole 模式下，不需要使用本功能。');
            return;
        }

        $password = $request->get('password', '');
        $config = Be::getConfig('App.System.Task');
        if ($config->password != $password) {
            $response->error('密码（password）错误, 任务调度中止！');
            return;
        }

        set_time_limit(0);
        ini_set('memory_limit', '1g');
        ignore_user_abort(true);
        session_write_close();
        header("Connection: close");
        header("HTTP/1.1 200 OK");
        ob_implicit_flush();

        $taskId = $request->get('taskId', 0);
        if (!$taskId) {
            $response->error('参数（taskId）缺失, 任务中止！');
            return;
        }

        $timestamp = $request->get('timestamp', time());
        $trigger = $request->get('trigger', '');

        /**
         * SYSTEM: 系统定时任务按时启动
         * MANUAL: 用户手工触发
         * RELATED：程序功能关联触发。
         */
        if (!in_array($trigger, ['SYSTEM', 'MANUAL', 'RELATED'])) {
            $response->error('参数（trigger）无效, 任务中止！');
            return;
        }

        Be::getService('App.System.Task')->run($taskId, $timestamp, $trigger);
    }


}
