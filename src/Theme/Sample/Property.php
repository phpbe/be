<?php

namespace Be\Theme\Sample;


class Property extends \Be\Theme\Property
{

    public $label = '示例主题';
    public $pages = [
        'Home' => [
            'url' => ['System.index.index'],
            'sections' => [
                'north' => ['Header'],
                'middle' => ['Slider', 'Banner', 'ImageWithTextOverlay', 'ImageWithText'],
                'south' => ['Footer'],
            ],
        ],
        'GoodsCategory' => [
            'url' => ['System.index.index'],
            'sections' => [
                'north' => ['Header'],
                'middle' => ['Slider', 'Banner', 'ImageWithTextOverlay', 'ImageWithText'],
                'south' => ['Footer'],
            ],
        ],
    ];

    public function __construct()
    {
        parent::__construct(__FILE__);
    }

}

