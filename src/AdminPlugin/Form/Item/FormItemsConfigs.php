<?php

namespace Be\AdminPlugin\Form\Item;

use Be\AdminPlugin\AdminPluginException;
use Be\Config\Annotation\BeConfigItem;

/**
 * 表单项 混合体数组
 */
class FormItemsConfigs extends FormItems
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
        if (!isset($params['items'][0])) {
            throw new AdminPluginException('参数' . $this->label . ' (' . $this->name . ') 须指定子项目参数（items）');
        }

        $item = $params['items'][0];

        $className = null;
        if (strpos($item, '\\') !== false) {
            $className = $item;
            $configName = substr($className, strrpos($item, '\\') + 1);
        } else {
            $parts = explode('.', $item);
            if (count($parts) > 3) {
                $type = array_shift($parts);
                $catalog = array_shift($parts);
                $className = '\\Be\\' . $type . '\\' . $catalog . '\\Config\\' . implode('\\', $parts);
                $configName = end($parts);
            }
        }

        if (!$className || !class_exists($className)) {
            throw new AdminPluginException('配置项' . $this->label . '里的类 (' . $item . ') 不存在！');
        }

        $configInstance = new $className();

        $items = [];
        $reflection = new \ReflectionClass($className);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $itemName = $property->getName();
            $itemComment = $property->getDocComment();
            $parseItemComments = \Be\Util\Annotation::parse($itemComment);

            $configItem = null;
            if (isset($parseItemComments['BeConfigItem'][0])) {
                $annotation = new BeConfigItem($parseItemComments['BeConfigItem'][0]);
                $configItem = $annotation->toArray();
                if (isset($configItem['value'])) {
                    $configItem['label'] = $configItem['value'];
                    unset($configItem['value']);
                }
            } else {
                $fn = '_' . $itemName;
                if (is_callable([$configInstance, $fn])) {
                    $configItem = $configInstance->$fn($itemName);
                }
            }

            if ($configItem) {
                $configItem['name'] = $itemName;
                $items[] = $configItem;
            }
        }

        $params['items'] = $items;

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

        $html .= '<el-card class="box-card" shadow="hover" style="margin-bottom: 10px;" v-for="(formItemsConfigsItem, formItemsConfigsIndex) in formData.' . $this->name . '">';

        $html .= '<template slot="header">';
        $html .= '<span>' . $this->label . ' - {{formItemsConfigsIndex+1}}</span>';
        $html .= '<el-button type="danger" icon="el-icon-remove" style="float: right;" @click.prevent="FormItemsConfigs_remove(\'' . $this->name . '\', formItemsConfigsIndex)">删除</el-button>';
        $html .= '</template>';

        foreach ($this->items as $item) {
            if (isset($item['name'])) {
                //$item['ui'][':prop'] = '\'formItemsConfigsItem.\' + formItemsConfigsIndex + \'.' . $item['name'] . '\'';
                $item['ui']['v-model'] = 'formItemsConfigsItem.' . $item['name'];
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
            $html .= '<el-button type="primary" icon="el-icon-plus" @click="FormItemsConfigs_' . $this->name . '_add">新增</el-button>';
        }

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

        $this->vueData = \Be\Util\Arr::merge($this->vueData, $vueDataX);

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
                'FormItemsConfigs_' . $this->name . '_add' => 'function() {
                    ' . ($this->maxSize > 0 ? 'if (this.formData[\'' . $this->name . '\'].length >= ' . $this->maxSize . ') {return;}' : '') . '
                    this.formData[\'' . $this->name . '\'].push(' . json_encode($data) . ');
                }',
                'FormItemsConfigs_remove' => 'function(name, index) {
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
