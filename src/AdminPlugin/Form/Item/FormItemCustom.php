<?php

namespace Be\AdminPlugin\Form\Item;


/**
 * 表单项 自定义
 */
class FormItemCustom extends FormItem
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
