<?php

namespace Be\AdminPlugin\Form\Item;

use Be\Be;
use Be\AdminPlugin\AdminPluginException;
use Be\Util\Crypt\Random;

/**
 * 表单项 Markdown 编辑器
 */
class FormItemMarkdown extends FormItem
{

    protected $js = []; // 需要引入的 JS 文件
    protected $css = []; // 需要引入的 CSS 文件
    protected $option = []; // 配置项
    protected $valueFormat = 'markdown'; // 值枨式： markdown 、html

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

        $baseUrl = Be::getProperty('App.System')->getWwwUrl() . '/lib/editor.md/editor.md-1.5.0/';

        $this->css = [
            $baseUrl . 'css/editormd.min.css',
        ];

        $this->js = [
            $baseUrl . 'editormd.min.js',
            $baseUrl . 'plugins/be-link-dialog/be-link-dialog.js',
            $baseUrl . 'plugins/be-image-dialog/be-image-dialog.js',
        ];

        $beLinkCallback = base64_encode('parent.window.beLink.selectFile(files);');
        $beImageCallback = base64_encode('parent.window.beImage.selectImage(files);');

        $this->option = [
            //'width' => '100%',
            'height' => '500',
            'tax' => true,
            'tocm' => true,
            'emoji' => true,
            'taskList' => true,
            'codeFold' => true,
            'searchReplace' => true,
            'htmlDecode' => 'style,script,iframe',
            'flowChart' => true,
            'sequenceDiagram' => true,
            'path' => $baseUrl . 'lib/',
            'toolbarIcons' => 'function() {
                return [
                    "undo", "redo", "|",
                    "bold", "del", "italic", "quote", "|",
                    "h1", "h2", "h3", "h4", "h5", "h6", "|",
                    "list-ul", "list-ol", "hr", "|",
                    "beLink", "reference-link", "beImage", "code", "preformatted-text", "code-block", "table", "datetime", "html-entities", "pagebreak", "|",
                    "goto-line", "watch", "preview", "fullscreen", "clear", "search", "|",
                    "help"
                ];
            }',

            'be_storage_url' => beAdminUrl('System.Storage.pop', ['callback' => $beLinkCallback]),
            'be_storage_url_filter_image' => beAdminUrl('System.Storage.pop', ['filterImage' => 1, 'callback' => $beImageCallback]),
        ];

        if ($this->valueFormat === 'html') {
            $this->option['saveHTMLToTextarea'] = true;
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

        if (isset($params['valueFormat'])) {
            $valueFormat = $params['valueFormat'];
            if ($valueFormat instanceof \Closure) {
                $valueFormat = $valueFormat($row);
            }

            $this->valueFormat = $valueFormat;
        }
    }

    /**
     * 获取需要引入的 JS 文件
     *
     * @return false | array
     */
    public function getJs()
    {
        return $this->js;
    }

    /**
     * 获取需要引入的 CSS 文件
     *
     * @return false | array
     */
    public function getCss()
    {
        return $this->css;
    }

    public function getCssCode()
    {
        return '
.editormd-fullscreen {
    z-index: 999999 !important;
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
        $html .= '<div id="formItemMarkdown_' . $this->name . '">';
        $html .= '<textarea></textarea>';
        $html .= '</div>';

        if ($this->description) {
            $html .= '<div class="be-c-999 be-mt-50 be-lh-150">' . $this->description . '</div>';
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
                    'instance' => false,
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
        $rand = Random::lowercaseLetters(16);

        $onChangeCallback = '';
        if (isset($this->ui['@change'])) {
            $onChangeCallback = 'this_' . $rand . '.';
            $onChangeCallback .= $this->ui['@change'];
            if (strpos($onChangeCallback, '(') === false) {
                $onChangeCallback .= '()';
            }
            $onChangeCallback .= ';';
        }

        $mountedCode = '';
        $mountedCode .= 'let this_' . $rand . ' = this;';

        $mountedCode .= 'let editor_' . $rand . ' = editormd("formItemMarkdown_' . $this->name . '", {';
        foreach ($this->option as $key => $val) {
            if (is_string($val)) {
                if (substr($val, 0, 9) === 'function(') {
                    $mountedCode .= $key . ':' . $val . ',';
                } else {
                    $mountedCode .= $key . ':"' . str_replace('"', '&quote;', $val) . '",';
                }
            } else {
                $mountedCode .= $key . ':' . json_encode($val) . ',';
            }
        }

        $mountedCode .= 'markdown: this_' . $rand . '.formData.' . $this->name . ',';
        $mountedCode .= 'onchange: function() {';
        if ($this->valueFormat === 'html') {
            $mountedCode .= ' this_' . $rand . '.formData.' . $this->name . ' = this.getHTML();';
        } else {
            $mountedCode .= ' this_' . $rand . '.formData.' . $this->name . ' = this.getMarkdown();';
        }
        $mountedCode .= $onChangeCallback;
        $mountedCode .= '},';
        $mountedCode .= 'onload: function() {';
        $mountedCode .= 'this_' . $rand . '.formItems.' . $this->name . '.instance = editor_' . $rand . '';
        $mountedCode .= '},';
        $mountedCode .= 'toolbarIconsClass:{beLink:"fa-link", beImage:"fa-picture-o"},';
        $mountedCode .= 'toolbarHandlers:{beLink:function(){this.beLinkDialog();}, beImage:function(){this.beImageDialog();}}';
        $mountedCode .= '});';
        return [
            'mounted' => $mountedCode,
        ];
    }


}
