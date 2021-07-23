<?php
namespace Be\App\JsonRpc\Config;

/**
 * @BeConfig("JsonRpc")
 */
class JsonRpc
{

    /**
     * @BeConfigItem("是否启用", driver="FormItemSwitch")
     */
    public $enable = false;

    /**
     * @BeConfigItem("访问日志", driver="FormItemSwitch")
     */
    public $accessLog = false;

    /**
     * @BeConfigItem("错误日志", driver="FormItemSwitch")
     */
    public $errorLog = true;

}
