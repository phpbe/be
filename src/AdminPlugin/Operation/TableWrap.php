<?php

namespace Be\AdminPlugin\Operation;

/**
 * 操作 包装容器
 */
class TableWrap
{
    public $name = null; // 键名
    public $label = ''; // 配置项中文名称
    public $value = ''; // 值
    public $ui = []; // UI界面参数
    public $position = 'right';

    /**
     * 构造函数
     *
     * @param array $params 参数
     */
    public function __construct($params = [])
    {
        if (isset($params['name'])) {
            $name = $params['name'];
            if ($name instanceof \Closure) {
                $this->name = $name();
            } else {
                $this->name = $name;
            }
        }

        if (isset($params['label'])) {
            $label = $params['label'];
            if ($label instanceof \Closure) {
                $this->label = $label();
            } else {
                $this->label = $label;
            }
        }

        if (isset($params['value'])) {
            $value = $params['value'];
            if ($value instanceof \Closure) {
                $this->value = $value();
            } else {
                $this->value = $value;
            }
        }

        if (isset($params['ui'])) {
            $ui = $params['ui'];
            if ($ui instanceof \Closure) {
                $this->ui = $ui();
            } else {
                $this->ui = $ui;
            }
        }

        if (isset($params['position'])) {
            $position = $params['position'];
            if ($position instanceof \Closure) {
                $this->position = $position();
            } else {
                $this->position = $position;
            }

            if (!in_array($this->position, ['left', 'right'])) {
                $this->position = 'right';
            }
        }

        if (!isset($this->ui['table-column']['prop'])) {
            $this->ui['table-column']['prop'] = $this->name;
        }

        if (!isset($this->ui['table-column']['label'])) {
            $this->ui['table-column']['label'] = $this->label;
        }

        if (!isset($this->ui['table-column']['width']) && isset($params['width'])) {
            $this->ui['table-column']['width'] = $params['width'];
        }

        if (!isset($this->ui['table-column']['align'])) {
            if (isset($params['align'])) {
                $this->ui['table-column']['align'] = $params['align'];
            } else {
                $this->ui['table-column']['align'] = 'center';
            }
        }

        if (!isset($this->ui['table-column']['header-align'])) {
            if (isset($params['header-align'])) {
                $this->ui['table-column']['header-align'] = $params['header-align'];
            } else {
                $this->ui['table-column']['header-align'] = $this->ui['table-column']['align'];
            }
        }
    }

    /**
     * 编辑
     *
     * @return string | array
     */
    public function getHtmlBefore()
    {
        $html = '<el-table-column';
        if (isset($this->ui['table-column'])) {
            foreach ($this->ui['table-column'] as $k => $v) {
                if ($v === null) {
                    $html .= ' ' . $k;
                } else {
                    $html .= ' ' . $k . '="' . $v . '"';
                }
            }
        }
        $html .= '>';
        $html .= '<template slot-scope="scope">';
        return $html;
    }

    /**
     * 编辑
     *
     * @return string | array
     */
    public function getHtmlAfter()
    {
        $html = '</template>';
        $html .= '</el-table-column>';

        return $html;
    }

}
