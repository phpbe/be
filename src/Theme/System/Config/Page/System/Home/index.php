<?php

namespace Be\Theme\System\Config\Page\System\Home;

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
                'backgroundColor' => '#FFFFFF',
                'paddingDesktop' => '0',
                'paddingTablet' => '0',
                'paddingMobile' => '0',
                'marginDesktop' => '0',
                'marginTablet' => '0',
                'marginMobile' => '0',
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
                'itemBackgroundColor' => '#F5F5F5',
                'itemTitleColor' => '#333333',
                'itemLinkColor' => '#999999',
                'backgroundColor' => '#FFFFFF',
                'paddingDesktop' => '2rem 0',
                'paddingTablet' => '1.75rem 0',
                'paddingMobile' => '1.5rem 0',
                'marginDesktop' => '0',
                'marginTablet' => '0',
                'marginMobile' => '0',
                'spacingDesktop' => '2rem',
                'spacingTablet' => '1.75rem',
                'spacingMobile' => '1.5rem',
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
                'backgroundColor' => '',
                'paddingDesktop' => '0',
                'paddingTablet' => '0',
                'paddingMobile' => '0',
                'marginDesktop' => '0',
                'marginTablet' => '0',
                'marginMobile' => '0',
            ],
        ],
    ];

}
