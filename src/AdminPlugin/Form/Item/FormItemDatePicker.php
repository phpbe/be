<?php

namespace Be\AdminPlugin\Form\Item;

/**
 * 表单项 日期选择器
 */
class FormItemDatePicker extends FormItem
{
    /**
     * 构造函数
     *
     * @param array $params 参数
     * @param array $row 数据对象
     */
    public function __construct($params = [], $row = [])
    {
        parent::__construct($params, $row);

        if ($this->required) {
            if (!isset($this->ui['form-item'][':rules'])) {
                $this->ui['form-item'][':rules'] = '[{required: true, message: \'请选择'.$this->label.'\', trigger: \'change\' }]';
            }
        }

        if ($this->disabled) {
            if (!isset($this->ui['disabled'])) {
                $this->ui['disabled'] = 'true';
            }
        }

        if (!isset($this->ui['placeholder'])) {
            $this->ui['placeholder'] = '选择日期';
        }

        if (!isset($this->ui['value-format'])) {
            $this->ui['value-format'] = 'yyyy-MM-dd';
        }

        $this->ui['type'] = 'date';

        if ($this->name !== null) {
            if (!isset($this->ui['v-model'])) {
                $this->ui['v-model'] = 'formData.' . $this->name;
            }
        }
    }


    /**
     * 获取html内容
     *
     * @return string
     */
    public function getHtml()
    {
        $html = '<el-form-item';
        foreach ($this->ui['form-item'] as $k => $v) {
            if ($v === null) {
                $html .= ' '.$k;
            } else {
                $html .= ' '.$k.'="' . $v . '"';
            }
        }
        $html .= '>';

        $html .= '<el-date-picker';
        foreach ($this->ui as $k => $v) {
            if ($k === 'form-item') {
                continue;
            }

            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';
        $html .= '</el-date-picker>';

        if ($this->description) {
            $html .= '<div class="be-c-999">' . $this->description . '</div>';
        }

        $html .= '</el-form-item>';
        return $html;
    }

}
