<?php

namespace Be\AdminPlugin\Form;

use Be\AdminPlugin\Form\Item\FormItem;
use Be\Be;

/**
 * 表单项管理
 *
 */
class FormItems
{

    //private $formItems = [];

    private $js = [];
    private $css = [];
    private $vueData = [];
    private $vueMethods = [];
    private $vueHooks = [];

    public function append(FormItem $formItem)
    {
        //$this->formItems[] = $formItem;

        $jsX = $formItem->getJs();
        if ($jsX) {
            $this->js = array_merge($this->js, $jsX);
        }

        $cssX = $formItem->getCss();
        if ($cssX) {
            $this->css = array_merge($this->css, $cssX);
        }

        $vueDataX = $formItem->getVueData();
        if ($vueDataX) {
            $this->vueData = \Be\Util\Arr::merge($this->vueData, $vueDataX);
        }

        $vueMethodsX = $formItem->getVueMethods();
        if ($vueMethodsX) {
            $this->vueMethods = array_merge($this->vueMethods, $vueMethodsX);
        }

        $vueHooksX = $formItem->getVueHooks();
        if ($vueHooksX) {
            foreach ($vueHooksX as $k => $v) {
                if (isset($this->vueHooks[$k])) {
                    $this->ueHooks[$k] .= "\r\n" . $v;
                } else {
                    $this->vueHooks[$k] = $v;
                }
            }
        }
    }

    /**
     * 获取 JS代码
     *
     * @return string
     */
    public function getJs(): string
    {
        if (count($this->js) == 0) {
            return '';
        }

        $html = '';
        $js = array_unique($this->js);
        foreach ($js as $x) {
            $html .= '<script src="' . $x . '"></script>';
        }

        return $html;
    }

    /**
     * 获取 CSS代码
     *
     * @return string
     */
    public function getCss(): string
    {
        if (count($this->css) == 0) {
            return '';
        }

        $html = '';
        $css = array_unique($this->css);
        foreach ($css as $x) {
            $html .= '<link rel="stylesheet" type="text/css" href="' . $x . '" />';
        }

        return $html;
    }

    /**
     * 设置VUE数据
     *
     * @param string $name 名称
     * @param mixed $value 数据
     */
    public function setVueData(string $name, $value)
    {
        if (isset($this->vueData[$name])) {
            $this->vueData[$name] = \Be\Util\Arr::merge($this->vueData[$name], $value);
        } else {
            $this->vueData[$name] = $value;
        }
    }

    /**
     * 获取 VUE 数据
     *
     * @param bool $append 是否附加逗号
     * @return string
     */
    public function getVueData(bool $append = true): string
    {
        if (count($this->vueData) == 0) {
            return '';
        }

        $code = '';
        foreach ($this->vueData as $k => $v) {
            $code .= ',' . $k . ':' . json_encode($v);
        }

        if (!$append) {
            $code = substr($code, 1);
        }

        return $code;
    }

    /**
     * 设置VUE方法
     *
     * @param string $name VUE方法名称
     * @param string $code 代码内容
     */
    public function setVueMethod(string $name, string $code)
    {
        $this->vueMethods[$name] = $code;
    }

    /**
     * 获取VUE方法列表，含方法名
     *
     * @param bool $append 是否附加逗号
     * @return string
     */
    public function getVueMethods(bool $append = true): string
    {
        if (count($this->vueMethods) == 0) {
            return '';
        }

        $code = '';
        foreach ($this->vueMethods as $k => $v) {
            $code .= ',' . $k . ':' . $v;
        }

        if (!$append) {
            $code = substr($code, 1);
        }

        return $code;
    }

    /**
     * 设置VUE钩子
     *
     * @param string $name VUE钩子名称
     * @param string $code 代码内容
     */
    public function setVueHook(string $name, string $code)
    {
        if (isset($this->vueHooks[$name])) {
            $this->vueHooks[$name] .= "\r\n" . $code;
        } else {
            $this->vueHooks[$name] = $code;
        }
    }

    /**
     * 获取VUE钩子方法代码，不含方法名
     *
     * @param string $name VUE钩子名称
     * @return string
     */
    public function getVueHook(string  $name): string
    {
        if (isset($this->vueHooks[$name])) {
            return $this->vueHooks[$name];
        }

        return '';
    }

    /**
     * 获取多个VUE钩子方法，含方法名
     *
     * @param string[] $names 钩子名称
     * @return string
     */
    public function getVueHooks(string ...$names): string
    {
        if (count($this->vueHooks) == 0) {
            return '';
        }

        $code = '';
        if (count($names) === 0) {
            foreach ($this->vueHooks as $k => $v) {
                $code .= ',' . $k . ': function () {' . $v . '}';
            }
        } else {
            foreach ($names as $name) {
                if (isset($this->vueHooks[$name])) {
                    $code .= ',' . $name . ': function () {' . $this->vueHooks[$name] . '}';
                }
            }
        }

        return $code;
    }

}