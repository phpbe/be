<?php

namespace Be\AdminPlugin\Card\Item;


/**
 * 字段 文本
 */
class CardItemText extends CardItem
{


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
        $html .= '{{item.'.$this->name.'}}';
        $html .= '</div>';

        return $html;
    }


}
