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
            'autoPlay' => 1,
            'autoPlayInterval' => 10,
            'dots' => 1,
            'dotsColor' => '#707979',
            'dotsActiveColor' => '#747D7D',
            'arrow' => 1,
            'items' => [

            ]
        ],
        [
            'items' => [

            ]
        ]
    ];



}
