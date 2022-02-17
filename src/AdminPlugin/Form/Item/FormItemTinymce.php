<?php

namespace Be\AdminPlugin\Form\Item;

use Be\Be;
use Be\AdminPlugin\AdminPluginException;

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

        $this->js = [
            'tinymce.min.js',
        ];


        $fileCallback = base64_encode('parent.window.befile.selectedFiles = files;');
        $imageCallback = base64_encode('parent.window.beimage.selectedFiles = files;');

        $this->option = [
            'selector' => '#formItemTinymce_' . $this->name,
            'language' => 'zh_CN',
            'branding' => false,
            'min_height' => '100',
            'height' => '500',
            'forced_root_block' => false,
            'toolbar_mode' => 'sliding',
            'be_storage_url' => beAdminUrl('System.Storage.pop', ['callback' => $fileCallback]),
            'be_storage_url_filter_image' => beAdminUrl('System.Storage.pop', ['filterImage' => 1, 'callback' => $imageCallback]),
        ];

        $layout = 'simple';
        if (isset($params['layout'])) {
            $layout = $params['layout'];
        }

        switch ($layout) {
            case 'basic':
                $this->option = array_merge($this->option, [
                    'plugins' => 'advlist autosave lists',
                    'toolbar' => 'formatselect bold italic strikethrough underline forecolor alignleft aligncenter alignright alignjustify removeformat | bullist numlist outdent indent',
                    'menubar' => false,
                    'statusbar' => false,
                ]);
                break;
            case 'simple':
                $this->option = array_merge($this->option, [
                    'plugins' => 'advlist autosave code fullscreen befile beimage link lists media table',
                    'toolbar' => 'formatselect bold italic strikethrough underline forecolor backcolor align removeformat | bullist numlist outdent indent | link befile beimage media table | code fullscreen',
                    'menubar' => false,
                    'statusbar' => false,
                ]);
                break;
            case 'full':
                $this->option = array_merge($this->option, [
                    'plugins' => 'advlist anchor autolink autosave befile beimage charmap charmap code codesample directionality emoticons fullscreen help hr image importcss insertdatetime link lists media nonbreaking noneditable pagebreak paste preview print quickbars save searchreplace table template textpattern toc visualblocks visualchars wordcount',
                    'toolbar' => 'undo redo | fontselect fontsizeselect formatselect bold italic underline strikethrough forecolor backcolor alignleft aligncenter alignright alignjustify removeformat | bullist numlist outdent indent | link befile beimage media table codesample anchor pagebreak charmap emoticons template | code preview fullscreen',
                    'menubar' => 'file edit view insert format tools table help',
                    'fontsize_formats' => '9px 10px 11px 12px 13px 14px 15px 16px 18px 20px 24px 28px 32px 36px 40px 48px 60px 72px',
                ]);
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
        $baseUrl = Be::getProperty('AdminPlugin.Form')->getUrl() . '/Template/tinymce_5.10.2/';
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
        $baseUrl = Be::getProperty('AdminPlugin.Form')->getUrl() . '/Template/tinymce_5.10.2/';
        $css = [];
        foreach ($this->css as $x) {
            $css[] = $baseUrl . $x;
        }

        return $css;
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

        $html .= '<textarea id="formItemTinymce_' . $this->name . '"></textarea>';

        if ($this->description) {
            $html .= '<div class="be-c-999 be-mt-20 be-lh-150">' . $this->description . '</div>';
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
        $mountedCode = '';
        $mountedCode .= 'var _this = this;';
        $mountedCode .= 'tinymce.init({';
        foreach ($this->option as $key => $val) {
            $mountedCode .=  $key . ':' . json_encode($val) . ',';
        }

        // 内容更新时回写到 formData
        $mountedCode .=  'init_instance_callback: function(editor) {
            _this.formItems.' . $this->name . '.instance = editor;
            editor.setContent(_this.formData.' . $this->name . ');
            editor.on(\'input change undo redo\', function() {
                _this.formData.' . $this->name . ' = this.getContent();
            });
        }';
        $mountedCode .= '});';

        return [
            'mounted' => $mountedCode,
            'beforeDestroy' => 'this.formItems.' . $this->name . '.instance.destroy();',
        ];
    }


}
