<?php

namespace Be\AdminPlugin\Form\Action;


/**
 * 表单操作项 按钮
 */
class FormActionButton extends FormAction
{


    /**
     * 构造函数
     *
     * @param array $params 参数
     */
    public function __construct($params = [])
    {
        parent::__construct($params);

        if (!isset($this->ui['type']) && isset($params['type'])) {
            $this->ui['type'] = $params['type'];
        }

        if (!isset($this->ui['icon']) && isset($params['icon'])) {
            $this->ui['icon'] = $params['icon'];
        }

        if (!isset($this->ui['@click'])) {
            $this->ui['@click'] = 'formActionClick(\'' . $this->name . '\')';
        }
    }


    /**
     * 编辑
     *
     * @return string
     */
    public function getHtml()
    {
        $html = '<el-button';
        foreach ($this->ui as $k => $v) {
            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';
        $html .= '<span>' . $this->label . '</span>';
        $html .= '</el-button>';

        return $html;
    }
}
