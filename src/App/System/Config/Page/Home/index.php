<?php

namespace Be\App\System\Config\Page\Home;

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
                'paddingMobile' => '0',
                'paddingTablet' => '0',
                'paddingDesktop' => '0',
                'marginMobile' => '0',
                'marginTablet' => '0',
                'marginDesktop' => '0',
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
                'paddingMobile' => '1.5rem 0',
                'paddingTablet' => '1.75rem 0',
                'paddingDesktop' => '2rem 0',
                'marginMobile' => '0',
                'marginTablet' => '0',
                'marginDesktop' => '0',
                'spacingMobile' => '1.5rem',
                'spacingTablet' => '1.75rem',
                'spacingDesktop' => '2rem',
                'items' => [
                    [
                        'name' => 'IconWithText',
                        'config' => [
                            'enable' => 1,
                            'iconType' => 'image',
                            'iconSvg' => '',
                            'iconImage' => 'https://cdn.phpbe.com/images/feature-icon/dual-drive.svg',
                            'title' => '??????PHP???Swoole?????????',
                            'linkText' => '????????????',
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
                            'title' => 'C10K?????????????????????',
                            'linkText' => '????????????',
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
                            'title' => '????????????????????????',
                            'linkText' => '????????????',
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
                            'title' => '????????????????????????',
                            'linkText' => '????????????',
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
                            'title' => '???????????????????????????',
                            'linkText' => '????????????',
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
                            'title' => '?????????????????????',
                            'linkText' => '????????????',
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
                'paddingMobile' => '0',
                'paddingTablet' => '0',
                'paddingDesktop' => '0',
                'marginMobile' => '0',
                'marginTablet' => '0',
                'marginDesktop' => '0',
            ],
        ],
    ];

}
