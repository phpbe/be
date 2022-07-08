<?php
namespace Be\Theme\System\Section\Header;

/**
 * @BeConfig("头部", ordering="1")
 */
class Config
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public int $enable = 1;

    /**
     * @BeConfigItem("Logo类型",
     *     driver="FormItemSelect",
     *     keyValues = "return ['text' => '文字', 'image' => '图像']"
     * )
     */
    public string $logoType = 'image';

    /**
     * @BeConfigItem("Logo文字",
     *     driver="FormItemInput",
     *     ui="return ['form-item' => ['v-show' => 'formData.logoType == \'text\'']];"
     * )
     */
    public string $logoText = 'Beyond Exception';

    /**
     * @BeConfigItem("Logo文字颜色",
     *     driver="FormItemColorPicker",
     *     ui="return ['form-item' => ['v-show' => 'formData.logoType == \'text\'']];"
     * )
     */
    public string $logoTextColor = '#666';

    /**
     * @BeConfigItem("Logo图像",
     *     driver="FormItemStorageImage",
     *     ui="return ['form-item' => ['v-show' => 'formData.logoType == \'image\'']];"
     * )
     */
    public string $logoImage = 'https://cdn.phpbe.com/images/logo/be.png';

    /**
     * @BeConfigItem("Logo图像最大宽度",
     *     driver="FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.logoType == \'image\'']];"
     * )
     */
    public int $logoImageMaxWidth = 0;

    /**
     * @BeConfigItem("Logo图像最大高度",
     *     driver="FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.logoType == \'image\'']];"
     * )
     */
    public int $logoImageMaxHeight = 0;

    /**
     * @BeConfigItem("背景颜色",
     *     driver="FormItemColorPicker"
     * )
     */
    public string $backgroundColor = '#fff';


    public function __icon() {
        return '<svg viewBox="0 0 20 20" focusable="false" aria-hidden="true"><path d="M1 2.5V9h18V2.5A1.5 1.5 0 0 0 17.5 1h-15A1.5 1.5 0 0 0 1 2.5zM2 19a1 1 0 0 1-1-1v-2h2v1h1v2H2zm17-1a1 1 0 0 1-1 1h-2v-2h1v-1h2v2zM1 14v-3h2v3H1zm16-3v3h2v-3h-2zM6 17h3v2H6v-2zm8 0h-3v2h3v-2z"></path></svg>';
    }

}
