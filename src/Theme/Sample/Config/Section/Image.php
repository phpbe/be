<?php
namespace Be\Theme\Sample\Config\Section;


/**
 * @BeConfig("图像", icon="el-icon-picture")
 */
class Image
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public $enable = 1;

    /**
     * @BeConfigItem("宽度",
     *     driver="FormItemSelect",
     *     keyValues = "return ['default' => '默认', 'fullWidth' => '全屏'];"
     * )
     */
    public $width = 'fullWidth';

    /**
     * @BeConfigItem("背景颜色",
     *     driver="FormItemColorPicker"
     * )
     */
    public $backgroundColor = '#fff';

    /**
     * @BeConfigItem("图像",
     *     driver="FormItemImage",
     *     path = "/Theme/Sample/Section/Image/image/")
     */
    public $image = '';

    /**
     * @BeConfigItem("手机版图像",
     *     driver="FormItemImage",
     *     path = "/Theme/Sample/Section/Image/imageMobile/")
     */
    public $imageMobile = '';

    /**
     * @BeConfigItem("链接",
     *     driver = "FormItemInput"
     * )
     */
    public $link = '';

}
