<?php
namespace Be\Theme\Sample\Config\Section;

/**
 * @BeConfig("底部")
 */
class Footer
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public $enable = 1;

    /**
     * @BeConfigItem("版权信息",
     *     driver="FormItemInputTextarea"
     * )
     */
    public $copyright = '';

    /**
     * @BeConfigItem("顶部外边距（像素）",
     *     driver = "FormItemSlider"
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public $marginTop = 20;

    public function __icon() {
        return '<svg viewBox="0 0 20 20" focusable="false" aria-hidden="true"><path d="M1 2a1 1 0 0 1 1-1h2v2H3v1H1V2zm17-1a1 1 0 0 1 1 1v2h-2V3h-1V1h2zm1 16.5V11H1v6.5A1.5 1.5 0 0 0 2.5 19h15a1.5 1.5 0 0 0 1.5-1.5zM19 6v3h-2V6h2zM3 9V6H1v3h2zm11-8v2h-3V1h3zM9 3V1H6v2h3z"></path></svg>';
    }
}
