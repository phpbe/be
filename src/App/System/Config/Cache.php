<?php

namespace Be\App\System\Config;

/**
 * @BeConfig("缓存")
 */
class Cache
{

    /**
     * @BeConfigItem("驱动",
     *     driver="FormItemSelect",
     *     keyValues = "return ['File' => '文件', 'Redis' => 'Redis'];")
     */
    public string $driver = 'File';

    /**
     * @BeConfigItem("REDIS库",
     *     driver="FormItemSelect",
     *     keyValues = "return \Be\Redis\RedisHelper::getConfigKeyValues();",
     *     ui="return ['form-item' => ['v-show' => 'formData.driver === \'Redis\'']];")
     */
    public string $redis = 'master';

}
