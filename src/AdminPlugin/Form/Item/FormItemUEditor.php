<?php

namespace Be\AdminPlugin\Form\Item;

use Be\Be;
use Be\AdminPlugin\AdminPluginException;

/**
 * 表单项 百度 UEditor 编辑器
 */
class FormItemUEditor extends FormItem
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
            'ueditor.config.js',
            'ueditor.all.min.js',
            'lang/zh-cn/zh-cn.js',
        ];

        $this->option = [
            'serverUrl' => beAdminUrl('System.AdminPlugin.formItemUEditor')
        ];

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
        $baseUrl = Be::getProperty('AdminPlugin.Form')->getUrl() . '/AdminTemplate/ueditor1_4_3_3/';
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
        $baseUrl = Be::getProperty('AdminPlugin.Form')->getUrl() . '/AdminTemplate/ueditor1_4_3_3/';
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

        $html .= '<textarea ref="refFormItemUEditor_' . $this->name . '" v-model="formData.' . $this->name . '"></textarea>';
        //<script id="editor" type="text/plain" style="width:1024px;height:500px;"></script>
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
                    'uEditor' => false,
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
        $mountedCode = 'this.formItems.' . $this->name . '.uEditor = UE.getEditor(this.$refs.refFormItemUEditor_' . $this->name . ',' . json_encode($this->option) . ');';

        $updatedCode = 'this.formData.' . $this->name . ' = this.formItems.' . $this->name . '.uEditor.getContent();';
        return [
            'mounted' => $mountedCode,
            'updated' => $updatedCode,
        ];
    }

}
