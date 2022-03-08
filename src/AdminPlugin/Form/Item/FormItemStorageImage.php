<?php

namespace Be\AdminPlugin\Form\Item;

use Be\Be;
use Be\AdminPlugin\AdminPluginException;


/**
 * 表单项 图像
 */
class FormItemStorageImage extends FormItem
{

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
                $this->ui['form-item'][':rules'] = '[{required: true, message: \'请选择' . $this->label . '\', trigger: \'blur\' }]';
            }
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

        $html .= '<div class="be-row">';
        $html .= '<div class="be-col-auto">';
        $html .= '<div v-if="formData.' . $this->name . '" class="form-item-storage-image">';
        $html .= '<img :src="formData.' . $this->name . '" alt="' . $this->label . '">';
        $html .= '<div class="form-item-storage-image-actions">';
        $html .= '<span class="form-item-storage-image-action" @click="formItemStorageImage_' . $this->name . '_preview"><i class="el-icon-zoom-in"></i></span>';
        $html .= '<span class="form-item-storage-image-action" @click="formItemStorageImage_' . $this->name . '_delete"><i class="el-icon-delete"></i></span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="be-col-auto">';
        $html .= '<div class="form-item-storage-image-selector" @click="formItemStorageImage_' . $this->name . '_select"><i class="el-icon-plus"></i></div>';
        $html .= '</div>';
        $html .= '</div>';

        if ($this->description) {
            $html .= '<div class="be-c-bbb be-mt-50 be-lh-150">' . $this->description . '</div>';
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
        return 'function formItemStorageImage_' . $this->name . '_selected(files){vueForm.formItemStorageImage_' . $this->name . '_selected(files);}';
    }

    /**
     * 获取需要引入的 CSS 代码
     *
     * @return false | array
     */
    public function getCss()
    {
        return [
            Be::getProperty('AdminPlugin.Form')->getUrl() . '/Template/css/form-item-storage-image.css',
        ];
    }

    /**
     * 获取 vue 方法
     *
     * @return false | array
     */
    public function getVueMethods()
    {
        $imageCallback = base64_encode('parent.be.getActiveIframe().formItemStorageImage_' . $this->name . '_selected(files);');
        $iframeUrl = beAdminUrl('System.Storage.pop', ['filterImage' => 1, 'callback' => $imageCallback]);

        $imageCallback = base64_encode('parent.formItemStorageImage_' . $this->name . '_selected(files);');
        $url = beAdminUrl('System.Storage.pop', ['filterImage' => 1, 'callback' => $imageCallback]);

        return [
            'formItemStorageImage_' . $this->name . '_select' => 'function () {
                if (window.frameElement && window.frameElement.tagName == "IFRAME") {
                    parent.be.setActiveIframe(window);
                    parent.be.openDialog("选择一个图像", "' . $iframeUrl . '");
                } else {
                    be.openDialog("选择一个图像", "' . $url . '");
                }
            }',
            'formItemStorageImage_' . $this->name . '_selected' => 'function (files) {
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
            'formItemStorageImage_' . $this->name . '_preview' => 'function () {
                if (window.frameElement && window.frameElement.tagName == "IFRAME") {
                    parent.be.openDialog("图像预览", this.formData.' . $this->name . ');
                } else {
                    be.openDialog("图像预览", this.formData.' . $this->name . ');
                }
            }',
            'formItemStorageImage_' . $this->name . '_delete' => 'function () {
                this.formData.' . $this->name . ' = "";
            }',
        ];
    }

}
