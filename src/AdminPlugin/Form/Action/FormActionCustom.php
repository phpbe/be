<?php

namespace Be\AdminPlugin\Form\Action;


/**
 * 表单操作 自定义
 */
class FormActionCustom extends FormAction
{

    /**
     * 获取html内容
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->value;
    }

}
