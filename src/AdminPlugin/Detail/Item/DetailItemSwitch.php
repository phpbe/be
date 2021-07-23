<?php

namespace Be\AdminPlugin\Detail\Item;


/**
 * 明细 开关
 */
class DetailItemSwitch extends DetailItem
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

        if (!isset($this->ui['switch']['active-value'])) {
            $this->ui['switch']['active-value'] = '1';
        }

        if (!isset($this->ui['switch']['inactive-value'])) {
            $this->ui['switch']['inactive-value'] = '0';
        }

        if (!isset($this->ui['switch']['value'])) {
            $this->ui['switch']['value'] = $this->value;
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
        foreach ($this->ui['form-item'] as $k => $v) {
            if ($v === null) {
                $html .= ' '.$k;
            } else {
                $html .= ' '.$k.'="' . $v . '"';
            }
        }
        $html .= '>';
        $html .= '<el-switch';
        if (isset($this->ui['switch'])) {
            foreach ($this->ui['switch'] as $k => $v) {
                if ($v === null) {
                    $html .= ' ' . $k;
                } else {
                    $html .= ' ' . $k . '="' . $v . '"';
                }
            }
        }
        $html .= '>';
        $html .= '</el-switch>';
        $html .= '</el-form-item>';
        return $html;
    }


}
