<?php

namespace Be\AdminPlugin\Form\Item;

use Be\AdminPlugin\AdminPluginException;

/**
 * 表单项 混合体
 */
class FormItemsObject extends FormItems
{

    /**
     * 构造函数
     *
     * @param array $params 参数
     * @param array $row 数据对象
     */
    public function __construct(array $params = [], array $row = [])
    {
        parent::__construct($params, $row);

        if ($this->name !== null) {
            if (!isset($this->ui['v-model'])) {
                $this->ui['v-model'] = 'formData.' . $this->name;
            }
        }
    }


    /**
     * 获取html内容ß
     *
     * @return string
     */
    public function getHtml(): string
    {
        $html = '<el-form-item';
        foreach ($this->ui['form-item'] as $k => $v) {
            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';

        $html .= '<el-card class="box-card" shadow="hover">';
        $value = [];
        foreach ($this->items as $item) {

            if (isset($item['name'])) {
                $item['ui']['v-model'] = 'formData.' . $this->name . '.' . $item['name'];
                $item['value'] = $this->value[$item['name']];
            }

            $driverClass = null;
            if (isset($item['driver'])) {
                if (substr($item['driver'], 0, 8) === 'FormItem') {
                    $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $item['driver'];
                } else {
                    $driverClass = $item['driver'];
                }
            } else {
                $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
            }
            $driver = new $driverClass($item, $this->value);

            $html .= $driver->getHtml();

            if ($driver->name !== null) {
                $value[$driver->name] = $driver->getValueString();
            }

            $jsX = $driver->getJs();
            if ($jsX) {
                $this->js = array_merge($this->js, $jsX);
            }

            $cssX = $driver->getCss();
            if ($cssX) {
                $this->css = array_merge($this->css, $cssX);
            }

            $vueDataX = $driver->getVueData();
            if ($vueDataX) {
                $this->vueData = \Be\Util\Arr::merge($this->vueData, $vueDataX);
            }

            $vueMethodsX = $driver->getVueMethods();
            if ($vueMethodsX) {
                $this->vueMethods = array_merge($this->vueMethods, $vueMethodsX);
            }

            $vueHooksX = $driver->getVueHooks();
            if ($vueHooksX) {
                foreach ($vueHooksX as $k => $v) {
                    if (isset($vueHooks[$k])) {
                        $vueHooks[$k] .= "\r\n" . $v;
                    } else {
                        $vueHooks[$k] = $v;
                    }
                }

                $this->vueHooks = array_merge($this->vueHooks, $vueMethodsX);
            }

        }

        $this->value = $value;

        $html .= '</el-card>';

        if ($this->description) {
            $html .= '<div class="be-c-999 be-mt-50 be-lh-150">' . $this->description . '</div>';
        }

        $html .= '</el-form-item>';
        return $html;
    }

    /**
     * 提交处理
     *
     * @param $data
     * @throws AdminPluginException
     */
    public function submit($data)
    {
        if (isset($data[$this->name]) && $data[$this->name] !== $this->nullValue) {
            $newValue = [];
            foreach ($this->items as $item) {

                $driverClass = null;
                if (isset($item['driver'])) {
                    if (substr($item['driver'], 0, 8) === 'FormItem') {
                        $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $item['driver'];
                    } else {
                        $driverClass = $item['driver'];
                    }
                } else {
                    $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
                }
                $driver = new $driverClass($item, $data[$this->name]);

                if ($driver->name) {
                    $driver->submit($data[$this->name]);
                    $newValue[$driver->name] = $driver->newValue;
                }
            }
            $this->newValue = $newValue;
        } else {
            $this->newValue = $this->nullValue;
        }


    }

}
