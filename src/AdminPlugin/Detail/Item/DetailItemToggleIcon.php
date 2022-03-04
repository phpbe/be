<?php

namespace Be\AdminPlugin\Detail\Item;


/**
 * 明细 切换器图标
 */
class DetailItemToggleIcon extends DetailItem
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

        if (!isset($this->ui['type'])) {
            $this->ui['type'] = $this->value ? 'success' : 'info';
        }

        if (!isset($this->ui['icon'])) {
            $this->ui['icon'] = $this->value ? 'el-icon-success' : 'el-icon-error';
        }

        if (!isset($this->ui['circle'])) {
            $this->ui['circle'] = null;
        }

        if (!isset($this->ui[':underline'])) {
            $this->ui[':underline'] = 'false';
        }

        if (!isset($this->ui['style'])) {
            $this->ui['style'] = 'cursor:auto;font-size:24px;' . ( $this->value ? '' : 'color:#bbb');
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
        $html .= '<el-link';
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
        $html .= '>';
        $html .= '</el-link>';
        $html .= '</el-form-item>';
        return $html;
    }

}
