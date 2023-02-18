<?php

namespace Be\AdminPlugin\Card\Item;


/**
 * 字段 进度条
 */
class CardItemProgress extends CardItem
{

    /**
     * 构造函数
     *
     * @param array $params 参数
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        if (!isset($this->ui[':percentage'])) {
            $this->ui[':percentage'] = 'scope.row.'.$this->name;
        }

        if (!isset($this->ui[':stroke-width'])) {
            $this->ui[':stroke-width'] = '16';
        }

        if (!isset($this->ui[':text-inside'])) {
            $this->ui[':text-inside'] = 'true';
        }

        if (!isset($this->ui[':status'])) {
            $this->ui[':status'] = 'item.'.$this->name.'==100?\'success\':\'primary\'';
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
        $html .= '<el-progress';
        foreach ($this->ui as $k => $v) {
            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';
        $html .= '</el-progress>';
        $html .= '</div>';

        return $html;
    }

}
