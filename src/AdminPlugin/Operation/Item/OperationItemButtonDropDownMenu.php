<?php

namespace Be\AdminPlugin\Operation\Item;

/**
 * 操作项 下拉菜单 其单项
 */
class OperationItemButtonDropDownMenu extends OperationItem
{

    /**
     * 构造函数
     *
     * @param array $params 参数
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->ui[':command'] = 'operationItemButtonDropDownMenuCommand(\''.$params['parentName'].'\','.$params['index'].', scope.row)';
    }

    /**
     * 获取html内容
     *
     * @return string
     */
    public function getHtml()
    {
        $html = '<el-dropdown-item';
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

        $html .= $this->label;
        $html .= '</el-dropdown-item>';

        return $html;
    }

}


