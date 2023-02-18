<?php

namespace Be\AdminPlugin\Toolbar\Item;


/**
 * 工具栏 链接
 */
class ToolbarItemLink extends ToolbarItem
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
            $this->ui['@click'] = 'toolbarItemClick(\'' . $this->name . '\')';
        }
    }

    /**
     * 获取html内容
     *
     * @return string
     */
    public function getHtml(): string
    {
        $html = '<el-link';
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

        return $html;
    }
}
