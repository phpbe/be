<?php

namespace Be\AdminPlugin\UiItem;

/**
 * UI项管理
 *
 */
class UiItems
{

    //private $this = [];

    private $js = [];
    private $jsCode = [];
    private $css = [];
    private $cssCode = [];
    private $vueData = [];
    private $vueMethods = [];
    private $vueHooks = [];

    public function add(UiItem $uiItem)
    {
        //$this->uiItems[] = $uiItem;

        $js = $uiItem->getJs();
        if ($js) {
            $this->js = array_merge($this->js, $js);
        }

        $jsCode = $uiItem->getJsCode();
        if ($jsCode) {
            $this->jsCode[] = $jsCode;
        }

        $css = $uiItem->getCss();
        if ($css) {
            $this->css = array_merge($this->css, $css);
        }

        $cssCode = $uiItem->getCssCode();
        if ($cssCode) {
            $this->cssCode[] = $cssCode;
        }

        $vueData = $uiItem->getVueData();
        if ($vueData) {
            $this->vueData = \Be\Util\Arr::merge($this->vueData, $vueData);
        }

        $vueMethods = $uiItem->getVueMethods();
        if ($vueMethods) {
            $this->vueMethods = array_merge($this->vueMethods, $vueMethods);
        }

        $vueHooks = $uiItem->getVueHooks();
        if ($vueHooks) {
            foreach ($vueHooks as $k => $v) {
                if (isset($this->vueHooks[$k])) {
                    $this->vueHooks[$k] .= "\r\n" . $v;
                } else {
                    $this->vueHooks[$k] = $v;
                }
            }
        }
    }

    /**
     * 通过setting参数批量设置
     *
     * @param array $setting
     * @return void
     */
    public function setting(array $setting = []) {
        if (isset($setting['js'])) {
            if (is_array($setting['js'])) {
                foreach ($setting['js'] as $v) {
                    $this->addJs($v);
                }
            } else {
                $this->addJs($setting['js']);
            }
        }

        if (isset($setting['jsCode'])) {
            if (is_array($setting['jsCode'])) {
                foreach ($setting['jsCode'] as $v) {
                    $this->addJsCode($v);
                }
            } else {
                $this->addJsCode($setting['js']);
            }
        }

        if (isset($setting['css'])) {
            if (is_array($setting['css'])) {
                foreach ($setting['css'] as $v) {
                    $this->addCss($v);
                }
            } else {
                $this->addCss($setting['js']);
            }
        }

        if (isset($setting['cssCode'])) {
            if (is_array($setting['cssCode'])) {
                foreach ($setting['cssCode'] as $v) {
                    $this->addCssCode($v);
                }
            } else {
                $this->addCssCode($setting['cssCode']);
            }
        }

        if (isset($setting['vueData'])) {
            foreach ($setting['vueData'] as $k => $v) {
                $this->setVueData($k, $v);
            }
        }

        if (isset($setting['vueMethods'])) {
            foreach ($setting['vueMethods'] as $k => $v) {
                $this->setVueMethod($k, $v);
            }
        }

        if (isset($setting['vueHooks'])) {
            foreach ($setting['vueHooks'] as $k => $v) {
                $this->setVueHook($k, $v);
            }
        }
    }

    /**
     * 添加 JS库
     */
    public function addJs($js)
    {
        $this->js[] = $js;
    }

    /**
     * 添加 JS代码
     */
    public function addJsCode($jsCode)
    {
        $this->jsCode[] = $jsCode;
    }

    /**
     * 获取 JS
     *
     * @return string
     */
    public function getJs(): string
    {
        $html = '';
        if (count($this->js) > 0) {
            $js = array_unique($this->js);
            foreach ($js as $x) {
                $html .= '<script src="' . $x . '"></script>';
            }
        }

        if (count($this->jsCode) > 0) {
            $html .= '<script>' . implode('\n', $this->jsCode). '</script>';
        }

        return $html;
    }

    /**
     * 添加 CSS库
     */
    public function addCss($css)
    {
        $this->css[] = $css;
    }

    /**
     * 添加 CSS代码
     */
    public function addCssCode($cssCode)
    {
        $this->cssCode[] = $cssCode;
    }

    /**
     * 获取 CSS
     *
     * @return string
     */
    public function getCss(): string
    {
        $html = '';
        if (count($this->css) > 0) {
            $css = array_unique($this->css);
            foreach ($css as $x) {
                $html .= '<link rel="stylesheet" type="text/css" href="' . $x . '" />';
            }
        }

        if (count($this->cssCode) == 0) {
            $html .= '<style type="text/css">' . implode('\n', $this->cssCode). '</style>';
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