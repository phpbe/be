<?php
namespace Be\AdminTheme\Admin\Config;

/**
 * @BeConfig("页面配置")
 */
class Page
{
    /**
     * @BeConfigItem("是否启用项部",
     *     driver = "FormItemSwitch")
     */
    public int $north = 0;

    /**
     * @BeConfigItem("是否启用中部",
     *     driver = "FormItemSwitch")
     */
    public int $middle = 0;

    /**
     * @BeConfigItem("是否启用左侧",
     *     driver = "FormItemSwitch")
     */
    public int $west = 0;

    /**
     * @BeConfigItem("是否启用左侧",
     *     driver = "FormItemSwitch")
     */
    public int $center = 0;

    /**
     * @BeConfigItem("是否启用右",
     *     driver = "FormItemSwitch")
     */
    public int $east = 0;

    /**
     * @BeConfigItem("是否启用底",
     *     driver = "FormItemSwitch")
     */
    public int $south = 0;

}
