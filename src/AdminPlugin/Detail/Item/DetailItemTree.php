<?php

namespace Be\AdminPlugin\Detail\Item;


/**
 * 明细 树
 */
class DetailItemTree extends DetailItem
{

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
            $treeData = $this->treeDataDisabled($treeData);
            $this->treeData = $treeData;
        }

        if (!isset($this->ui['tree']['node-key'])) {
            $this->ui['tree']['node-key'] = 'key';
        }

        $this->ui['tree'][':data'] = 'detailItems.' . $this->name . '.treeData';
        $this->ui['tree'][':default-checked-keys'] = 'detailItems.' . $this->name . '.value';
        $this->ui['tree']['show-checkbox'] = null;
        $this->ui['tree']['default-expand-all'] = null;
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
        if (isset($this->ui['tree'])) {
            foreach ($this->ui['tree'] as $k => $v) {
                if ($v === null) {
                    $html .= ' ' . $k;
                } else {
                    $html .= ' ' . $k . '="' . $v . '"';
                }
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
            'detailItems' => [
                $this->name => [
                    'value' => $this->value,
                    'treeData' => $this->treeData,
                ]
            ]
        ];
    }


}
