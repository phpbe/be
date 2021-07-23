<?php

namespace Be\AdminPlugin\Tab;

use Be\AdminPlugin\AdminPluginException;

/**
 * 选项卡
 */
class Driver
{
    public $name = null; // 键名
    public $label = ''; // 配置项中文名称
    public $value = ''; // 值
    public $keyValues = null; // 可选值键值对
    public $ui = []; // UI界面参数

    public $newValue = null; // 新值

    /**
     * 构造函数
     *
     * @param array $params 参数
     * @throws AdminPluginException
     */
    public function __construct($params = [])
    {
        if (!isset($params['name'])) {
            throw new AdminPluginException('选项卡参数 name 缺失');
        }

        $name = $params['name'];
        if ($name  instanceof \Closure) {
            $this->name = $name();
        } else {
            $this->name = $name;
        }

        if (isset($params['label'])) {
            $label = $params['label'];
            if ($label instanceof \Closure) {
                $this->label = $label();
            } else {
                $this->label = $label;
            }
        }

        if (isset($params['value'])) {
            $value = $params['value'];
            if ($value instanceof \Closure) {
                $this->value = (string)$value();
            } else {
                $this->value = (string)$value;
            }
        }

        if (isset($params['keyValues'])) {
            $keyValues = $params['keyValues'];
            if ($keyValues instanceof \Closure) {
                $this->keyValues = $keyValues();
            } else {
                $this->keyValues = $keyValues;
            }
        } else {
            if (isset($params['values'])) {
                $values = $params['values'];
                if ($values instanceof \Closure) {
                    $values = $values();
                }

                $keyValues = [];
                foreach ($values as $value) {
                    $keyValues[$value] = $value;
                }
                $this->keyValues = $keyValues;
            }
        }

        if ($this->keyValues === null) {
            throw new AdminPluginException('选项卡参数 keyValues 缺失');
        }

        if (isset($params['ui'])) {
            $ui = $params['ui'];
            if ($ui instanceof \Closure) {
                $this->ui = $ui();
            } else {
                $this->ui = $ui;
            }
        }

        if (!isset($this->ui['type'])) {
            $this->ui['type'] = 'card';
        }

        if (!isset($this->ui['@tab-click'])) {
            $this->ui['@tab-click'] = 'tabClick';
        }

        $this->ui['v-model'] = 'formData.' . $this->name;

    }

    /**
     * 获取html内容
     *
     * @return string | array
     */
    public function getHtml()
    {
        $html = '<el-tabs';
        foreach ($this->ui as $k => $v) {
            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';

        foreach ($this->keyValues as $key => $val) {
            $html .= '<el-tab-pane label="' . $val . '" name="' . $key . '"></el-tab-pane>';
        }

        $html .= '</el-tabs>';
        return $html;
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
        return [
            'tabClick' => 'function (tab, event) {
                this.formData.'.$this->name.' = tab.name;
                this.gotoPage(1);
            }',
        ];
    }

    /**
     * 提交处理
     *
     * @param $data
     * @throws \Exception
     */
    public function submit($data)
    {
        if (isset($data[$this->name])) {
            $this->newValue = $data[$this->name];
        }
    }

}
