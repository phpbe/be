<?php

namespace Be\AdminPlugin\Form\Item;

use Be\AdminPlugin\AdminPluginException;

/**
 * 表单项 混合体数组
 */
class FormItemsObjects extends FormItems
{

    private $resize = true;
    private $minSize = 0;
    private $maxSize = 0;

    /**
     * 构造函数
     *
     * @param array $params 参数
     * @param array $row 数据对象
     */
    public function __construct($params = [], $row = [])
    {
        parent::__construct($params, $row);

        if (isset($params['resize'])) {
            $this->resize = $params['resize'];
        }

        if (isset($params['minSize'])) {
            $this->minSize = $params['minSize'];
        }

        if (isset($params['maxSize'])) {
            $this->maxSize = $params['maxSize'];
        }

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
    public function getHtml()
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

        $html .= '<el-card class="box-card" shadow="hover" style="margin-bottom: 10px;" v-for="(formItemsObjectsItem, formItemsObjectsIndex) in formData.' . $this->name . '">';

        $html .= '<template slot="header">';
        $html .= '<span>' . $this->label . ' - {{formItemsObjectsIndex+1}}</span>';
        $html .= '<el-button type="danger" icon="el-icon-remove" style="float: right;" @click.prevent="FormItemsObjects_remove(\'' . $this->name . '\', formItemsObjectsIndex)">删除</el-button>';
        $html .= '</template>';

        foreach ($this->items as $item) {
            if (isset($item['name'])) {
                //$item['ui'][':prop'] = '\'formItemsObjectsItem.\' + formItemsObjectsIndex + \'.' . $item['name'] . '\'';
                $item['ui']['v-model'] = 'formItemsObjectsItem.' . $item['name'];
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
            $driver = new $driverClass($item);

            $html .= $driver->getHtml();

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

        $html .= '</el-card>';

        if ($this->resize) {
            $html .= '<el-button type="primary" icon="el-icon-plus" @click="FormItemsObjects_' . $this->name . '_add">新增</el-button>';
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
            foreach ($data[$this->name] as $d) {
                $newValueX = [];
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
                    $driver = new $driverClass($item, $d);

                    if ($driver->name) {
                        $driver->submit($d);
                        $newValueX[$driver->name] = $driver->newValue;
                    }
                }
                $newValue[] = $newValueX;
            }
            $this->newValue = $newValue;
        } else {
            $this->newValue = $this->nullValue;
        }
    }

    /**
     * 获取 vue data
     *
     * @return false | array
     */
    public function getVueData()
    {
        $vueDataX = [
            'formItems' => [
                $this->name => [
                    'minSize' => $this->minSize,
                ]
            ]
        ];

        \Be\Util\Arr::merge($this->vueData, $vueDataX);

        return $this->vueData;
    }

    /**
     * 获取 vue 方法
     *
     * @return false | array
     */
    public function getVueMethods()
    {
        if ($this->resize) {
            $data = [];
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
                $driver = new $driverClass($item);

                if ($driver->name) {
                    if ($driver->value === null) {
                        $data[$driver->name] = $driver->defaultValue;
                    } else {
                        $data[$driver->name] = $driver->value;
                    }
                }
            }

            $vueMethodsX = [
                'FormItemsObjects_' . $this->name . '_add' => 'function() {
                    ' . ($this->maxSize > 0 ? 'if (this.formData[\'' . $this->name . '\'].length >= ' . $this->maxSize . ') {return;}' : '') . '
                    this.formData[\'' . $this->name . '\'].push(' . json_encode($data) . ');
                }',
                'FormItemsObjects_remove' => 'function(name, index) {
                    if (this.formData[name].length <= this.formItems[name].minSize) {
                        return;
                    }
                    this.formData[name].splice(index, 1)
                }',
            ];

            $this->vueMethods = array_merge($this->vueMethods, $vueMethodsX);
        }

        if (count($this->vueMethods) > 0) {
            return $this->vueMethods;
        }
        return false;
    }


}
