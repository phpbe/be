<?php

namespace Be\Theme;

/**
 * 主题属性
 */
abstract class Property extends \Be\Property\Driver
{

    protected string $type = 'theme';

    /**
     * 预览图片
     *
     * @var string
     */
    public string $previewImage = '';


    public function getPreviewImageUrl(): string
    {
        if ($this->previewImage) {
            $prefix = substr($this->previewImage, 0 , 7);
            if ($prefix === 'http://' || $prefix === 'https:/') {
                return $this->previewImage;
            } else {
                return $this->getWwwUrl() . '/' . $this->previewImage;
            }
        } else {
            return \Be\Be::getProperty('App.System')->getWwwUrl() . '/admin/theme-editor/images/no-preview.jpg';
        }
    }

}
