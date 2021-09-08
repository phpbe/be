<?php

namespace Be\AdminPlugin\Card\Item;


/**
 * 字段 标签
 */
class CardItemTag extends CardItem
{

    /**
     * 构造函数
     *
     * @param array $params 参数
     */
    public function __construct($params = [])
    {
        parent::__construct($params);

        if (!isset($this->ui['size'])) {
            $this->ui['size'] = isset($params['size']) ? $params['size'] : 'mini';
        }

        if ($this->url) {
            if (!isset($this->ui['@click'])) {
                $this->ui['@click'] = 'cardItemClick(\'' . $this->name . '\', item)';
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
        $html = '<div class="card-item">';
        if ($this->label) {
            $html .= '<b>' . $this->label . '</b>：';
        }
        $html .= '<el-tag';
        foreach ($this->ui as $k => $v) {
            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';
        $html .= '{{item.'.$this->name.'}}';
        $html .= '</el-tag>';
        $html .= '</div>';

        return $html;
    }
}
