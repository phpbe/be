<?php

namespace Be\AdminPlugin\Detail\Item;


/**
 * 明细 文本
 */
class DetailItemText extends DetailItem
{
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
        $html .= '<div style="word-wrap: break-word; word-break:break-all;">';
        $html .= '{{detailItems.'.$this->name.'.value}}';
        $html .= '</div>';
        $html .= '</el-form-item>';
        return $html;
    }


    /**
     * 获取 vue data
     *
     * @return false | array
     */
    public function getVueData()
    {
        if ($this->keyValues !== null && is_array($this->keyValues)) {
            $value = $this->value;
            $keyValues = $this->keyValues;
            if (isset($keyValues[$value])) {
                $value = $keyValues[$value];
            } else {
                $value = '';
            }
            return [
                'detailItems' => [
                    $this->name => [
                        'value' => nl2br($value),
                        'keyValues' => $keyValues,
                    ]
                ]
            ];
        } else {
            return [
                'detailItems' => [
                    $this->name => [
                        'value' => nl2br($this->value),
                    ]
                ]
            ];
        }
    }

}
