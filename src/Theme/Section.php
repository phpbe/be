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

    // 唯一ID
    public string $id;

    // 配置数据
    public object $config;

    // 调用此部件的主题
    public object $theme;

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
     * 用于 be-content
     * @return void
     */
    public function before()
    {
    }

    /**
     * 输出后置内容
     *
     * 用于 be-content
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
        if ($this->config->backgroundColor) {
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

        // 手机端
        if ($this->config->paddingTopMobile || $this->config->paddingBottomMobile) {
            $css .= '@media (max-width: 768px) {';
            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            if ($this->config->paddingTopMobile) {
                $css .= 'padding-top: ' . $this->config->paddingTopMobile . 'px;';
            }
            if ($this->config->paddingBottomMobile) {
                $css .= 'padding-bottom: ' . $this->config->paddingBottomMobile . 'px;';
            }
            $css .= '}';
            $css .= '}';
        }

        // 平析端
        if ($this->config->paddingTopTablet || $this->config->paddingBottomTablet) {
            $css .= '@media (min-width: 768px) {';
            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            if ($this->config->paddingTopTablet) {
                $css .= 'padding-top: ' . $this->config->paddingTopTablet . 'px;';
            }
            if ($this->config->paddingBottomTablet) {
                $css .= 'padding-bottom: ' . $this->config->paddingBottomTablet . 'px;';
            }
            $css .= '}';
            $css .= '}';
        }

        // 电脑端
        if ($this->config->paddingTopDesktop || $this->config->paddingBottomDesktop) {
            $css .= '@media (min-width: 992px) {';
            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            if ($this->config->paddingTopDesktop) {
                $css .= 'padding-top: ' . $this->config->paddingTopDesktop . 'px;';
            }
            if ($this->config->paddingBottomDesktop) {
                $css .= 'padding-bottom: ' . $this->config->paddingBottomDesktop . 'px;';
            }
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
        if ($this->config->spacingMobile) {
            $css .= '@media (max-width: 768px) {';

            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            $css .= 'margin-left: -' . ($this->config->spacingMobile / 2) . 'px;';
            $css .= 'margin-right: -' . ($this->config->spacingMobile / 2) . 'px;';
            $css .= 'margin-bottom: -' . $this->config->spacingMobile . 'px;';
            $css .= '}';

            $css .= '#' . $this->id . ' .' . $cssItemClass . ' {';
            $css .= 'width: ' . $itemWidthMobile . ';';
            if ($this->config->spacingMobile) {
                $css .= 'padding-left: ' . ($this->config->spacingMobile / 2) . 'px;';
                $css .= 'padding-right: ' . ($this->config->spacingMobile / 2) . 'px;';
                $css .= 'margin-bottom: ' . $this->config->spacingMobile . 'px;';
            }
            $css .= '}';

            $css .= '}';
        }

        // 平析端
        if ($this->config->spacingTablet) {
            $css .= '@media (min-width: 768px) {';

            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            $css .= 'margin-left: -' . ($this->config->spacingTablet / 2) . 'px;';
            $css .= 'margin-right: -' . ($this->config->spacingTablet / 2) . 'px;';
            $css .= 'margin-bottom: -' . $this->config->spacingTablet . 'px;';
            $css .= '}';

            $css .= '#' . $this->id . ' .' . $cssItemClass . ' {';
            $css .= 'width: ' . $itemWidthTablet . ';';
            if ($this->config->spacingTablet) {
                $css .= 'padding-left: ' . ($this->config->spacingTablet / 2) . 'px;';
                $css .= 'padding-right: ' . ($this->config->spacingTablet / 2) . 'px;';
                $css .= 'margin-bottom: ' . $this->config->spacingTablet . 'px;';
            }
            $css .= '}';

            $css .= '}';
        }

        // 电脑端
        if ($this->config->spacingDesktop) {
            $css .= '@media (min-width: 992px) {';

            $css .= '#' . $this->id . ' .' . $cssClass . ' {';
            $css .= 'margin-left: -' . ($this->config->spacingDesktop / 2) . 'px;';
            $css .= 'margin-right: -' . ($this->config->spacingDesktop / 2) . 'px;';
            $css .= 'margin-bottom: -' . $this->config->spacingDesktop . 'px;';
            $css .= '}';

            $css .= '#' . $this->id . ' .' . $cssItemClass . ' {';
            $css .= 'width: ' . $itemWidthDesktop . ';';
            if ($this->config->spacingDesktop) {
                $css .= 'padding-left: ' . ($this->config->spacingDesktop / 2) . 'px;';
                $css .= 'padding-right: ' . ($this->config->spacingDesktop / 2) . 'px;';
                $css .= 'margin-bottom: ' . $this->config->spacingDesktop . 'px;';
            }
            $css .= '}';

            $css .= '}';
        }

        return $css;
    }


}
