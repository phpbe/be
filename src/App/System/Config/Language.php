<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("多语言")
 */
class Language
{

    /**
     * @BeConfigItem("语言列表", driver="FormItemCode", language="json", valueType = "array(string)")
     */
    public array $languages = ['zh-CN', 'en-US'];

    /**
     * @BeConfigItem("默认语言", driver="FormItemInput")
     */
    public string $default = 'zh-CN';

    /**
     * @BeConfigItem("自动检测", driver="FormItemSwitch")
     */
    public int $autoDetect = 0;

}
