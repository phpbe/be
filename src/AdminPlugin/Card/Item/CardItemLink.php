<?php

namespace Be\AdminPlugin\Card\Item;


/**
 * 字段 链接
 */
class CardItemLink extends CardItem
{


    /**
     * 构造函数
     *
     * @param array $params 参数
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        if (!isset($this->ui['type'])) {
            if (isset($params['type'])) {
                $this->ui['type'] = $params['type'];
            } else {
                $this->ui['type'] = 'primary';
            }
        }

        if ($this->url) {
            if (!isset($this->ui['@click'])) {
                $this->ui['@click'] = 'cardItemClick(\'' . $this->name . '\', item)';
            }
        } else {
            if (!isset($this->ui['@click'])) {
                $this->ui['@click'] = 'cardItemLinkClick(\'' . $this->name . '\', item)';
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
        $html = '<div class="card-item">';
        if ($this->label) {
            $html .= '<b>' . $this->label . '</b>：';
        }
        $html .= '<el-link';
        foreach ($this->ui as $k => $v) {
            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';
        $html .= '{{item.'.$this->name.'}}';
        $html .= '</el-link>';
        $html .= '</div>';

        return $html;
    }

    /**
     * 获取 vue data
     *
     * @return false | array
     */
    public function getVueData()
    {
        $vueData = [
            'cardItems' => [
                $this->name => [
                    'url' => $this->url ?? '',
                    'confirm' => $this->confirm === null ? '' : $this->confirm,
                    'target' => $this->target,
                ]
            ]
        ];

        if ($this->target === 'dialog') {
            $vueData['cardItems'][$this->name]['dialog'] = $this->dialog;
        } elseif ($this->target === 'drawer') {
            $vueData['cardItems'][$this->name]['drawer'] = $this->drawer;
        }

        return $vueData;
    }

    /**
     * 获取 vue 方法
     *
     * @return false | array
     */
    public function getVueMethods()
    {
        return [
            'cardItemLinkClick' => 'function (name, row) {
                var option = this.cardItems[name];
                var sUrl = option.url ? option.url:row[name]; 
                if (option.confirm) {
                    var _this = this;
                    this.$confirm(option.confirm, \'操作确认\', {
                      confirmButtonText: \'确定\',
                      cancelButtonText: \'取消\',
                      type: \'warning\'
                    }).then(function(){
                         switch (option.target) {
                            case "self":
                            case "_self":
                                window.location.href = sUrl;
                            case "dialog":
                                _this.dialog.title = option.dialog.title;
                                _this.dialog.width = option.dialog.width;
                                _this.dialog.height = option.dialog.height;
                                _this.dialog.visible = true;
                                setTimeout(function () {
                                    document.getElementById("frame-dialog").src = sUrl;
                                }, 50);
                                break;
                            case "drawer":
                                _this.drawer.title = option.drawer.title;
                                _this.drawer.width = option.drawer.width;
                                _this.drawer.visible = true;
                                setTimeout(function () {
                                    document.getElementById("frame-drawer").src = sUrl;
                                }, 50);
                                break;
                            case "blank":
                            case "_blank":
                            default:
                                window.open(sUrl);
                        }
                    }).catch(function(){});
                } else {
                    switch (option.target) {
                        case "self":
                        case "_self":
                            window.location.href = sUrl;
                        case "dialog":
                            this.dialog.title = option.dialog.title;
                            this.dialog.width = option.dialog.width;
                            this.dialog.height = option.dialog.height;
                            this.dialog.visible = true;
                            setTimeout(function () {
                                document.getElementById("frame-dialog").src = sUrl;
                            }, 50);
                            break;
                        case "drawer":
                            this.drawer.title = option.drawer.title;
                            this.drawer.width = option.drawer.width;
                            this.drawer.visible = true;
                            setTimeout(function () {
                                document.getElementById("frame-drawer").src = sUrl;
                            }, 50);
                            break;
                        case "blank":
                        case "_blank":
                        default:
                            window.open(sUrl);
                    }
                }
            }'
        ];
    }

}
