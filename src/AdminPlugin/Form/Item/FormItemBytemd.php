<?php

namespace Be\AdminPlugin\Form\Item;

use Be\Be;
use Be\AdminPlugin\AdminPluginException;
use Be\Util\Crypt\Random;

/**
 * 表单项 ByteMD 编辑器
 */
class FormItemBytemd extends FormItem
{

    protected array $js = []; // 需要引入的 JS 文件
    protected array $css = []; // 需要引入的 CSS 文件
    protected array $plugins = []; // ByteMD 插件
    protected string $valueFormat = 'markdown'; // 值格式：markdown、html

    /**
     * 构造函数
     *
     * @param array $params 参数
     * @param array $row 数据对象
     * @throws AdminPluginException
     */
    public function __construct(array $params = [], array $row = [])
    {
        parent::__construct($params, $row);

        if ($this->required) {
            if (!isset($this->ui['form-item'][':rules'])) {
                $this->ui['form-item'][':rules'] = '[{required: true, message: \'请输入' . $this->label . '\', trigger: \'blur\' }]';
            }
        }

        $baseUrl = Be::getProperty('App.System')->getWwwUrl() . '/lib/bytemd/bytemd-1.22.0/';

        $this->css = [
            $baseUrl . 'bytemd.min.css',
            $baseUrl . 'github-markdown-light.min.css',
            $baseUrl . 'highlight-vs.min.css',
        ];

        // process polyfill 必须在 bytemd 之前加载，因为 bytemd 内部引用了 Node.js 的 process 对象
        $this->js = [
            $baseUrl . 'process.min.js',
            $baseUrl . 'bytemd.min.js',
        ];

        // 默认插件
        $this->plugins = ['gfm', 'highlight', 'frontmatter', 'mermaid', 'math', 'gemoji', 'be-storage'];

        if (isset($params['plugins'])) {
            $plugins = $params['plugins'];
            if ($plugins instanceof \Closure) {
                $plugins = $plugins($row);
            }
            $this->plugins = $plugins;
        }

        // 加载插件 JS
        if (in_array('gfm', $this->plugins)) {
            $this->js[] = $baseUrl . 'plugins/plugin-gfm.min.js';
        }
        if (in_array('highlight', $this->plugins)) {
            $this->js[] = $baseUrl . 'plugins/plugin-highlight.min.js';
        }
        if (in_array('frontmatter', $this->plugins)) {
            $this->js[] = $baseUrl . 'plugins/plugin-frontmatter.min.js';
        }
        if (in_array('mermaid', $this->plugins)) {
            $this->js[] = $baseUrl . 'plugins/plugin-mermaid.min.js';
        }
        if (in_array('math', $this->plugins)) {
            $this->js[] = $baseUrl . 'plugins/plugin-math.min.js';
            $this->css[] = $baseUrl . 'katex/katex.min.css';
        }
        if (in_array('gemoji', $this->plugins)) {
            $this->js[] = $baseUrl . 'plugins/plugin-gemoji.min.js';
        }
        if (in_array('be-storage', $this->plugins)) {
            $this->js[] = $baseUrl . 'plugins/plugin-be-storage.js';
        }

        if (isset($params['valueFormat'])) {
            $valueFormat = $params['valueFormat'];
            if ($valueFormat instanceof \Closure) {
                $valueFormat = $valueFormat($row);
            }
            $this->valueFormat = $valueFormat;
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
.bytemd-fullscreen {
    z-index: 999999 !important;
}
.bytemd {
    height: 500px;
}
.bytemd-status {
    display: none;
}
.el-form-item .bytemd {
    line-height: normal;
    font-size: 14px;
    color: #24292e;
}
.el-form-item .bytemd button {
    background: none;
    border: none;
    padding: 0;
    margin: 0;
    font-size: inherit;
    color: inherit;
    cursor: pointer;
    -webkit-appearance: none;
    appearance: none;
    line-height: normal;
}
.el-form-item .bytemd svg {
    display: block;
}
.el-form-item .bytemd input {
    border: none;
    outline: none;
    padding: 0;
    margin: 0;
    width: auto;
    font-size: inherit;
    color: inherit;
    -webkit-appearance: none;
    appearance: none;
    line-height: normal;
    background: transparent;
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
        $html .= '<div id="formItemBytemd_' . $this->name . '"></div>';

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

        // 中文 locale
        $localeJs = '{';
        $localeJs .= 'Bold:"加粗",boldText:"加粗文本",cheatsheet:"Markdown 语法",closeHelp:"关闭帮助",closeToc:"关闭目录",code:"代码",codeBlock:"代码块",codeLang:"语言",codeText:"代码",exitFullscreen:"退出全屏",exitPreviewOnly:"退出仅预览",exitWriteOnly:"退出仅编辑",fullscreen:"全屏",h1:"标题 1",h2:"标题 2",h3:"标题 3",h4:"标题 4",h5:"标题 5",h6:"标题 6",headingText:"标题",help:"帮助",hr:"水平分割线",image:"图片",imageAlt:"替代文本",imageTitle:"标题",italic:"斜体",italicText:"斜体文本",limited:"已达到最大字符限制",lines:"行",link:"链接",linkText:"链接文本",ol:"有序列表",olItem:"列表项",preview:"预览",previewOnly:"仅预览",quote:"引用",quotedText:"引用文本",shortcuts:"快捷键",source:"源代码",sync:"滚动同步",toc:"目录",top:"回到顶部",ul:"无序列表",ulItem:"列表项",words:"字数",write:"编辑",writeOnly:"仅编辑"';
        $localeJs .= '}';

        // 构建插件数组
        // UMD 导出的全局变量本身就是插件工厂函数，直接调用即可
        $pluginsJs = '[';
        if (in_array('gfm', $this->plugins)) {
            $pluginsJs .= 'bytemdPluginGfm({locale:{strike:"删除线",strikeText:"文本",table:"表格",tableHeading:"表头",task:"任务列表",taskText:"待办"}}),';
        }
        if (in_array('highlight', $this->plugins)) {
            $pluginsJs .= 'bytemdPluginHighlight(),';
        }
        if (in_array('frontmatter', $this->plugins)) {
            $pluginsJs .= 'bytemdPluginFrontmatter(),';
        }
        if (in_array('mermaid', $this->plugins)) {
            $pluginsJs .= 'bytemdPluginMermaid({locale:{"class":"类图",er:"关系图",flowchart:"流程图",gantt:"甘特图",mermaid:"Mermaid图表",mindmap:"思维导图",pie:"饼状图",sequence:"时序图",state:"状态图",timeline:"时间轴",uj:"旅程图"}}),';
        }
        if (in_array('math', $this->plugins)) {
            $pluginsJs .= 'bytemdPluginMath({locale:{block:"块级公式",blockText:"公式",inline:"行内公式",inlineText:"公式"}}),';
        }
        if (in_array('gemoji', $this->plugins)) {
            $pluginsJs .= 'bytemdPluginGemoji(),';
        }
        if (in_array('be-storage', $this->plugins)) {
            $beImageCallback = base64_encode('parent.window._bytemdBeStorageImageSelected(files);');
            $beImageCallbackIframe = base64_encode('parent.be.getActiveIframe()._bytemdBeStorageImageSelected(files);');
            $beFileCallback = base64_encode('parent.window._bytemdBeStorageFileSelected(files);');
            $beFileCallbackIframe = base64_encode('parent.be.getActiveIframe()._bytemdBeStorageFileSelected(files);');

            $beStorageImageUrl = beAdminUrl('System.Storage.pop', ['filterImage' => 1, 'confirmMode' => 1, 'callback' => $beImageCallback]);
            $beStorageImageUrlIframe = beAdminUrl('System.Storage.pop', ['filterImage' => 1, 'confirmMode' => 1, 'callback' => $beImageCallbackIframe]);
            $beStorageFileUrl = beAdminUrl('System.Storage.pop', ['confirmMode' => 1, 'callback' => $beFileCallback]);
            $beStorageFileUrlIframe = beAdminUrl('System.Storage.pop', ['confirmMode' => 1, 'callback' => $beFileCallbackIframe]);

            $pluginsJs .= 'bytemdPluginBeStorage({'
                . 'imageUrl:"' . $beStorageImageUrl . '",'
                . 'imageUrlIframe:"' . $beStorageImageUrlIframe . '",'
                . 'fileUrl:"' . $beStorageFileUrl . '",'
                . 'fileUrlIframe:"' . $beStorageFileUrlIframe . '",'
                . 'insertImageLabel:"插入图片",'
                . 'insertFileLabel:"插入文件"'
                . '}),';
        }
        $pluginsJs .= ']';

        $mountedCode = '';
        $mountedCode .= 'let this_' . $rand . ' = this;';
        $mountedCode .= 'let editor_' . $rand . ' = new bytemd.Editor({';
        $mountedCode .= 'target: document.getElementById("formItemBytemd_' . $this->name . '"),';
        $mountedCode .= 'props: {';
        $mountedCode .= 'value: this_' . $rand . '.formData.' . $this->name . ' || "",';
        $mountedCode .= 'plugins: ' . $pluginsJs . ',';
        $mountedCode .= 'locale: ' . $localeJs . ',';
        $mountedCode .= '}';
        $mountedCode .= '});';

        $mountedCode .= 'editor_' . $rand . '.$on("change", function(e) {';
        if ($this->valueFormat === 'html') {
            $mountedCode .= 'let v = bytemd.getProcessor(e.detail.value).toString();';
            $mountedCode .= 'this_' . $rand . '.formData.' . $this->name . ' = v;';
        } else {
            $mountedCode .= 'this_' . $rand . '.formData.' . $this->name . ' = e.detail.value;';
        }
        $mountedCode .= 'editor_' . $rand . '.$set({value: e.detail.value});';
        $mountedCode .= $onChangeCallback;
        $mountedCode .= '});';

        $mountedCode .= 'this_' . $rand . '.formItems.' . $this->name . '.instance = editor_' . $rand . ';';

        return [
            'mounted' => $mountedCode,
        ];
    }

}
