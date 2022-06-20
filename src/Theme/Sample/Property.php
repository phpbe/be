<?php

namespace Be\Theme\Sample;


class Property extends \Be\Theme\Property
{

    public string  $label = '示例主题';
    public array $pages = [
        'Home' => [
            'url' => ['System.Home.index'],
            'sections' => [
                'north' => ['Header'],
                'middle' => ['Slider', 'GroupOfIconWithText', 'Images', 'ImageWithTextOverlay', 'ImageWithText', 'Image'],
                'south' => ['Footer'],
            ],
        ],
    ];

    public function __construct()
    {
        parent::__construct(__FILE__);
    }

}

