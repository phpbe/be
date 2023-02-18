<?php

namespace Be\AdminPlugin\Card\Item;


/**
 * 字段 图片
 */
class CardItemImage extends CardItem
{


    /**
     * 构造函数
     *
     * @param array $params 参数
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        if (!isset($this->ui[':src'])) {
            $this->ui[':src'] = 'item.'.$this->name.'';
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
    public function getHtml(): string
    {
        $html = '<div class="card-item">';
        if ($this->label) {
            $html .= '<b>' . $this->label . '</b>：';
        }
        $html .= '<el-image';
        foreach ($this->ui as $k => $v) {
            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';
        $html .= '</el-image>';
        $html .= '</div>';

        return $html;
    }

}
