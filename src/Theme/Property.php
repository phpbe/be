<?php
namespace Be\Theme;

/**
 * 主题属性
 */
abstract class Property extends \Be\Property\Driver
{

    /**
     * 可䨒置的页面
     *
     * @var string[]
     */
    public $pages = ['Home'];

    /**
     * 预览图片
     *
     * @var string
     */
    public $previewImage = '';


    public function getPreviewImageUrl () {
        if ($this->previewImage) {
            return $this->getUrl() . '/' . $this->previewImage;
        } else {
            return \Be\Be::getProperty('App.System')->getUrl() . '/Template/Admin/ThemeEditor/images/no-preview.jpg';
        }
    }

}
