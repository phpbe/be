<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("图片水印", test = "return beAdminUrl('System.Watermark.test');")
 */
class Watermark
{
    /**
     * @BeConfigItem("是否启用", driver = "FormItemSwitch")
     */
    public $enable = 0;

    /**
     * @BeConfigItem("类型",
     *     driver = "FormItemSelect",
     *     keyValues = "return ['text' => '文字', 'image' => '图像'];",
     *     ui="return ['form-item' => ['v-show' => 'formData.enable === 1']];")
     */
    public $type = 'text';

    /**
     * @BeConfigItem("水印位置",
     *     driver = "FormItemSelect",
     *     keyValues = "return ['north'=>'上','south'=>'下','east'=>'左','west'=>'右','center'=>'中','northwest'=>'左上','southwest'=>'左下','northeast'=>'右上','southeast'=>'右下'];",
     *     ui="return ['form-item' => ['v-show' => 'formData.enable === 1']];")
     */
    public $position = 'southeast';

    /**
     * @BeConfigItem("水平偏移像素值",
     *     driver = "FormItemInputNumberInt",
     *     ui = "return ['form-item' => ['v-show' => 'formData.enable === 1']];")
     */
    public $offsetX = -70;

    /**
     * @BeConfigItem("垂直偏移像素值",
     *     driver="FormItemInputNumberInt",
     *     ui="return ['form-item' => ['v-show' => 'formData.enable === 1']];")
     */
    public $offsetY = -70;

    /**
     * @BeConfigItem("图像水印文件",
     *     driver = "FormItemImage",
     *     path = "/System/Watermark/",
     *     ui = "return ['form-item' => ['v-show' => 'formData.enable === 1 && formData.type === \'image\'']];",
     *     maxWidth = "256",
     *     maxHeight = "256",
     * )
     */
    public $image = '';

    /**
     * @BeConfigItem("文印文字",
     *     driver = "FormItemInput",
     *     ui = "return ['form-item' => ['v-show' => 'formData.enable === 1 && formData.type === \'text\'']];")
     */
    public $text = 'BE';

    /**
     * @BeConfigItem("文印文字大小",
     *     driver = "FormItemInputNumberInt",
     *     ui = "return ['form-item' => ['v-show' => 'formData.enable === 1 && formData.type === \'text\''], ':min' => 1];")
     */
    public $textSize = 20;

    /**
     * @BeConfigItem("文印文字颜色",
     *     driver = "FormItemCode",
     *     language="json",
     *     valueType = "array(int)",
     *     ui = "return ['form-item' => ['v-show' => 'formData.enable === 1 && formData.type === \'text\'']];")
     */
    public $textColor = [255, 255, 255];


}
