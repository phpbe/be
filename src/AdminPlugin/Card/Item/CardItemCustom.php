<?php

namespace Be\AdminPlugin\Card\Item;


/**
 * 字段 自定义
 */
class CardItemCustom extends CardItem
{



    /**
     * 获取html内容
     *
     * @return string
     */
    public function getHtml(): string
    {
        $html = '<div class="card-item">';
        $html .= '<div v-html="item.'.$this->name.'"></div>';
        $html .= '</div>';
        return $html;
    }

}
