<?php

namespace Be\AdminTheme\System;


class Property extends \Be\AdminTheme\Property
{

    public string $label = '系统主题';

    /**
     * 预览图片
     *
     * @var string
     */
    public string $previewImage = 'images/preview.jpg';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

