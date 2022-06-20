<?php

namespace Be\Theme;

/**
 * 主题属性
 */
abstract class Property extends \Be\Property\Driver
{

    protected string $type = 'theme';

    /**
     * 可䨒置的页面
     *
     * @var string[]
     */
    public array $pages = ['Home'];

    /**
     * 预览图片
     *
     * @var string
     */
    public string $previewImage = '';


    public function getPreviewImageUrl(): string
    {
        if ($this->previewImage) {
            return $this->getWwwUrl() . '/' . $this->previewImage;
        } else {
            return \Be\Be::getProperty('App.System')->getWwwUrl() . '/admin/theme-editor/images/no-preview.jpg';
        }
    }

}
