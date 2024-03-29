<?php

namespace Be\AdminPlugin\Table\Item;


/**
 * 字段 编号
 */
class TableItemIndex extends TableItem
{

    /**
     * 构造函数
     *
     * @param array $params 参数
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->ui['table-column']['type'] = 'index';

        if (!isset($this->ui['table-column']['width'])) {
            $this->ui['table-column']['width'] = '50';
        }
    }

    /**
     * 获取html内容
     *
     * @return string
     */
    public function getHtml(): string
    {
        $html = '<el-table-column';
        if (isset($this->ui['table-column'])) {
            foreach ($this->ui['table-column'] as $k => $v) {
                if ($v === null) {
                    $html .= ' ' . $k;
                } else {
                    $html .= ' ' . $k . '="' . $v . '"';
                }
            }
        }
        $html .= '>';
        $html .= '</el-table-column>';

        return $html;
    }
}
