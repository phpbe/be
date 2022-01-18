<?php

namespace Be\AdminPlugin\Form\Item;

use Be\AdminPlugin\AdminPluginException;
use Be\Config\Annotation\BeConfigItem;

/**
 * 表单项 混合体
 */
class FormItemsConfig extends FormItems
{

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

        $html .= '<el-card class="box-card" shadow="hover">';
        $value = [];
        foreach ($this->items as $item) {

            if (isset($item['name'])) {
                $item['ui']['v-model'] = 'formData.' . $this->name . '.' . $item['name'];
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
            $html .= '<div class="be-c-999 be-mt-20 be-lh-150">' . $this->description . '</div>';
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
