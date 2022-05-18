<?php

namespace Be\AdminPlugin\Form\Item;

use Be\Be;
use Be\AdminPlugin\AdminPluginException;

/**
 * 表单项 代码
 */
class FormItemCode extends FormItem
{

    protected $js = []; // 需要引入的 JS 文件
    protected $css = []; // 需要引入的 CSS 文件
    protected $option = []; // 配置项

    /**
     * 构造函数
     *
     * @param array $params 参数
     * @param array $row 数据对象
     * @throws AdminPluginException
     */
    public function __construct($params = [], $row = [])
    {
        parent::__construct($params, $row);

        if ($this->required) {
            if (!isset($this->ui['form-item'][':rules'])) {
                $this->ui['form-item'][':rules'] = '[{required: true, message: \'请输入' . $this->label . '\', trigger: \'blur\' }]';
            }
        }

        if (isset($params['language'])) {
            $language = $params['language'];
            if ($language instanceof \Closure) {
                $language = $language($row);
            }

            switch ($language) {
                case 'html':
                    $this->js = [
                        'lib/codemirror.js',
                        'mode/htmlmixed/htmlmixed.js',
                        'mode/xml/xml.js',
                        'mode/javascript/javascript.js',
                        'mode/css/css.js',
                    ];

                    $this->css = [
                        'lib/codemirror.css',
                    ];

                    $this->option = [
                        'theme' => 'default',
                        'mode' => 'htmlmixed',
                        'lineNumbers' => true,
                        'lineWrapping' => true,
                    ];

                    break;
                case 'css':
                    $this->js = [
                        'lib/codemirror.js',
                        'mode/css/css.js',
                        'addon/edit/matchbrackets.js',
                        'addon/hint/show-hint.js',
                        'addon/hint/css-hint.js',
                        'addon/css/css.js',
                    ];

                    $this->css = [
                        'lib/codemirror.css',
                        'addon/hint/show-hint.css',
                    ];

                    $this->option = [
                        'theme' => 'default',
                        'mode' => 'text/css',
                        'lineNumbers' => true,
                        'lineWrapping' => true,
                        'matchBrackets' => true,
                    ];
                    break;
                case 'javascript':
                    $this->js = [
                        'lib/codemirror.js',
                        'mode/javascript/javascript.js',
                        'addon/edit/matchbrackets.js',
                    ];

                    $this->css = [
                        'lib/codemirror.css',
                    ];

                    $this->option = [
                        'theme' => 'default',
                        'mode' => 'text/javascript',
                        'lineNumbers' => true,
                        'lineWrapping' => true,
                        'matchBrackets' => true,
                    ];

                    break;
                case 'json':
                    $this->js = [
                        'lib/codemirror.js',
                        'mode/javascript/javascript.js',
                        'addon/edit/matchbrackets.js',
                        'addon/lint/jsonlint-1.6.0.js',
                        'addon/lint/lint.js',
                        'addon/lint/json-lint.js',

                    ];

                    $this->css = [
                        'lib/codemirror.css',
                        'addon/lint/lint.css',
                    ];

                    $this->option = [
                        'theme' => 'default',
                        'mode' => 'application/json',
                        'lineNumbers' => true,
                        'lineWrapping' => true,
                        'gutters' => ['CodeMirror-lint-markers'],
                        'matchBrackets' => true,
                        'lint' => true,
                    ];
                    break;
                case 'php':
                    $this->js = [
                        'lib/codemirror.js',
                        'mode/clike/clike.js',
                        'mode/php/php.js',
                        'addon/edit/matchbrackets.js',
                    ];

                    $this->css = [
                        'lib/codemirror.css',
                    ];

                    $this->option = [
                        'theme' => 'default',
                        'mode' => 'text/x-php',
                        'lineNumbers' => true,
                        'lineWrapping' => true,
                        'matchBrackets' => true,
                    ];
                    break;
                case 'sql':
                    $this->js = [
                        'lib/codemirror.js',
                        'mode/sql/sql.js',
                        'addon/edit/matchbrackets.js',
                        'addon/hint/show-hint.js',
                        'addon/hint/sql-hint.js',
                    ];

                    $this->css = [
                        'lib/codemirror.css',
                        'addon/hint/show-hint.css',
                    ];

                    $this->option = [
                        'theme' => 'default',
                        'mode' => 'text/x-sql',
                        'lineNumbers' => true,
                        'lineWrapping' => true,
                        'matchBrackets' => true,
                    ];

                    break;
                case 'xml':
                    $this->js = [
                        'lib/codemirror.js',
                        'mode/xml/xml.js',
                    ];

                    $this->css = [
                        'lib/codemirror.css',
                    ];

                    $this->option = [
                        'theme' => 'default',
                        'mode' => 'text/html',
                        'lineNumbers' => true,
                        'lineWrapping' => true,
                    ];
                    break;

                default:
                    $this->js = [
                        'lib/codemirror.js',
                        'mode/javascript/javascript.js',
                    ];

                    $this->css = [
                        'lib/codemirror.css',
                    ];

                    $this->option = [
                        'theme' => 'default',
                        'mode' => 'javascript',
                        'lineNumbers' => true,
                        'lineWrapping' => true,
                    ];
            }

            if (isset($params['js'])) {
                $js = $params['js'];
                if ($js instanceof \Closure) {
                    $js = $js($row);
                }

                if (is_array($js)) {
                    $this->js = array_merge($this->js, $js);
                }
            }

            if (isset($params['css'])) {
                $css = $params['css'];
                if ($css instanceof \Closure) {
                    $css = $css($row);
                }

                if (is_array($css)) {
                    $this->css = array_merge($this->css, $css);
                }
            }

            if (isset($params['option'])) {
                $option = $params['option'];
                if ($option instanceof \Closure) {
                    $option = $option($row);
                }

                if (is_array($option)) {
                    $this->option = array_merge($this->option, $option);
                }
            }

        } else {

            if (isset($params['js'])) {
                $js = $params['js'];
                if ($js instanceof \Closure) {
                    $js = $js($row);
                }
                if (is_array($js)) {
                    $this->js = $js;
                }
            }

            if (isset($params['css'])) {
                $css = $params['css'];
                if ($css instanceof \Closure) {
                    $css = $css($row);
                }

                if (is_array($css)) {
                    $this->css = $css;
                }
            }

            if (isset($params['option'])) {
                $option = $params['option'];
                if ($option instanceof \Closure) {
                    $option = $option($row);
                }

                if (is_array($option)) {
                    $this->option = $option;
                }
            }
        }
    }

    /**
     * 获取需要引入的 JS 文件
     *
     * @return false | array
     */
    public function getJs()
    {
        $baseUrl = Be::getProperty('AdminPlugin.Form')->getUrl() . '/Template/codemirror-5.57.0/';
        $js = [];
        foreach ($this->js as $x) {
            $js[] = $baseUrl . $x;
        }

        return $js;
    }

    /**
     * 获取需要引入的 CSS 文件
     *
     * @return false | array
     */
    public function getCss()
    {
        $baseUrl = Be::getProperty('AdminPlugin.Form')->getUrl() . '/Template/codemirror-5.57.0/';
        $css = [];
        foreach ($this->css as $x) {
            $css[] = $baseUrl . $x;
        }

        return $css;
    }

    public function getCssCode()
    {
        return '
.CodeMirror {
    font-size: 13px;
    line-height: 150%;
    border: 1px solid #DCDFE6;
    min-height: 60px;
    height: auto !important;
}';
    }

    /**
     * 获取html内容
     *
     * @return string
     */
    public function getHtml()
    {
        $html = '<el-form-item';
        foreach ($this->ui['form-item'] as $k => $v) {
            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';

        $html .= '<textarea ref="refFormItemCode_' . $this->name . '"';
        if ($this->name !== null) {
            if (!isset($this->ui['v-model'])) {
                $html .= ' v-model="formData.' . $this->name . '"';
            } else {
                $html .= ' v-model="' . $this->ui['v-model'] . '"';
            }
        }
        $html .= '></textarea>';

        if ($this->description) {
            $html .= '<div class="be-c-bbb be-mt-50 be-lh-150">' . $this->description . '</div>';
        }

        $html .= '</el-form-item>';
        return $html;
    }

    /**
     * 获取 vue data
     *
     * @return false | array
     */
    public function getVueData()
    {
        return [
            'formItems' => [
                $this->name => [
                    'codeMirror' => false,
                ]
            ]
        ];
    }

    /**
     * 获取 vue 钩子
     *
     * @return false | array
     */
    public function getVueHooks()
    {
        $mountedCode = 'this.formItems.' . $this->name . '.codeMirror = CodeMirror.fromTextArea(this.$refs.refFormItemCode_' . $this->name . ',' . json_encode($this->option) . ');';

        $updatedCode = 'this.formItems.' . $this->name . '.codeMirror && this.formItems.' . $this->name . '.codeMirror.refresh();';
        $updatedCode .= 'this.formData.' . $this->name . ' = this.formItems.' . $this->name . '.codeMirror.getValue();';
        return [
            'mounted' => $mountedCode,
            'updated' => $updatedCode,
        ];
    }

}
