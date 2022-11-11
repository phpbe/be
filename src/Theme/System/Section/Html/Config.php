<?php
namespace Be\Theme\System\Section\Html;


/**
 * @BeConfig("HTML源码", icon="bi-code")
 */
class Config
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public int $enable = 1;

    /**
     * @BeConfigItem("宽度",
     *     description="位于middle时有效",
     *     driver="FormItemSelect",
     *     keyValues = "return ['default' => '默认', 'fullWidth' => '全屏'];"
     * )
     */
    public string $width = 'default';

    /**
     * @BeConfigItem("内容",
     *     driver="FormItemCode",
     *     language="html"
     * )
     */
    public string $content = '';

}
