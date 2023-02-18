<?php

namespace Be\AdminPlugin\Form\Item;

use Be\AdminPlugin\AdminPluginException;

/**
 * 表单项 菜单
 */
class FormItemMenu extends FormItem
{

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
                $this->ui['form-item'][':rules'] = '[{required: true, message: \'请选择' . $this->label . '\', trigger: \'blur\' }]';
            }
        }

        if (!isset($this->ui['@click'])) {
            $this->ui['@click'] = 'formItemMenu_' . $this->name . '_select';
        }

        if (!isset($this->ui['type'])) {
            $this->ui['type'] = 'primary';
        }

        if (!isset($this->ui['size'])) {
            $this->ui['size'] = 'medium';
        }

        if ($this->name !== null) {
            if (!isset($this->ui['v-model'])) {
                $this->ui['v-model'] = 'formData.' . $this->name;
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
        $html = '<el-form-item';
        foreach ($this->ui['form-item'] as $k => $v) {
            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';

        $html .= '<div v-if="formData.' . $this->name . '">';
        $html .= '<el-link :href="formData.' . $this->name . '" title="' . $this->label . '" type="primary" :underline="false" target="_blank">{{formData.' . $this->name . '}}</el-link> ';
        $html .= '<el-link type="danger" icon="el-icon-delete" @click="formItemMenu_' . $this->name . '_delete"></el-link>';
        $html .= '</div>';

        $html .= '<div><el-button';
        foreach ($this->ui as $k => $v) {
            if ($k === 'form-item') {
                continue;
            }

            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>选择文件</el-button></div>';

        if ($this->description) {
            $html .= '<div class="be-c-999 be-mt-50 be-lh-150">' . $this->description . '</div>';
        }

        $html .= '</el-form-item>';
        return $html;
    }

    /**
     * 获取需要引入的 JS 代码
     *
     * @return false | string
     */
    public function getJsCode()
    {
        return 'function formItemMenu_' . $this->name . '_selected(files){vueForm.formItemMenu_' . $this->name . '_selected(files);}';
    }



    /**
     * 获取 vue 方法
     *
     * @return false | array
     */
    public function getVueMethods()
    {
        $imageCallback = base64_encode('parent.be.getActiveIframe().formItemMenu_' . $this->name . '_selected(files);');
        $iframeUrl = beAdminUrl('System.Storage.pop', ['callback' => $imageCallback]);

        $imageCallback = base64_encode('parent.formItemMenu_' . $this->name . '_selected(files);');
        $url = beAdminUrl('System.Storage.pop', ['callback' => $imageCallback]);

        return [
            'formItemMenu_' . $this->name . '_select' => 'function () {
                if (window.frameElement && window.frameElement.tagName == "IFRAME") {
                    parent.be.setActiveIframe(window);
                    parent.be.openDialog("选择一个文件", "' . $iframeUrl . '");
                } else {
                    be.openDialog("选择一个文件", "' . $url . '");
                }
            }',
            'formItemMenu_' . $this->name . '_selected' => 'function (files) {
                if (files.length > 0) {
                    let file = files[0];
                    this.formData.' . $this->name . ' = file.url;
                }
                
                if (window.frameElement && window.frameElement.tagName == "IFRAME") {
                    parent.be.closeDialog();
                } else {
                    be.closeDialog();
                }
            }',
            'formItemMenu_' . $this->name . '_delete' => 'function () {
                this.formData.' . $this->name . ' = "";
            }',
        ];
    }

}
