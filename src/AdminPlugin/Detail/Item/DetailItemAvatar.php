<?php

namespace Be\AdminPlugin\Detail\Item;


/**
 * 明细 头像
 */
class DetailItemAvatar extends DetailItem
{

    /**
     * 构造函数
     *
     * @param array $params 参数
     * @param array $row 数据对象
     */
    public function __construct($params = [], $row = [])
    {
        parent::__construct($params, $row);

        if (!isset($this->ui['shape'])) {
            $this->ui['shape'] = 'square';
        }

        if (!isset($this->ui[':src'])) {
            $this->ui[':src'] = 'detailItems.' . $this->name . '.value';
        }
    }

    /**
     * 获取html内容
     *
     * @return string
     */
    public function getHtml()
    {
        $html = '<el-form-item';

        if (isset($this->ui['form-item'])) {
            foreach ($this->ui['form-item'] as $k => $v) {
                if ($v === null) {
                    $html .= ' '.$k;
                } else {
                    $html .= ' '.$k.'="' . $v . '"';
                }
            }
        }

        $html .= '>';
        $html .= '<el-avatar';
        if (isset($this->ui)) {
            foreach ($this->ui as $k => $v) {
                if ($k === 'form-item') {
                    continue;
                }

                if ($v === null) {
                    $html .= ' ' . $k;
                } else {
                    $html .= ' ' . $k . '="' . $v . '"';
                }
            }
        }
        $html .= '>';
        $html .= '</el-avatar>';
        $html .= '</el-form-item>';
        return $html;
    }

}
