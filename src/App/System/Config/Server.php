<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("Swoole 服务器", enable="return \Be\Be::getRuntime()->isSwooleMode();")
 */
class Server
{

    /**
     * @BeConfigItem("监听的IP地址", driver="FormItemInput")
     */
    public string $host = '0.0.0.0';

    /**
     * @BeConfigItem("端口号",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 1];")
     */
    public int $port = 80;

    /**
     * @BeConfigItem("Reactor线程数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public int $reactor_num = 0;

    /**
     * @BeConfigItem("Worker进程数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public int $worker_num = 0;

    /**
     * @BeConfigItem("Worker进程最大任务数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public int $max_request = 0;

    /**
     * @BeConfigItem("最大连接数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public int $max_conn = 0;

    /**
     * @BeConfigItem("Task进程数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public int $task_worker_num = 4;

    /**
     * @BeConfigItem("Task进程最大任务数",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 0];")
     */
    public int $task_max_request = 0;

    /**
     * @BeConfigItem("启动时清理运行时环境", description="运行时环境缓存了表结构，路由，实时编译的模板等" driver="FormItemSwitch")
     */
    public int $clearRuntimeOnStart = 1;

}
