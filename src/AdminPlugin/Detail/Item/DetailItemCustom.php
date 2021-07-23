<?php

namespace Be\AdminPlugin\Detail\Item;

/**
 * 明细 自定义
 */
class DetailItemCustom extends DetailItem
{

    public $html = '';

    /**
     * 构造函数
     *
     * @param array $params 参数
     * @param array $row 数据对象
     */
    public function __construct($params = [], $row = [])
    {
        parent::__construct($params, $row);

        $html = $params['html'];
        if ($html instanceof \Closure) {
            $this->html = $html($row);
        } else {
            $this->html = $html;
        }
    }

    /**
     * 获取html内容
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

}
