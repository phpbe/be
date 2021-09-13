<?php
namespace Be\AdminTheme\Admin\Config\Page;

/**
 * @BeConfig("首页")
 */
class Home
{

    public $northSections = ['Header'];
    public $northSectionsData = [
        [
            'enable' => 1,
            'logoType' => 'text',
            'logoText' => 'Beyond Exception',
            'logoImage' => '',
            'marginTop' => 0,
        ]
    ];

    public $middleSections = ['Slider', 'Banner'];
    public $middleSectionsData = [
        [
            'enable' => 1,
            'autoplay' => 1,
            'delay' => 3000,
            'speed' => 300,
            'loop' => 1,
            'pagination' => 1,
            'paginationColor' => '#FF6600',
            'navigation' => 1,
            'navigationColor' => '#FF6600',
            'navigationSize' => 30,
            'marginTop' => 0,
            'items' => [

            ]
        ],
        [
            'enable' => 1,
            'hoverEffect' => 'rotateScale',
            'marginTop' => 20,
            'marginLeftRight' => 0,
            'spacing' => 30,
            'items' => [

            ]
        ]
    ];

    public $southSections = ['Footer'];
    public $southSectionsData = [
        [
            'enable' => 1,
            'copyright' => 'Copyright©2020-2021 BE版权所有',
            'marginTop' => 20,
        ]
    ];

}
