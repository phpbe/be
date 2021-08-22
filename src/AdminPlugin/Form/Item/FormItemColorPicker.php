<?php

namespace Be\AdminPlugin\Form\Item;

/**
 * 表单项 颜色选择器
 */
class FormItemColorPicker extends FormItem
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
                $this->ui['form-item'][':rules'] = '[{required: true, message: \'请选择' . $this->label . '\', trigger: \'change\' }]';
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
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';


        $html .= '<div style="display: flex">';
        $html .= '<div style="flex: 0 0 40px; height: 32px;">';
        $html .= '<el-color-picker';
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
        $html .= '</el-color-picker>';
        $html .= '</div>';
        $html .= '<div style="flex: 1 1 auto;">';
        $html .= '<el-input v-model="' . $this->ui['v-model'] . '" style="width:120px;">';
        $html .= '</el-input>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</el-form-item>';
        return $html;
    }

}
