<?php
namespace Be\Theme\System\Section\Php;


/**
 * @BeConfig("PHP代码", icon="bi-code")
 */
class Config
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public int $enable = 1;

    /**
     * @BeConfigItem("代码",
     *     driver="FormItemCode",
     *     language="php"
     * )
     */
    public string $content = '';

}
