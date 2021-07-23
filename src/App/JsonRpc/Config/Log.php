<?php
namespace Be\App\JsonRpc\Config;

/**
 * @BeConfig("日志")
 */
class Log
{

    /**
     * @BeConfigItem("访问日志", driver="FormItemSwitch")
     */
    public $accessLog = false;

    /**
     * @BeConfigItem("错误日志", driver="FormItemSwitch")
     */
    public $errorLog = true;

}
