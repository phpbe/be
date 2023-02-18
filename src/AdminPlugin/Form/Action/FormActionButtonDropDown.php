<?php

namespace Be\AdminPlugin\Form\Action;


/**
 * 表单操作 下拉菜单
 */
class FormActionButtonDropDown extends FormAction
{

    protected array $menus = []; // 下拉菜单

    /**
     * 构造函数
     *
     * @param array $params 参数
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        if (!isset($this->ui['dropdown']['@command'])) {
            $this->ui['dropdown']['@command'] = 'formActionButtonDropDownClick';
        }

        if (!isset($this->ui['dropdown-menu']['slot'])) {
            $this->ui['dropdown-menu']['slot'] = 'dropdown';
        }

        if (isset($params['menus'])) {
            $menus = $params['menus'];

            $tmpMenus = null;
            if ($menus instanceof \Closure) {
                $tmpMenus = $menus();
            } else {
                $tmpMenus = $menus;
            }

            if (is_array($tmpMenus)) {
                $i = 0;
                $newMenus = [];
                foreach ($tmpMenus as $tmpMenu) {
                    $tmpMenu['parentName'] = $this->name;
                    $tmpMenu['index'] = $i++;
                    $newMenus[] = new FormActionButtonDropDownMenu($tmpMenu);
                }
                $this->menus = $newMenus;
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
        $html = '<el-dropdown';
        if (isset($this->ui['dropdown'])) {
            foreach ($this->ui['dropdown'] as $k => $v) {
                if ($v === null) {
                    $html .= ' ' . $k;
                } else {
                    $html .= ' ' . $k . '="' . $v . '"';
                }
            }
        }
        $html .= '>';

        $html .= '<el-button';
        foreach ($this->ui as $k => $v) {
            if ($k === 'dropdown' || $k === 'dropdown-menu') {
                continue;
            }
            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';
        $html .= $this->label;
        $html .= '<i class="el-icon-arrow-down el-icon--right"></i>';
        $html .= '</el-button>';

        if (count($this->menus)) {
            $html .= '<el-dropdown-menu';
            if (isset($this->ui['dropdown-menu'])) {
                foreach ($this->ui['dropdown-menu'] as $k => $v) {
                    if ($v === null) {
                        $html .= ' ' . $k;
                    } else {
                        $html .= ' ' . $k . '="' . $v . '"';
                    }
                }
            }
            $html .= '>';

            foreach ($this->menus as $menu) {
                $html .= $menu->getHtml();
            }
            $html .= '</el-dropdown-menu>';
        }

        $html .= '</el-dropdown>';
        return $html;
    }

    /**
     * 获取 vue data
     *
     * @return false | array
     */
    public function getVueData()
    {
        $menus = [];
        foreach ($this->menus as $menu) {
            $m = [
                'url' => $menu->url,
                'confirm' => $menu->confirm === null ? '' : $menu->confirm,
                'target' => $menu->target,
                'postData' => $menu->postData,
            ];

            if ($menu->target === 'dialog') {
                $m['dialog'] = $menu->dialog;
            } elseif ($menu->target === 'drawer') {
                $m['drawer'] = $menu->drawer;
            }

            $menus[] = $m;
        }

        return [
            'formActions' => [
                $this->name => [
                    'menus' => $menus,
                ]
            ]
        ];
    }

    /**
     * 获取 vue 方法
     *
     * @return false | array
     */
    public function getVueMethods()
    {
        return [
            'FormActionButtonDropDownClick' => 'function (command) {
                var option = this.formActions[command.name].menus[command.index];
                if (option.confirm) {
                    var _this = this;
                    this.$confirm(option.confirm, \'操作确认\', {
                      confirmButtonText: \'确定\',
                      cancelButtonText: \'取消\',
                      type: \'warning\'
                    }).then(function(){
                        _this.formAction(command.name, option);
                    }).catch(function(){});
                } else {
                    this.formAction(command.name, option);
                }
            }',
            'FormActionButtonDropDownMenuCommand' => 'function (name, index) {
                return {"name": name, "index": index};
            }',
        ];
    }


}
