<?php
namespace Be\Theme\Sample\Config\Page;

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
            'logoImageMaxWidth' => 0,
            'logoImageMaxHeight' => 0,
            'backgroundColor' => '#FFFFFF',
            'paddingTopDesktop' => 40,
            'paddingTopTablet' => 30,
            'paddingTopMobile' => 20,
            'paddingBottomDesktop' => 40,
            'paddingBottomTablet' => 30,
            'paddingBottomMobile' => 20,
        ]
    ];

    public $middleSections = ['Slider', 'Images'];
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
            'backgroundColor' => '#FFFFFF',
            'paddingTopDesktop' => 40,
            'paddingTopTablet' => 30,
            'paddingTopMobile' => 20,
            'paddingBottomDesktop' => 40,
            'paddingBottomTablet' => 30,
            'paddingBottomMobile' => 20,
            'items' => [

            ]
        ],
        [
            'enable' => 1,
            'backgroundColor' => '#FFFFFF',
            'hoverEffect' => 'rotateScale',
            'paddingTopDesktop' => 40,
            'paddingTopTablet' => 30,
            'paddingTopMobile' => 20,
            'paddingBottomDesktop' => 40,
            'paddingBottomTablet' => 30,
            'paddingBottomMobile' => 20,
            'spacingDesktop' => 40,
            'spacingTablet' => 30,
            'spacingMobile' => 20,
            'items' => [

            ]
        ]
    ];

    public $southSections = ['Footer'];
    public $southSectionsData = [
        [
            'enable' => 1,
            'copyright' => 'Copyright©2020-2021 BE版权所有',
            'backgroundColor' => '#FFFFFF',
            'paddingTopDesktop' => 40,
            'paddingTopTablet' => 30,
            'paddingTopMobile' => 20,
            'paddingBottomDesktop' => 40,
            'paddingBottomTablet' => 30,
            'paddingBottomMobile' => 20,
        ]
    ];

}
