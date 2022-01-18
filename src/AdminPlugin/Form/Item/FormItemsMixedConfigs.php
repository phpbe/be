<?php

namespace Be\AdminPlugin\Form\Item;

use Be\AdminPlugin\AdminPluginException;
use Be\Config\Annotation\BeConfig;
use Be\Config\Annotation\BeConfigItem;

/**
 * 表单项 混合体配置对象
 */
class FormItemsMixedConfigs extends FormItems
{

    private $resize = true;
    private $minSize = 0;
    private $maxSize = 0;
    private $labelNewItem = '新增';

    /**
     * 构造函数
     *
     * @param array $params 参数
     * @param array $row 数据对象
     */
    public function __construct($params = [], $row = [])
    {
        if (!isset($params['items'])) {
            throw new AdminPluginException('参数' . $this->label . ' (' . $this->name . ') 须指定子项目参数（items）');
        }

        $items = [];
        foreach ($params['items'] as $item) {
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

            $item = [];
            $reflection = new \ReflectionClass($className);
            $classComment = $reflection->getDocComment();
            $parseClassComments = \Be\Util\Annotation::parse($classComment);
            if (isset($parseClassComments['BeConfig'][0])) {
                $annotation = new BeConfig($parseClassComments['BeConfig'][0]);
                $config = $annotation->toArray();
                if (isset($config['value'])) {
                    $item['label'] = $config['value'];
                } else {
                    $item['label'] = $configName;
                }
                $item['name'] = $configName;
            }

            $subItems = [];
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
                    $subItems[] = $configItem;
                }
            }

            $item['items'] = $subItems;
            $items[] = $item;
        }

        $params['items'] = $items;

       //print_r($params);
        //exit;

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

        if (isset($params['labelNewItem'])) {
            $this->labelNewItem = $params['labelNewItem'];
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

        $html .= '<el-card class="box-card" shadow="hover" style="margin-bottom: 10px;" v-for="(formItemsMixedConfigsItem, formItemsMixedConfigsIndex) in formData.' . $this->name . '">';

        $html .= '<template slot="header">';
        foreach ($this->items as $item) {
            $html .= '<span v-if="formItemsMixedConfigsItem.name === \'' . $item['name'] . '\'">' . $this->label . ' - {{formItemsMixedConfigsIndex+1}} - ';
            $html .= $item['label'];
            $html .= '</span>';
        }
        $html .= '<el-button type="danger" icon="el-icon-remove" style="float: right;" @click.prevent="FormItemsMixedConfigs_remove(\'' . $this->name . '\', formItemsMixedConfigsIndex)">删除</el-button>';
        $html .= '</template>';

        foreach ($this->items as $item) {
            $html .= '<template v-if="formItemsMixedConfigsItem.name === \''.$item['name'].'\'">';
            foreach ($item['items'] as $itemX) {
                if (isset($itemX['name'])) {
                    //$itemX['ui'][':prop'] = '\'formItemsMixedConfigsItem.\' + formItemsMixedConfigsIndex + \'.' . $itemX['name'] . '\'';
                    $itemX['ui']['v-model'] = 'formItemsMixedConfigsItem.data.' . $itemX['name'];
                }

                $driverClass = null;
                if (isset($itemX['driver'])) {
                    if (substr($itemX['driver'], 0, 8) === 'FormItem') {
                        $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $itemX['driver'];
                    } else {
                        $driverClass = $itemX['driver'];
                    }
                } else {
                    $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
                }
                $driver = new $driverClass($itemX);

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
            $html .= ' </template>';
        }

        $html .= '</el-card>';

        if ($this->resize) {
            $html .= '<el-dropdown @command="FormItemsMixedConfigs_' . $this->name . '_add">';
            $html .= '<el-button type="primary">';
            $html .= $this->labelNewItem . '<i class="el-icon-arrow-down el-icon--right"></i>';
            $html .= '</el-button>';
            $html .= '<el-dropdown-menu slot="dropdown">';
            foreach ($this->items as $item) {
                $html .= '<el-dropdown-item command="'.$item['name'].'">'.$item['label'].'</el-dropdown-item>';
            }
            $html .= '</el-dropdown-menu>';
            $html .= '</el-dropdown>';
        }

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
            foreach ($data[$this->name] as $d) {
                foreach ($this->items as $item) {
                    if ($d['name'] === $item['name']) {
                        $dataX = [];
                        foreach ($item['items'] as $itemX) {
                            $driverClass = null;
                            if (isset($itemX['driver'])) {
                                if (substr($itemX['driver'], 0, 8) === 'FormItem') {
                                    $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $itemX['driver'];
                                } else {
                                    $driverClass = $itemX['driver'];
                                }
                            } else {
                                $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
                            }
                            $driver = new $driverClass($itemX, $d['data']);

                            if ($driver->name) {
                                $driver->submit($d['data']);
                                $dataX[$driver->name] = $driver->newValue;
                            }
                        }
                        $newValue[] = [
                            'name' => $item['name'],
                            'data' => $dataX,
                        ];
                        break;
                    }
                }
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
                $dataX = [];
                foreach ($item['items'] as $itemX) {
                    $driverClass = null;
                    if (isset($itemX['driver'])) {
                        if (substr($itemX['driver'], 0, 8) === 'FormItem') {
                            $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $itemX['driver'];
                        } else {
                            $driverClass = $itemX['driver'];
                        }
                    } else {
                        $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
                    }
                    $driver = new $driverClass($itemX);

                    if ($driver->name) {
                        if ($driver->value === null) {
                            $dataX[$driver->name] = $driver->defaultValue;
                        } else {
                            $dataX[$driver->name] = $driver->value;
                        }
                    }
                }

                $data[$item['name']] = [
                    'name' => $item['name'],
                    'data' => $dataX,
                ];
            }

            $vueMethodsX = [
                'FormItemsMixedConfigs_' . $this->name . '_add' => 'function(command) {
                    ' . ($this->maxSize > 0 ? 'if (this.formData[\'' . $this->name . '\'].length >= ' . $this->maxSize . ') {return;}' : '') . '
                    var data = '.json_encode($data).';
                    this.formData[\'' . $this->name . '\'].push(data[command]);
                }',
                'FormItemsMixedConfigs_remove' => 'function(name, index) {
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
