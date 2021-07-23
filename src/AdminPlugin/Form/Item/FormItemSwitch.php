<?php

namespace Be\AdminPlugin\Form\Item;

/**
 * 表单项 开关
 */
class FormItemSwitch extends FormItem
{

    public $valueType = 'int';

    /**
     * 构造函数
     *
     * @param array $params 参数
     * @param array $row 数据对象
     */
    public function __construct($params = [], $row = [])
    {
        parent::__construct($params, $row);

        if (!$this->value) {
            $this->value = '0';
        } else {
            $this->value = (string)$this->value;
        }

        if ($this->disabled) {
            if (!isset($this->ui['disabled'])) {
                $this->ui['disabled'] = 'true';
            }
        }

        if (!isset($this->ui['active-value'])) {
            $this->ui['active-value'] = 1;
        }

        if (!isset($this->ui['inactive-value'])) {
            $this->ui['inactive-value'] = 0;
        }

        $this->ui['v-model'] = 'formData.' . $this->name;
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

        $html .= '<el-switch';
        foreach ($this->ui as $k => $v) {
            if ($k == 'form-item') {
                continue;
            }

            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';
        $html .= '</el-switch>';

        $html .= '</el-form-item>';
        return $html;
    }


}
