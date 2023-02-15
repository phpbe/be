<?php

namespace Be\AdminPlugin\Form\Item;

use Be\Be;
use Be\AdminPlugin\AdminPluginException;
use Be\Util\Crypt\Random;

/**
 * 表单项 Tinymce 编辑器
 */
class FormItemTinymce extends FormItem
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

        $appSystemWwwUrl = Be::getProperty('App.System')->getWwwUrl();
        $baseUrl = $appSystemWwwUrl . '/lib/tinymce/tinymce_5.10.2';

        $this->js = [
            $baseUrl . '/tinymce.min.js',
        ];

        $fileCallback = base64_encode('parent.window.befile.selectedFiles = files;');
        $imageCallback = base64_encode('parent.window.beimage.selectedFiles = files;');

        $layout = 'simple';
        if (isset($params['layout'])) {
            $layout = $params['layout'];
        }

        $autoresize = true;
        if (isset($params['autoresize'])) {
            $autoresize = $params['autoresize'];
        }

        $this->option = [
            'selector' => '#formItemTinymce_' . $this->name,
            'language' => 'zh_CN',
            //'inline' => true,

            // 工具栏浮动
            'toolbar_sticky' => true,
            'toolbar_sticky_offset' => 60,

            // 移除 Powered by TinyMCE
            'branding' => false,

            // 工具栏一行显示不全时的展示样式
            'toolbar_mode' => 'sliding',

            'be_storage_url' => beAdminUrl('System.Storage.pop', ['callback' => $fileCallback]),
            'be_storage_url_filter_image' => beAdminUrl('System.Storage.pop', ['filterImage' => 1, 'callback' => $imageCallback]),

            // 禁用插入资源的相对网址，与伪静态冲突
            'relative_urls' => false,

            // 不处理资源网址
            'convert_urls' => false,
        ];

        $isOpenaiInstalled = Be::getService('App.System.Admin.App')->isInstalled('Openai');
        if ($isOpenaiInstalled) {
            $chatgptCallback = base64_encode('parent.window.bechatgpt.textCompletion = textCompletion;');
            $this->option['be_openai_chatgpt_url'] = beAdminUrl('Openai.TextCompletion.pop', ['callback' => $chatgptCallback]);
        }

        $contentCss = '';
        $contentCss .= 'https://cdn.phpbe.com/ui/be.css,';
        $contentCss .= 'https://cdn.phpbe.com/ui/be-icons.css';
        if (!isset($this->option['content_css']) || $this->option['content_css'] === '') {
            $this->option['content_css'] = $contentCss;
        } else {
            $this->option['content_css'] .= ',' . $contentCss;
        }

        switch ($layout) {
            case 'basic':
                $this->option = array_merge($this->option, [
                    'min_height' => 200,
                    'plugins' => 'advlist' .  ($autoresize ? ' autoresize' : '') . ' indent2em lists',
                    'toolbar' => 'formatselect bold italic strikethrough underline forecolor alignleft aligncenter alignright alignjustify removeformat | bullist numlist outdent indent indent2em',
                    'menubar' => false,
                    'statusbar' => false,
                ]);
                break;
            case 'simple':
                $plugins = 'advlist';
                if ($autoresize) $plugins .= ' autoresize';
                $plugins .= ' code fullscreen';
                if ($isOpenaiInstalled) $plugins .= ' bechatgpt';
                $plugins .= ' befile beimage indent2em link lists media table';

                $toolbar = 'formatselect bold italic strikethrough underline forecolor backcolor align removeformat | bullist numlist outdent indent indent2em | link ';
                if ($isOpenaiInstalled) {
                    $toolbar .= ' bechatgpt';
                }
                $toolbar .= ' befile beimage media table | code fullscreen';


                $this->option = array_merge($this->option, [
                    'min_height' => 300,
                    'plugins' => $plugins,
                    'toolbar' => $toolbar,
                    'menubar' => false,
                    'statusbar' => false,
                ]);
                break;
            case 'full':

                $plugins = 'advlist anchor autolink';
                if ($autoresize) $plugins .= ' autoresize';
                if ($isOpenaiInstalled) $plugins .= ' bechatgpt';
                $plugins .= ' befile beimage charmap charmap code becodesample directionality emoticons fullscreen help hr image importcss insertdatetime indent2em link lists media nonbreaking noneditable pagebreak paste preview print save searchreplace table template textpattern toc visualblocks visualchars wordcount';

                $toolbar = 'undo redo | fontsizeselect formatselect bold italic underline strikethrough forecolor backcolor align removeformat | bullist numlist outdent indent indent2em | link ';
                if ($isOpenaiInstalled) {
                    $toolbar .= ' bechatgpt';
                }
                $toolbar .= ' befile beimage media table becodesample anchor pagebreak charmap emoticons template | code preview fullscreen';

                $this->option = array_merge($this->option, [
                    'min_height' => 400,
                    'plugins' => $plugins,
                    'toolbar' => $toolbar,
                    //'menubar' => 'file edit view insert format tools table help',
                    'menubar' => false,
                    'statusbar' => false,
                    //'fontsize_formats' => '0.75rem 0.8rem 0.9rem 1rem 1.1rem 1.2rem 1.25rem 1.3rem 1.4rem 1.5rem 1.75rem 2rem 2.5rem 3rem 3.5rem 4rem 5rem 6rem',
                    'fontsize_formats' => '9px 10px 11px 12px 13px 14px 15px 16px 18px 20px 24px 28px 32px 36px 40px 48px 60px 72px',
                ]);

                $this->js[] = $appSystemWwwUrl . '/lib/highlight.js/highlight.js-11.5.1/highlight.min.js';

                $contentCss = $appSystemWwwUrl . '/lib/highlight.js/highlight.js-11.5.1/styles/atom-one-light.css';
                $this->option['content_css'] .= ',' . $contentCss;

                break;
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

        if (isset($this->option['inline']) && $this->option['inline']) {
            $html .= '<div class="be-p-100" style="border:#DCDFE6 1px solid;" id="formItemTinymce_' . $this->name . '"></div>';
        } else {
            $html .= '<textarea id="formItemTinymce_' . $this->name . '"></textarea>';
        }

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

        $mountedCode = '';
        $mountedCode .= 'let this_' . $rand . ' = this;';
        $mountedCode .= 'tinymce.init({';
        foreach ($this->option as $key => $val) {
            $mountedCode .= $key . ':' . json_encode($val) . ',';
        }

        $onChangeCallback = '';
        if (isset($this->ui['@change'])) {
            $onChangeCallback = 'this_' . $rand . '.';
            $onChangeCallback .= $this->ui['@change'];
            if (strpos($onChangeCallback, '(') === false) {
                $onChangeCallback .= '()';
            }
            $onChangeCallback .= ';';
        }

        // 内容更新时回写到 formData
        $mountedCode .= 'init_instance_callback: function(editor) {
            this_' . $rand . '.formItems.' . $this->name . '.instance = editor;
            editor.setContent(this_' . $rand . '.formData.' . $this->name . ');
            editor.on(\'input change undo redo\', function() {
                let content = this.getContent();
                this_' . $rand . '.formData.' . $this->name . ' = content;
                ' . $onChangeCallback . '
            });
        }';

        $mountedCode .= '});';

        return [
            'mounted' => $mountedCode,
            'beforeDestroy' => 'this.formItems.' . $this->name . '.instance.destroy();',
        ];
    }


}
