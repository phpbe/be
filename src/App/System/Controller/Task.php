<?php

namespace Be\App\System\Controller;

use Be\Be;

class Task
{

    /**
     * @BeRoute("/system/task")
     */
    public function index()
    {
        

        if (Be::getRuntime()->isSwooleMode()) {
            Resonse::error('Swoole 模式下，不需要使用本功能。');
            return;
        }

        Be::getResponse()->success('任务程序运行正常。');
    }

    /**
     * 定时任务
     *
     * @BeRoute("/system/task/schedule")
     */
    public function schedule()
    {
        
        

        if (Be::getRuntime()->isSwooleMode()) {
            Resonse::error('Swoole 模式下，不需要使用本功能。');
            return;
        }

        $password = Request::get('password', '');
        $config = Be::getConfig('App.System.Task');
        if ($config->password !== $password) {
            Resonse::error('密码错误, 任务调度中止！');
            return;
        }

        Be::getService('App.System.Task')->schedule();

        Resonse::success('任务已触发！');
    }

    /**
     * @BeRoute("/system/task/run")
     */
    public function run()
    {
        
        

        if (Be::getRuntime()->isSwooleMode()) {
            Resonse::error('Swoole 模式下，不需要使用本功能。');
            return;
        }

        $password = Request::get('password', '');
        $config = Be::getConfig('App.System.Task');
        if ($config->password !== $password) {
            Resonse::error('密码（password）错误, 任务调度中止！');
            return;
        }

        set_time_limit(0);
        ini_set('memory_limit', '1g');
        ignore_user_abort(true);
        session_write_close();
        header("Connection: close");
        header("HTTP/1.1 200 OK");
        ob_implicit_flush();

        $taskId = Request::get('taskId', '');
        if (!$taskId) {
            Resonse::error('参数（taskId）缺失, 任务中止！');
            return;
        }

        $timestamp = Request::get('timestamp', time());
        $trigger = Request::get('trigger', '');

        /**
         * SYSTEM: 系统定时任务按时启动
         * MANUAL: 用户手工触发
         * RELATED：程序功能关联触发。
         */
        if (!in_array($trigger, ['SYSTEM', 'MANUAL', 'RELATED'])) {
            Resonse::error('参数（trigger）无效, 任务中止！');
            return;
        }

        $taskData = null;
        if (Request::isPost()) {
            $postData = Request::post();
            if (is_array($postData) && count($postData) > 0) {
                $taskData = $postData;
            }
        }

        Be::getService('App.System.Task')->onTask($taskId, $timestamp, $trigger, $taskData);

        Resonse::success('任务执行完成！');
    }


}
