<?php

namespace Be\AdminPlugin\Form\Item;

/**
 * 表单项 树
 */
class FormItemTree extends FormItem
{

    protected $valueType = 'array(string)';

    protected $treeData = [];


    /**
     * 构造函数
     *
     * @param array $params 参数
     * @param array $row 数据对象
     */
    public function __construct($params = [], $row = [])
    {
        parent::__construct($params, $row);

        if (isset($params['treeData'])) {
            $treeData = $params['treeData'];
            if ($treeData instanceof \Closure) {
                $treeData = $treeData($row);
            }

            $this->treeData = $treeData;
        }

        if ($this->required) {
            if (!isset($this->ui['form-item'][':rules'])) {
                $this->ui['form-item'][':rules'] = '[{required: true, message: \'请选择' . $this->label . '\', trigger: \'change\' }]';
            }
        }

        if ($this->disabled) {
            $this->treeData = $this->treeDataDisabled($this->treeData);
        }

        if (!isset($this->ui['node-key'])) {
            $this->ui['node-key'] = 'key';
        }

        $this->ui[':data'] = 'formItems.' . $this->name . '.treeData';
        $this->ui[':default-checked-keys'] = 'formItems.' . $this->name . '.value';
        $this->ui['show-checkbox'] = null;
        $this->ui['@check'] = 'formItemTree_' . $this->name.'_check';
    }


    protected function treeDataDisabled($treeData) {
        $treeDataDisabled = [];
        foreach ($treeData as $x) {
            if (isset($x['children']) && count($x['children']) > 0) {
                $x['children'] = $this->treeDataDisabled($x['children']);
            }
            $x['disabled'] = 'true';
            $treeDataDisabled[] = $x;
        }
        return $treeDataDisabled;
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

        $html .= '<el-tree';
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
        $html .= '</el-tree>';

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
                    'treeData' => $this->treeData,
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
            'formItemTree_' . $this->name . '_check' => 'function(node, data) {
                this.formData.' . $this->name . ' = JSON.stringify(data.checkedKeys);
            }',
        ];
    }

}

