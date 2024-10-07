<?php

namespace Be\AdminPlugin\Toolbar\Item;

use Be\AdminPlugin\UiItem\UiItem;
use Be\Be;

/**
 * 工具栏驱动
 */
abstract class ToolbarItem extends UiItem
{
    protected ?string $name = null; // 键名
    protected string $label = ''; // 配置项中文名称
    protected string $value = ''; // 值
    protected array $ui = []; // UI界面参数

    protected string $url = ''; // 网址
    protected array $postData = []; // 有后端请求时的附加上的数据
    protected ?string $confirm = null; // 操作前确认
    protected string $target = 'drawer';
    protected array $dialog = [];
    protected array $drawer = [];

    protected static int $nameIndex = 0;

    /**
     * 构造函数
     *
     * @param array $params 参数
     */
    public function __construct(array $params = [])
    {
        if (isset($params['name'])) {
            $name = $params['name'];
            if ($name instanceof \Closure) {
                $this->name = $name();
            } else {
                $this->name = $name;
            }
        } else {
            $this->name = 'n' . (self::$nameIndex++);
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

        if (isset($params['url'])) {
            $url = $params['url'];
            if ($url instanceof \Closure) {
                $this->url = $url();
            } else {
                $this->url = $url;
            }
        } else {
            if (isset($params['task'])) {
                $task = $params['task'];
                if ($task instanceof \Closure) {
                    $task = $task();
                }

                $url = Be::getRequest()->getUrl();
                $url .= (strpos($url, '?') === false ? '?' : '&') . 'task=' . $task;
                $this->url = $url;
            } elseif (isset($params['action'])) {
                $action = $params['action'];
                if ($action instanceof \Closure) {
                    $action = $action();
                }

                
                $appName = Request::getAppName();
                $controllerName = Request::getControllerName();
                $this->url = beAdminUrl($appName . '.' . $controllerName . '.' . $action);
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

        if (isset($params['postData'])) {
            $postData = $params['postData'];
            if ($postData instanceof \Closure) {
                $this->postData = $postData();
            } else {
                $this->postData = $postData;
            }
        }

        if (isset($params['confirm'])) {
            $confirm = $params['confirm'];
            if ($confirm instanceof \Closure) {
                $this->confirm = $confirm();
            } else {
                $this->confirm = $confirm;
            }
        }

        if (isset($params['target'])) {
            $target = $params['target'];
            if ($target instanceof \Closure) {
                $this->target = $target();
            } else {
                $this->target = $target;
            }
        }

        if ($this->target === 'dialog') {
            if (isset($params['dialog'])) {
                $dialog = $params['dialog'];
                if ($dialog instanceof \Closure) {
                    $this->dialog = $dialog();
                } else {
                    $this->dialog = $dialog;
                }
            }

            if (!isset($this->dialog['title'])) {
                $this->dialog['title'] = $this->label;
            }

            if (!isset($this->dialog['width'])) {
                $this->dialog['width'] = '600px';
            }

            if (!isset($this->drawer['height'])) {
                $this->dialog['height'] = '400px';
            }

        } elseif ($this->target === 'drawer') {
            if (isset($params['drawer'])) {
                $drawer = $params['drawer'];
                if ($drawer instanceof \Closure) {
                    $this->drawer = $drawer();
                } else {
                    $this->drawer = $drawer;
                }
            }

            if (!isset($this->drawer['title'])) {
                $this->drawer['title'] = $this->label;
            }

            if (!isset($this->drawer['width'])) {
                $this->drawer['width'] = '40%';
            }
        }
    }

    /**
     * 获取 vue data
     *
     * @return false | array
     */
    public function getVueData()
    {
        $vueData = [
            'toolbarItems' => [
                $this->name => [
                    'url' => $this->url,
                    'confirm' => $this->confirm === null ? '' : $this->confirm,
                    'target' => $this->target,
                    'postData' => $this->postData,
                    'enable' => true,
                ]
            ]
        ];

        if ($this->target === 'dialog') {
            $vueData['toolbarItems'][$this->name]['dialog'] = $this->dialog;
        } elseif ($this->target === 'drawer') {
            $vueData['toolbarItems'][$this->name]['drawer'] = $this->drawer;
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
            'toolbarItemClick' => 'function (name) {
                var option = this.toolbarItems[name];
                if (option.confirm) {
                    var _this = this;
                    this.$confirm(option.confirm, \'操作确认\', {
                      confirmButtonText: \'确定\',
                      cancelButtonText: \'取消\',
                      type: \'warning\'
                    }).then(function(){
                        _this.toolbarItemAction(name, option);
                    }).catch(function(){});
                } else {
                    this.toolbarItemAction(name, option);
                }
            }'
        ];
    }

}
