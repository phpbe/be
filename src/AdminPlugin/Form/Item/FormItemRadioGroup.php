<?php

namespace Be\AdminPlugin\Form\Item;

/**
 * 表单项 单选框
 */
class FormItemRadioGroup extends FormItem
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

        $html .= '<el-radio-group';
        foreach ($this->ui as $k => $v) {
            if ($k === 'form-item' || $k === 'radio') {
                continue;
            }

            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';

        foreach ($this->keyValues as $key => $val) {
            $html .= '<el-radio';
            if (isset($this->ui['radio'])) {
                foreach ($this->ui['radio'] as $k => $v) {
                    if ($v === null) {
                        $html .= ' ' . $k;
                    } else {
                        $html .= ' ' . $k . '="' . $v . '"';
                    }
                }
            }

            $html .= ' label="'. $key .'"';
            $html .= '>';
            $html .= $val;
            $html .= '</el-radio>';
        }

        $html .= '</el-radio-group>';

        if ($this->description) {
            $html .= '<div class="be-c-999">' . $this->description . '</div>';
        }

        $html .= '</el-form-item>';
        return $html;
    }

}

