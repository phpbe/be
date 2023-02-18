<?php

namespace Be\AdminPlugin\UiItem;

/**
 * UI项
 *
 */
class UiItem
{

    public function __get(string $property)
    {
        if (isset($this->$property)) {
            return ($this->$property);
        } else {
            return null;
        }
    }

    /**
     * 获取需要引入的 JS 文件
     *
     * @return false | array
     */
    public function getJs()
    {
        return false;
    }

    /**
     * 获取需要引入的 JS 代码
     *
     * @return false | string
     */
    public function getJsCode()
    {
        return false;
    }

    /**
     * 获取需要引入的 CSS 代码
     *
     * @return false | array
     */
    public function getCss()
    {
        return false;
    }

    /**
     * 获取需要引入的 CSS 文件
     *
     * @return false | string
     */
    public function getCssCode()
    {
        return false;
    }

    /**
     * 获取HTML内容
     *
     * @return string
     */
    public function getHtml(): string
    {
        return '';
    }

    /**
     * 获取 vue data
     *
     * @return false | array
     */
    public function getVueData()
    {
        return false;
    }

    /**
     * 获取 vue 方法
     *
     * @return false | array
     */
    public function getVueMethods()
    {
        return false;
    }

    /**
     * 获取 vue 钩子
     *
     * @return false | array
     */
    public function getVueHooks()
    {
        return false;
    }

}