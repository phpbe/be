<?php

namespace Be\AdminPlugin\Detail\Item;

/**
 * 明细驱动
 */
abstract class DetailItem
{

    protected $name = ''; // 键名
    protected string $label = ''; // 配置项中文名称
    protected $value = ''; // 值
    protected $keyValues = null; // 可选值键值对
    protected $ui = []; // UI界面参数

    protected static $nameIndex = 0;

    /**
     * 构造函数
     *
     * @param array $params 参数
     * @param array $row 数据对象
     */
    public function __construct($params = [], $row = [])
    {
        if (isset($params['name'])) {
            $this->name = $params['name'];
        }

        if (isset($params['label'])) {
            $label = $params['label'];
            if ($label instanceof \Closure) {
                $this->label = $label($row);
            } else {
                $this->label = $label;
            }
        }

        if (isset($params['value'])) {
            $value = $params['value'];
            if ($value instanceof \Closure) {
                $this->value = $value($row);
            } else {
                $this->value = $value;
            }
        }

        if (isset($params['keyValues'])) {
            $keyValues = $params['keyValues'];
            if ($keyValues instanceof \Closure) {
                $this->keyValues = $keyValues($row);
            } else {
                $this->keyValues = $keyValues;
            }
        }

        if (isset($params['ui'])) {
            $ui = $params['ui'];
            if ($ui instanceof \Closure) {
                $this->ui = $ui($row);
            } else {
                $this->ui = $ui;
            }
        }

        if (!isset($this->ui['form-item']['label'])) {
            $this->ui['form-item']['label'] = htmlspecialchars($this->label);
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
     * 获取需要引入的 CSS 文件
     *
     * @return false | array
     */
    public function getCss()
    {
        return false;
    }

    /**
     * 获取HTML内容
     *
     * @return string
     */
    public function getHtml()
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
        $value = $this->value;
        if ($this->keyValues !== null && is_array($this->keyValues)) {
            if (isset($keyValues[$value])) {
                $value = $keyValues[$value];
            } else {
                $value = '';
            }
        }

        return [
            'detailItems' => [
                $this->name => [
                    'value' => $value,
                ]
            ]
        ];
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

    public function __get($property)
    {
        if (isset($this->$property)) {
            return ($this->$property);
        } else {
            return null;
        }
    }

    /**
     * 获取值的字符形式
     *
     * @return string
     */
    public function getValueString()
    {
        if (is_array($this->value) || is_object($this->value)) {
            return json_encode($this->value);
        }
        return $this->value;
    }

    /**
     * 获取新值的字符形式
     *
     * @return string
     */
    public function getNewValueString()
    {
        if (is_array($this->newValue) || is_object($this->newValue)) {
            return json_encode($this->newValue);
        }
        return $this->newValue;
    }

}
