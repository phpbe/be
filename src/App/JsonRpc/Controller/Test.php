<?php

namespace Be\App\JsonRpc\Controller;


use Be\Be;

class Test
{


    public function index()
    {
        

        // 远程服务调用
        $result = Be::getService('App.JsonRpc.JsonRpc')->proxy('JsonRpc.Test')->sum(1, 1);
        Resonse::write(print_r($result, true));

        // 批量调用
        $result = Be::getService('App.JsonRpc.JsonRpc')->bulk([
            [
                'service' => 'JsonRpc.Test',
                'method' => 'sum',
                'params' => [2, 2]
            ],
            [
                'service' => 'JsonRpc.Test',
                'method' => 'sum',
                'params' => [3, 3]
            ],
            [
                'service' => 'JsonRpc.Test',
                'method' => 'sum',
                'params' => [4, 4]
            ],
        ]);
        Resonse::write(print_r($result, true));

        /**
         * 调用其它服务
         *
         * 远程调用 System 应用下 App Service 的 getApps 方法
         */
        $result = Be::getService('App.JsonRpc.JsonRpc')->proxy('System.App')->getApps();
        Resonse::write(print_r($result, true));

    }


}
