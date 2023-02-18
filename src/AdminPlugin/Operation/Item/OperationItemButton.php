<?php

namespace Be\AdminPlugin\Operation\Item;


/**
 * 操作项 按钮
 */
class OperationItemButton extends OperationItem
{

    /**
     * 构造函数
     *
     * @param array $params 参数
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        if (!isset($this->ui['size'])) {
            $this->ui['size'] = 'mini';
        }

        if (!isset($this->ui['@click'])) {
            $this->ui['@click'] = 'operationItemClick(\'' . $this->name . '\', scope.row)';
        }
    }


    /**
     * 编辑
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

        $html .= '<el-button';
        foreach ($this->ui as $k => $v) {
            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';
        $html .= '<span>' . $this->label . '</span>';
        $html .= '</el-button>';

        if ($this->tooltip !== null) {
            $html .= '</el-tooltip>';
        }

        return $html;
    }
}
