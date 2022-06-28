<?php

namespace Be\Theme\System\Config\Page\System\Home;

use Be\Theme\System\Config\Page;

/**
 * @BeConfig("首页")
 */
class index extends Page
{

    /**
     * @BeConfigItem("是否启用底",
     *     driver = "FormItemPageSection")
     */
    public array $middleSections = [
        'Theme.System.Slider',
        'Theme.System.Images',
        'Theme.System.Image',
    ];


}
