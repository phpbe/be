<?php

namespace Be\AdminPlugin\Card\Item;


/**
 * 字段 开关
 */
class CardItemSwitch extends CardItem
{

    /**
     * 构造函数
     *
     * @param array $params 参数
     */
    public function __construct($params = [])
    {
        parent::__construct($params);

        if ($this->url) {
            if (!isset($this->ui['@change'])) {
                $this->ui['@change'] = 'cardItemClick(\'' . $this->name . '\', item)';
            }

            if (!isset($this->postData['field'])) {
                $this->postData['field'] = $this->name;
            }
        }

        if (!isset($this->ui['active-value'])) {
            $this->ui['active-value'] = '1';
        }

        if (!isset($this->ui['inactive-value'])) {
            $this->ui['inactive-value'] = '0';
        }

        if (!isset($this->ui['v-model'])) {
            $this->ui['v-model'] = 'item.' . $this->name;
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
        $html .= '<el-switch';
        foreach ($this->ui as $k => $v) {
            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';
        $html .= '</el-switch>';
        $html .= '</div>';

        return $html;
    }

}
