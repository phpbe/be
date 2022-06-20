<?php

namespace Be\AdminTheme\Admin;


class Property extends \Be\AdminTheme\Property
{

    public string $label = '默认主题';

    public array $pages = [
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
    public string $previewImage = 'images/preview.jpg';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

