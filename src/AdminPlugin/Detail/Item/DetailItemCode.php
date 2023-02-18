<?php

namespace Be\AdminPlugin\Detail\Item;

use Be\Be;

/**
 * 明细 代码
 */
class DetailItemCode extends DetailItem
{

    protected $js = []; // 需要引入的 JS 文件
    protected $css = []; // 需要引入的 CSS 文件
    protected $option = []; // 配置项

    /**
     * 构造函数
     *
     * @param array $params 参数
     * @param array $row 数据对象
     */
    public function __construct(array $params = [], array $row = [])
    {
        parent::__construct($params, $row);

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
        $baseUrl = Be::getProperty('App.System')->getWwwUrl() . '/lib/codemirror/codemirror-5.57.0/';
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
        $baseUrl = Be::getProperty('App.System')->getWwwUrl() . '/lib/codemirror/codemirror-5.57.0/';
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
    line-height: 1.5rem;
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
    public function getHtml(): string
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

        $html .= '<textarea ref="codeMirror_' . $this->name . '">' . $this->value . '</textarea>';

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
            'detailItems' => [
                $this->name => [
                    'value' => $this->value,
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
        $this->option['readOnly'] = true;
        $mountedCode = 'this.detailItems.' . $this->name . '.codeMirror = CodeMirror.fromTextArea(this.$refs.codeMirror_' . $this->name . ',' . json_encode($this->option) . ');';
        $updatedCode = 'this.detailItems.' . $this->name . '.codeMirror && this.detailItems.' . $this->name . '.codeMirror.refresh();';
        return [
            'mounted' => $mountedCode,
            'updated' => $updatedCode,
        ];
    }

}
