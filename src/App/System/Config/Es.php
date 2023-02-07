<?php

namespace Be\App\System\Config;

/**
 * @BeConfig("ES搜索引擎")
 */
class Es
{

    /**
     * @BeConfigItem("是否启用", driver = "FormItemSwitch")
     */
    public int $enable = 0;

    /**
     * @BeConfigItem("ES服务器",
     *     driver="FormItemCode",
     *     language="json",
     *     valueType = "array(string)",
     *     ui="return ['form-item' => ['v-show' => 'formData.enable === 1']];")
     */
    public array $hosts = ['127.0.0.1:9200'];

}
