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
    public function __construct(array $params = [], array $row = [])
    {
        parent::__construct($params, $row);

        if (!isset($this->ui[':active-value'])) {
            $this->ui[':active-value'] = 1;
        }

        if (!isset($this->ui[':inactive-value'])) {
            $this->ui[':inactive-value'] = 0;
        }

        if (!isset($this->ui[':value'])) {
            $this->ui[':value'] = 'detailItems.' . $this->name . '.value';
        }


        if ($this->name !== null) {
            if (!isset($this->ui['v-model.number'])) {
                $this->ui['v-model.number'] = 'formData.' . $this->name;
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
        $html = '<el-form-item';

        if (isset($this->ui['form-item'])) {
            foreach ($this->ui['form-item'] as $k => $v) {
                if ($v === null) {
                    $html .= ' ' . $k;
                } else {
                    $html .= ' ' . $k . '="' . $v . '"';
                }
            }
        }

        $html .= '>';
        $html .= '<el-switch';
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
        $html .= '</el-switch>';
        $html .= '</el-form-item>';
        return $html;
    }


}
