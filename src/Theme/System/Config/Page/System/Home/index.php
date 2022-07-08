<?php

namespace Be\Theme\System\Config\Page\System\Home;

/**
 * @BeConfig("首页")
 */
class index
{

    public int $middle = 1;

    public array $middleSections = [
        [
            'name' => 'Theme.System.Slider',
            'config' => [
                'enable' => 1,
                'width' => 'fullWidth',
                'autoplay' => 1,
                'delay' => 3000,
                'speed' => 300,
                'loop' => 1,
                'pagination' => 1,
                'navigation' => 1,
                'navigationSize' => 30,
                'backgroundColor' => '#fff',
                'paddingTopDesktop' => 0,
                'paddingTopTablet' => 0,
                'paddingTopMobile' => 0,
                'paddingBottomDesktop' => 0,
                'paddingBottomTablet' => 0,
                'paddingBottomMobile' => 0,
                'items' => [
                    [
                        'name' => 'Image',
                        'config' => [
                            'enable' => 1,
                            'image' => 'https://cdn.phpbe.com/images/slider/desktop-1.png',
                            'imageMobile' => 'https://cdn.phpbe.com/images/slider/mobile-1.png',
                            'link' => 'https://www.phpbe.com',
                        ],
                    ],
                    [
                        'name' => 'Image',
                        'config' => [
                            'enable' => 1,
                            'image' => 'https://cdn.phpbe.com/images/slider/desktop-2.png',
                            'imageMobile' => 'https://cdn.phpbe.com/images/slider/mobile-2.png',
                            'link' => 'https://www.phpbe.com',
                        ],
                    ],
                    [
                        'name' => 'Image',
                        'config' => [
                            'enable' => 1,
                            'image' => 'https://cdn.phpbe.com/images/slider/desktop-3.png',
                            'imageMobile' => 'https://cdn.phpbe.com/images/slider/mobile-3.png',
                            'link' => 'https://www.phpbe.com',
                        ],
                    ],
                ]
            ],
        ],
        [
            'name' => 'Theme.System.GroupOfIconWithText',
            'config' => [
                'enable' => 1,
                'width' => 'default',
                'backgroundColor' => '#fff',
                'itemBackgroundColor' => '#f7f7f7',
                'itemTitleColor' => '#333333',
                'itemLinkColor' => '#999',
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
                    [
                        'name' => 'IconWithText',
                        'config' => [
                            'enable' => 1,
                            'iconType' => 'image',
                            'iconSvg' => '',
                            'iconImage' => 'https://cdn.phpbe.com/images/feature-icon/dual-drive.svg',
                            'title' => '普通PHP和Swoole双驱动',
                            'linkText' => '了解更多',
                            'linkUrl' => 'https://www.phpbe.com',
                        ],
                    ],
                    [
                        'name' => 'IconWithText',
                        'config' => [
                            'enable' => 1,
                            'iconType' => 'image',
                            'iconSvg' => '',
                            'iconImage' => 'https://cdn.phpbe.com/images/feature-icon/c10k.svg',
                            'title' => 'C10K高并发，高可用',
                            'linkText' => '了解更多',
                            'linkUrl' => 'https://www.phpbe.com',
                        ],
                    ],
                    [
                        'name' => 'IconWithText',
                        'config' => [
                            'enable' => 1,
                            'iconType' => 'image',
                            'iconSvg' => '',
                            'iconImage' => 'https://cdn.phpbe.com/images/feature-icon/friendly.svg',
                            'title' => '开发友好，无门槛',
                            'linkText' => '了解更多',
                            'linkUrl' => 'https://www.phpbe.com',
                        ],
                    ],
                    [
                        'name' => 'IconWithText',
                        'config' => [
                            'enable' => 1,
                            'iconType' => 'image',
                            'iconSvg' => '',
                            'iconImage' => 'https://cdn.phpbe.com/images/feature-icon/low-code.svg',
                            'title' => '低代码，快速迭代',
                            'linkText' => '了解更多',
                            'linkUrl' => 'https://www.phpbe.com',
                        ],
                    ],
                    [
                        'name' => 'IconWithText',
                        'config' => [
                            'enable' => 1,
                            'iconType' => 'image',
                            'iconSvg' => '',
                            'iconImage' => 'https://cdn.phpbe.com/images/feature-icon/update.svg',
                            'title' => '持续升级，快速响应',
                            'linkText' => '了解更多',
                            'linkUrl' => 'https://www.phpbe.com',
                        ],
                    ],
                    [
                        'name' => 'IconWithText',
                        'config' => [
                            'enable' => 1,
                            'iconType' => 'image',
                            'iconSvg' => '',
                            'iconImage' => 'https://cdn.phpbe.com/images/feature-icon/free.svg',
                            'title' => '开源，无需费用',
                            'linkText' => '了解更多',
                            'linkUrl' => 'https://www.phpbe.com',
                        ],
                    ],
                ],
            ],
        ],
        [
            'name' => 'Theme.System.Banner',
            'config' => [
                'enable' => 1,
                'width' => 'fullWidth',
                'image' => 'https://cdn.phpbe.com/images/banner/desktop-1.png',
                'imageMobile' => 'https://cdn.phpbe.com/images/banner/mobile-1.png',
                'link' => 'https://www.phpbe.com',
                'backgroundColor' => '#fff',
                'itemBackgroundColor' => '#f7f7f7',
                'itemTitleColor' => '#333333',
                'itemLinkColor' => '#999',
                'paddingTopDesktop' => 0,
                'paddingTopTablet' => 0,
                'paddingTopMobile' => 0,
                'paddingBottomDesktop' => 40,
                'paddingBottomTablet' => 30,
                'paddingBottomMobile' => 20,
            ],
        ],
    ];

}
