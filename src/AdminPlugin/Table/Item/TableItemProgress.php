<?php

namespace Be\AdminPlugin\Table\Item;


/**
 * 字段 进度条
 */
class TableItemProgress extends TableItem
{

    /**
     * 构造函数
     *
     * @param array $params 参数
     */
    public function __construct($params = [])
    {
        parent::__construct($params);

        if (!isset($this->ui[':percentage'])) {
            $this->ui[':percentage'] = 'scope.row.'.$this->name;
        }

        if (!isset($this->ui[':stroke-width'])) {
            $this->ui[':stroke-width'] = '16';
        }

        if (!isset($this->ui[':text-inside'])) {
            $this->ui[':text-inside'] = 'true';
        }

        if (!isset($this->ui[':status'])) {
            $this->ui[':status'] = 'scope.row.'.$this->name.'==100?\'success\':\'primary\'';
        }

        if ($this->url) {
            if (!isset($this->ui['@click'])) {
                $this->ui['@click'] = 'tableItemClick(\'' . $this->name . '\', scope.row)';
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
        $html .= '<template slot-scope="scope">';
        $html .= '<el-progress';
        foreach ($this->ui as $k => $v) {
            if ($k === 'table-column') {
                continue;
            }

            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';
        $html .= '</el-progress>';
        $html .= '</template>';
        $html .= '</el-table-column>';

        return $html;
    }

}
