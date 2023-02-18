<?php

namespace Be\AdminPlugin\Form\Item;

/**
 * 表单项 隐藏输入框
 */
class FormItemHidden extends FormItem
{

    /**
     * 获取html内容
     *
     * @return string
     */
    public function getHtml(): string
    {
        return '';
    }


}
