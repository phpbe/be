<?php

namespace Be\App\System\Config;

/**
 * @BeConfig("ES搜索引擎")
 */
class Es
{

    /**
     * @BeConfigItem("ES服务器", driver="FormItemCode", language="json", valueType = "array(string)")
     */
    public $hosts = ['127.0.0.1:9200'];

}
