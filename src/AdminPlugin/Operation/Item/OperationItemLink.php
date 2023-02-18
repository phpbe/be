<?php

namespace Be\AdminPlugin\Operation\Item;


/**
 * 搜索项 布尔值
 */
class OperationItemLink extends OperationItem
{

    /**
     * 构造函数
     *
     * @param array $params 参数
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        if (!isset($this->ui['type'])) {
            if (isset($params['type'])) {
                $this->ui['type'] = $params['type'];
            } else {
                $this->ui['type'] = 'primary';
            }
        }

        if (!isset($this->ui['icon']) && isset($params['icon'])) {
            $this->ui['icon'] = $params['icon'];
        }

        if (isset($this->ui['href'])) {
            unset($this->ui['href']);
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

        $html .= '<el-link';
        foreach ($this->ui as $k => $v) {
            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';
        $html .= $this->label;
        $html .= '</el-link>';

        if ($this->tooltip !== null) {
            $html .= '</el-tooltip>';
        }

        return $html;
    }

}
