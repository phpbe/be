<?php

namespace Be\AdminTheme\Admin;


class Property extends \Be\AdminTheme\Property
{

    public $label = '默认主题';

    public $pages = [
        'Home' => [
            'url' => ['System.index.index'],
            'sections' => [
            ],
        ],
    ];

    /**
     * 预览图片
     *
     * @var string
     */
    public $previewImage = 'images/preview.jpg';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

