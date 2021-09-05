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
        $html = '<div class="">';
        $html .= $this->label . ' ';
        $html .= '{{item.'.$this->name.'}}';
        $html .= '</div>';

        return $html;
    }


}
