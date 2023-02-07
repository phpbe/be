<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("系统运行日志")
 */
class Log
{

    /**
     * @BeConfigItem("日志级别",
     *     desrciption="大于等于该级别的日志才会记录",
     *     driver="FormItemSelect",
     *     keyValues = "return ['debug' => 'DEBUG：调试信息','info' => 'INFO：事件记录，如：SQL语免','notice' => 'NOTICE：不常见的事件','warning' => 'WARNING：警告信息','error' => 'ERROR：系统，框架运行时错误','critical' => 'CRITICAL：程序中未捕获的异常','alert' => 'ALERT：系统不可用，如数据库连接中断','emergency' => 'EMERGENCY：超级擎报'];")
     */
    public string $level = 'debug';

    /**
     * @BeConfigItem("记录 GET", driver="FormItemSwitch")
     */
    public int $get = 1;

    /**
     * @BeConfigItem("记录 POST", driver="FormItemSwitch")
     */
    public int $post = 1;

    /**
     * @BeConfigItem("记录 REQUEST", driver="FormItemSwitch")
     */
    public int $request = 1;

    /**
     * @BeConfigItem("记录 COOKIE", driver="FormItemSwitch")
     */
    public int $cookie = 1;

    /**
     * @BeConfigItem("记录 SESSION", driver="FormItemSwitch")
     */
    public int $session = 1;

    /**
     * @BeConfigItem("记录 头信息", driver="FormItemSwitch")
     */
    public int $header = 1;

    /**
     * @BeConfigItem("记录 SERVER", driver="FormItemSwitch")
     */
    public int $server = 1;

    /**
     * @BeConfigItem("记录内存占用", driver="FormItemSwitch")
     */
    public int $memery = 1;

}

