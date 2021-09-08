<?php
namespace Be\Theme\Sample\Config\Page;

/**
 * @BeConfig("商品分类页")
 */
class GoodsCategory
{

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



}
