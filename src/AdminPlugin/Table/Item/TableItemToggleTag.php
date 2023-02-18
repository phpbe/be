<?php

namespace Be\AdminPlugin\Table\Item;


/**
 * 字段 切换器
 */
class TableItemToggleTag extends TableItem
{

    protected array $on = [
        'type' => 'success',
        'label' => '是',
    ];

    protected array $off = [
        'type' => 'info',
        'label' => '否',
    ];

    /**
     * 构造函数
     *
     * @param array $params 参数
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        if (isset($params['on'])) {
            if (isset($params['on']['type'])) {
                $this->on['type'] = $params['on']['type'];
            }

            if (isset($params['on']['label'])) {
                $this->on['label'] = $params['on']['label'];
            }

            if (isset($params['on']['icon'])) {
                $this->on['icon'] = $params['on']['icon'];
            }
        }

        if (isset($params['off'])) {
            if (isset($params['off']['type'])) {
                $this->off['type'] = $params['off']['type'];
            }

            if (isset($params['off']['label'])) {
                $this->off['label'] = $params['off']['label'];
            }

            if (isset($params['off']['icon'])) {
                $this->off['icon'] = $params['off']['icon'];
            }
        }

        if (!isset($this->ui[':type'])) {
            $this->ui[':type'] = 'scope.row.' . $this->name . ' == \'1\' ? \'' . $this->on['type'] . '\' : \'' . $this->off['type'] . '\'';
        }

        if (!isset($this->ui['size'])) {
            $this->ui['size'] = 'medium';
        }

        if ($this->url) {
            if (!isset($this->ui['@click'])) {
                $this->ui['@click'] = 'scope.row.' . $this->name . ' = (scope.row.' . $this->name . ' === \'1\' ? \'0\' : \'1\');tableItemClick(\'' . $this->name . '\', scope.row)';
            }

            if (!isset($this->postData['field'])) {
                $this->postData['field'] = $this->name;
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
        $html .= '<el-tag';
        foreach ($this->ui as $k => $v) {
            if ($k === 'table-column') {
                continue;
            }

            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';
        if (isset($this->on['icon']) && isset($this->off['icon'])) {
            $html .= '<i :class="scope.row.' . $this->name . ' === \'1\' ? \''.$this->on['icon'].'\' : \''.$this->off['icon'].'\'"></i> ';
        }
        $html .= '{{scope.row.' . $this->name . ' === \'1\' ? \'' . $this->on['label'] . '\' : \'' . $this->off['label'] . '\'}}';
        $html .= '</el-tag>';
        $html .= '</template>';
        $html .= '</el-table-column>';

        return $html;
    }

}
