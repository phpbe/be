<?php

namespace Be\Theme;

/**
 * 部件
 */
abstract class Section
{
    // 可用位置：north, middle, west, center, east, south
    public array $positions = [];

    // 当前位置
    public string $position;

    // 设置指定咱由的页面可用
    public array $routes = ['*'];

    // 当前路由
    public string $route;

    // 唯一ID
    public string $id;

    // 配置数据
    public object $config;

    // 调用此部件的页面模板
    public object $pageTemplate;

    /**
     * 输出内容
     *
     * @return void
     */
    public function display()
    {
    }

    /**
     * 输出前置内容
     *
     * 用于 be-page-title be-page-content 等用作包装功能的部件
     * @return void
     */
    public function before()
    {
    }

    /**
     * 输出后置内容
     *
     * 用于 be-page-title be-page-content 等用作包装功能的部件
     * @return void
     */
    public function after()
    {
    }

    /**
     * 背景色 CSS
     *
     * @param string $cssClass 样式类名
     * @return string
     */
    public function getCssBackgroundColor(string $cssClass): string
    {
        $css = '';
        if (isset($this->config->backgroundColor) && $this->config->backgroundColor) {
            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            $css .= 'background-color: ' . $this->config->backgroundColor . ';';
            $css .= '}';
        }
        return $css;
    }

    /**
     * 内边距 CSS
     *
     * @param string $cssClass 样式类名
     * @return string
     */
    public function getCssPadding(string $cssClass): string
    {
        $css = '';

        if (isset($this->config->padding) && $this->config->padding !== '') {
            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            $css .= 'padding: ' . $this->config->padding . ';';
            $css .= '}';
        }

        // 手机端
        if (isset($this->config->paddingMobile) && $this->config->paddingMobile !== '') {
            $css .= '@media (max-width: 768px) {';
            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            $css .= 'padding: ' . $this->config->paddingMobile . ';';
            $css .= '}';
            $css .= '}';
        }

        // 平析端
        if (isset($this->config->paddingTablet) && $this->config->paddingTablet !== '') {
            $css .= '@media (min-width: 768px) {';
            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            $css .= 'padding: ' . $this->config->paddingTablet . ';';
            $css .= '}';
            $css .= '}';
        }

        // 电脑端
        if (isset($this->config->paddingDesktop) && $this->config->paddingDesktop !== '') {
            $css .= '@media (min-width: 992px) {';
            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            $css .= 'padding: ' . $this->config->paddingDesktop . ';';
            $css .= '}';
            $css .= '}';
        }

        return $css;
    }


    /**
     * 外边距 CSS
     *
     * @param string $cssClass 样式类名
     * @return string
     */
    public function getCssMargin(string $cssClass): string
    {
        $css = '';

        if (isset($this->config->margin) && $this->config->margin !== '') {
            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            $css .= 'margin: ' . $this->config->margin . ';';
            $css .= '}';
        }

        // 手机端
        if (isset($this->config->marginMobile) && $this->config->marginMobile !== '') {
            $css .= '@media (max-width: 768px) {';
            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            $css .= 'margin: ' . $this->config->marginMobile . ';';
            $css .= '}';
            $css .= '}';
        }


        // 平析端
        if (isset($this->config->marginTablet) && $this->config->marginTablet !== '') {
            $css .= '@media (min-width: 768px) {';
            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            $css .= 'margin: ' . $this->config->marginTablet . ';';
            $css .= '}';
            $css .= '}';
        }

        // 电脑端
        if (isset($this->config->marginDesktop) && $this->config->marginDesktop !== '') {
            $css .= '@media (min-width: 992px) {';
            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            $css .= 'margin: ' . $this->config->marginDesktop . ';';
            $css .= '}';
            $css .= '}';
        }

        return $css;
    }

    /**
     * 内间距 CSS
     *
     * @param string $cssClass 样式类名
     * @param string $cssItemClass 子项样式类名
     * @param string $itemWidthDesktop 子项宽度 - 电脑羰
     * @param string $itemWidthTablet 子项宽度 - 平板端
     * @param string $itemWidthMobile 子项宽度 - 手机端
     * @return string
     */
    public function getCssSpacing(string $cssClass = 'items', string $cssItemClass = 'item', $itemWidthMobile = '100%', $itemWidthTablet = '50%', $itemWidthDesktop = '33.333333333333%'): string
    {
        $css = '';

        $css .= '#' . $this->id . ' .' . $cssClass . ' {';
        $css .= 'display: flex;';
        $css .= 'flex-wrap: wrap;';
        $css .= 'overflow: hidden;';
        $css .= '}';

        $css .= '#' . $this->id . ' .' . $cssItemClass . ' {';
        $css .= 'flex: 0 1 auto;';
        $css .= 'overflow: hidden;';
        $css .= '}';

        // 手机端
        if (isset($this->config->spacingMobile) && $this->config->spacingMobile !== '') {
            $css .= '@media (max-width: 768px) {';

            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            $css .= 'margin-left: calc(-' . $this->config->spacingMobile . ' / 2);';
            $css .= 'margin-right: calc(-' . $this->config->spacingMobile . ' / 2);';
            $css .= 'margin-bottom: -' . $this->config->spacingMobile . ';';
            $css .= '}';

            $css .= '#' . $this->id . ' .' . $cssItemClass . ' {';
            $css .= 'width: ' . $itemWidthMobile . ';';
            $css .= 'padding-left: calc(' . $this->config->spacingMobile . ' / 2);';
            $css .= 'padding-right: calc(' . $this->config->spacingMobile . ' / 2);';
            $css .= 'margin-bottom: ' . $this->config->spacingMobile . ';';
            $css .= '}';

            $css .= '}';
        }

        // 平析端
        if (isset($this->config->spacingTablet) && $this->config->spacingTablet !== '') {
            $css .= '@media (min-width: 768px) {';

            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            $css .= 'margin-left: calc(-' . $this->config->spacingTablet . ' / 2);';
            $css .= 'margin-right: calc(-' . $this->config->spacingTablet . ' / 2);';
            $css .= 'margin-bottom: -' . $this->config->spacingTablet . ';';
            $css .= '}';

            $css .= '#' . $this->id . ' .' . $cssItemClass . ' {';
            $css .= 'width: ' . $itemWidthTablet . ';';
            $css .= 'padding-left: calc(' . $this->config->spacingTablet . ' / 2);';
            $css .= 'padding-right: calc(' . $this->config->spacingTablet . ' / 2);';
            $css .= 'margin-bottom: ' . $this->config->spacingTablet . ';';
            $css .= '}';

            $css .= '}';
        }

        // 电脑端
        if (isset($this->config->spacingDesktop) && $this->config->spacingDesktop !== '') {
            $css .= '@media (min-width: 992px) {';

            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            $css .= 'margin-left: calc(-' . $this->config->spacingDesktop . ' / 2);';
            $css .= 'margin-right: calc(-' . $this->config->spacingDesktop . ' / 2);';
            $css .= 'margin-bottom: -' . $this->config->spacingDesktop . ';';
            $css .= '}';

            $css .= '#' . $this->id . ' .' . $cssItemClass . ' {';
            $css .= 'width: ' . $itemWidthDesktop . ';';
            $css .= 'padding-left: calc(' . $this->config->spacingDesktop . ' / 2);';
            $css .= 'padding-right: calc(' . $this->config->spacingDesktop . ' / 2);';
            $css .= 'margin-bottom: ' . $this->config->spacingDesktop . ';';
            $css .= '}';

            $css .= '}';
        }

        return $css;
    }


}
