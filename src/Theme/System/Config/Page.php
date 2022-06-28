<?php
namespace Be\Theme\System\Config;

/**
 * @BeConfig("页面配置")
 */
class Page
{
    /**
     * @BeConfigItem("是否启用项部",
     *     driver = "FormItemSwitch")
     */
    public int $north = 1;

    /**
     * @BeConfigItem("是否启用中部",
     *     driver = "FormItemSwitch")
     */
    public int $middle = 1;

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
    public int $south = 1;

    /**
     * @BeConfigItem("是否启用底",
     *     driver = "FormItemPageSection")
     */
    public array $northSections = [
        'Theme.System.Header'
    ];

    /**
     * @BeConfigItem("是否启用底",
     *     driver = "FormItemPageSection")
     */
    public array $middleSections = [
        'be-content',
    ];

    /**
     * @BeConfigItem("是否启用底",
     *     driver = "FormItemPageSection")
     */
    public array $westSections = [

    ];

    /**
     * @BeConfigItem("是否启用底",
     *     driver = "FormItemPageSection")
     */
    public array $centerSections = [
        'be-content',
    ];

    /**
     * @BeConfigItem("是否启用底",
     *     driver = "FormItemPageSection")
     */
    public array $eastSections = [

    ];

    /**
     * @BeConfigItem("是否启用底",
     *     driver = "FormItemPageSection")
     */
    public array $southSections = [
        'Theme.System.Footer',
    ];

}
