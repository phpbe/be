<?php

namespace Be\App\System\Config;

/**
 * @BeConfig("SESSION")
 */
class Session
{
    /**
     * @BeConfigItem("名称",
     *     driver="FormItemInput",
     *     description = "用在 cookie 或者 URL 中的会话名称， 例如：PHPSESSID。 只能使用字母和数字，建议尽可能的短一些")
     */
    public string $name = 'SSID';

    /**
     * @BeConfigItem("SESSION 超时时间",
     *     driver="FormItemInputNumberInt",
     *     ui = "return [':min' => 1];")
     */
    public int $expire = 1440;

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
