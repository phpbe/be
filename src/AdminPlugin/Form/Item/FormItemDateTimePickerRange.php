<?php

namespace Be\AdminPlugin\Form\Item;

/**
 * 表单项 日期时间范围选择器
 */
class FormItemDateTimePickerRange extends FormItem
{

    protected string $valueType = 'array';

    /**
     * 构造函数
     *
     * @param array $params 参数
     * @param array $row 数据对象
     */
    public function __construct(array $params = [], array $row = [])
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

        if (!isset($this->ui['range-separator'])) {
            $this->ui['range-separator'] = '至';
        }

        if (!isset($this->ui['start-placeholder'])) {
            $this->ui['start-placeholder'] = '开始日期时间';
        }

        if (!isset($this->ui['end-placeholder'])) {
            $this->ui['end-placeholder'] = '结束日期时间';
        }

        if (!isset($this->ui['value-format'])) {
            $this->ui['value-format'] = 'yyyy-MM-dd HH:mm:ss';
        }

        $this->ui['type'] = 'datetimerange';
        $this->ui['@change'] = 'formItemDateTimePickerRange_' . $this->name.'_change';

        if ($this->name !== null) {
            if (!isset($this->ui['v-model'])) {
                $this->ui['v-model'] = 'formItems.' . $this->name.'.value';
            }
        }
    }

    /**
     * 获取html内容
     *
     * @return string
     */
    public function getHtml(): string
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
            $html .= '<div class="be-c-999 be-mt-50 be-lh-150">' . $this->description . '</div>';
        }

        $html .= '</el-form-item>';
        return $html;
    }

    /**
     * 获取 vue data
     *
     * @return false | array
     */
    public function getVueData()
    {
        return [
            'formItems' => [
                $this->name => [
                    'value' => $this->value,
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
        return [
            'formItemDateTimePickerRange_' . $this->name . '_change' => 'function(value) {
                if (value) {
                    this.formData.' . $this->name . ' = JSON.stringify(value);
                } else {
                    this.formData.' . $this->name . ' = "";
                }
            }',
        ];
    }

}

