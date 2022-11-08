<?php

namespace Be\App\System\Config\Page\Home;


/**
 * @BeConfig("首页")
 */
class index
{

    public int $middle = 1;

    public ?array $middleSections = null;

    /**
     * @BeConfigItem("HEAD头标题",
     *     description="HEAD头标题，用于SEO",
     *     driver = "FormItemInput"
     * )
     */
    public string $title = 'PHPBE双驱框架';

    /**
     * @BeConfigItem("META描述",
     *     description="填写页面内容的简单描述，用于SEO",
     *     driver = "FormItemInputTextArea"
     * )
     */
    public string $metaDescription = 'PHPBE双驱框架';

    /**
     * @BeConfigItem("META关键词",
     *     description="填写页面内容的关键词，用于SEO，3~5个即可，不宜过多，以英文逗号分隔多个关键词。",
     *     driver = "FormItemInput"
     * )
     */
    public string $metaKeywords = 'PHP,BE,双驱,框架';

    /**
     * @BeConfigItem("页面标题",
     *     description="展示在页面内容中的标题，一般与HEAD头标题一致，两者相同时可不填写此项",
     *     driver = "FormItemInput"
     * )
     */
    public string $pageTitle = 'PHPBE双驱框架';


    public function __construct() {
        $this->middleSections = [
            [
                'name' => 'Theme.System.Slider',
                'config' => (object)[
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
                            'config' => (object)[
                                'enable' => 1,
                                'image' => 'https://cdn.phpbe.com/images/slider/desktop-1.png',
                                'imageMobile' => 'https://cdn.phpbe.com/images/slider/mobile-1.png',
                                'link' => 'https://www.phpbe.com',
                            ],
                        ],
                        [
                            'name' => 'Image',
                            'config' => (object)[
                                'enable' => 1,
                                'image' => 'https://cdn.phpbe.com/images/slider/desktop-2.png',
                                'imageMobile' => 'https://cdn.phpbe.com/images/slider/mobile-2.png',
                                'link' => 'https://www.phpbe.com',
                            ],
                        ],
                        [
                            'name' => 'Image',
                            'config' => (object)[
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
                'config' => (object)[
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
                            'config' => (object)[
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
                            'config' => (object)[
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
                            'config' => (object)[
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
                            'config' => (object)[
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
                            'config' => (object)[
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
                            'config' => (object)[
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
                'config' => (object)[
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

}
