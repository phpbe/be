<?php

namespace Be\AdminPlugin\Table\Item;

use Be\AdminPlugin\UiItem\UiItem;
use Be\Be;

/**
 * 字段驱动
 */
abstract class TableItem extends UiItem
{

    protected ?string $name = null; // 键名
    protected string $label = ''; // 配置项中文名称

    protected string $url = ''; // 网址
    protected array $postData = []; // 有后端请求时的附加上的数据
    protected ?string $confirm = null; // 操作前确认
    protected string $target = 'drawer';
    protected array $dialog = [];
    protected array $drawer = [];

    protected array $ui = []; // UI界面参数

    protected string $value = ''; // 值
    protected ?array $keyValues = null; // 可选值键值对

    protected int $export = 1; // 是否可导出
    protected ?string $exportValue = null; // 控制导出的值，默认取 value

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

        /*
        if (isset($params['value'])) {
            $value = $params['value'];
            if ($value instanceof \Closure) {
                $this->value = $value();
            } else {
                $this->value = $value;
            }
        }
        */

        if (isset($params['keyValues'])) {
            $keyValues = $params['keyValues'];
            if ($keyValues instanceof \Closure) {
                $this->keyValues = $keyValues();
            } else {
                $this->keyValues = $keyValues;
            }
        } else {
            if (isset($params['values'])) {
                $values = $params['values'];
                if ($values instanceof \Closure) {
                    $values = $values();
                }

                $keyValues = [];
                foreach ($values as $value) {
                    $keyValues[$value] = $value;
                }
                $this->keyValues = $keyValues;
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

        /*
        if (isset($params['export'])) {
            $export = $params['export'];
            if ($export instanceof \Closure) {
                $this->export = $export();
            } else {
                $this->export = $export;
            }
        }

        if (isset($params['exportValue'])) {
            $exportValue = $params['exportValue'];
            if ($exportValue instanceof \Closure) {
                $this->exportValue = $exportValue($tuple);
            } else {
                $this->exportValue = $exportValue;
            }
        }
        */

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

                $url = Request::getUrl();
                $url .= (strpos($url, '?') === false ? '?' : '&') . 'task=' . $task;
                $this->url = $url;
            } elseif (isset($params['action'])) {
                $action = $params['action'];
                if ($action instanceof \Closure) {
                    $action = $action();
                }
                $this->url = beAdminUrl(Request::getAppName() . '.' . Request::getControllerName() . '.' . $action);
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

        if (!isset($this->ui['table-column']['prop'])) {
            $this->ui['table-column']['prop'] = $this->name;
        }

        if (!isset($this->ui['table-column']['label'])) {
            $this->ui['table-column']['label'] = $this->label;
        }

        if (!isset($this->ui['table-column']['sortable']) && isset($params['sortable']) && $params['sortable']) {
            $this->ui['table-column']['sortable'] = 'custom';
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
     * 获取 vue data
     *
     * @return false | array
     */
    public function getVueData()
    {
        if (!$this->url) return false;

        $vueData = [
            'tableItems' => [
                $this->name => [
                    'url' => $this->url,
                    'confirm' => $this->confirm === null ? '' : $this->confirm,
                    'target' => $this->target,
                    'postData' => $this->postData,
                ]
            ]
        ];

        if ($this->target === 'dialog') {
            $vueData['tableItems'][$this->name]['dialog'] = $this->dialog;
        } elseif ($this->target === 'drawer') {
            $vueData['tableItems'][$this->name]['drawer'] = $this->drawer;
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
            'tableItemClick' => 'function (name, row) {
                var option = this.tableItems[name];
                if (option.confirm) {
                    var _this = this;
                    this.$confirm(option.confirm, \'操作确认\', {
                      confirmButtonText: \'确定\',
                      cancelButtonText: \'取消\',
                      type: \'warning\'
                    }).then(function(){
                        _this.gridItemAction(name, option, row);
                    }).catch(function(){});
                } else {
                    this.gridItemAction(name, option, row);
                }
            }'
        ];
    }

}
