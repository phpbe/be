<?php

namespace Be\AdminPlugin\Toolbar\Item;


/**
 * 工具栏 下拉菜单 其单项
 */
class ToolbarItemButtonDropDownMenu extends ToolbarItem
{

    /**
     * 构造函数
     *
     * @param array $params 参数
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->ui[':command'] = 'toolbarItemButtonDropDownMenuCommand(\''.$params['parentName'].'\','.$params['index'].')';
    }

    /**
     * 获取html内容
     *
     * @return string
     */
    public function getHtml(): string
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


