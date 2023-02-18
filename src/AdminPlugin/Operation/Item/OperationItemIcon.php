<?php

namespace Be\AdminPlugin\Operation\Item;


/**
 * 操作项 图标
 */
class OperationItemIcon extends OperationItem
{

    /**
     * 构造函数
     *
     * @param array $params 参数
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        if (!isset($this->ui['@click'])) {
            $this->ui['@click'] = 'operationItemClick(\'' . $this->name . '\', scope.row)';
        }
    }

    /**
     * 获取HTML内容
     *
     * @return string
     */
    public function getHtml(): string
    {
        $html = '';

        if ($this->tooltip !== null) {
            $html .= '<el-tooltip';
            foreach ($this->tooltip as $k => $v) {
                if ($v === null) {
                    $html .= ' ' . $k;
                } else {
                    $html .= ' ' . $k . '="' . $v . '"';
                }
            }
            $html .= '>';
        }

        $html .= '<el-icon';
        if (isset($this->ui)) {
            foreach ($this->ui as $k => $v) {
                if ($v === null) {
                    $html .= ' ' . $k;
                } else {
                    $html .= ' ' . $k . '="' . $v . '"';
                }
            }
        }
        $html .= '>';
        $html .= '</el-icon>';

        if ($this->tooltip !== null) {
            $html .= '</el-tooltip>';
        }

        return $html;
    }
}
