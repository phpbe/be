<?php

namespace Be\AdminPlugin\Form\Item;

use Be\Be;
use Be\AdminPlugin\AdminPluginException;
use Be\Util\FileSystem\FileSize;

/**
 * 表单项 图像
 */
class FormItemImage extends FormItem
{

    public $path = ''; // 保存路径
    public $maxSizeInt = 0; // 最大尺寸（整型字节数）
    public $maxSize = ''; // 最大尺寸（字符类型）
    public $allowUploadImageTypes = []; // 允许上传的图像类型
    public $maxWidth = 0;
    public $maxHeight = 0;
    public $filename = ''; // 指定存储的文件名

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

        if (!isset($params['path'])) {
            throw new AdminPluginException('参数' . $this->label . ' (' . $this->name . ') 须指定保存路径（path）');
        }
        $this->path = $params['path'];

        if (isset($params['uploadMaxSize'])) {
            $this->maxSize = $params['uploadMaxSize'];
        } else {
            $this->maxSize = Be::getConfig('App.System.System')->uploadMaxSize;
        }
        $this->maxSizeInt = FileSize::string2Int($this->maxSize);

        // 允许上传的图像类型
        if (isset($params['allowUploadImageTypes'])) {
            $this->allowUploadImageTypes = $params['allowUploadImageTypes'];
        } else {
            $this->allowUploadImageTypes = Be::getConfig('App.System.System')->allowUploadImageTypes;
        }

        if ($this->required) {
            if (!isset($this->ui['form-item'][':rules'])) {
                $this->ui['form-item'][':rules'] = '[{required: true, message: \'请上传' . $this->label . '\', trigger: \'blur\' }]';
            }
        }

        // 最大宽度
        if (isset($params['maxWidth']) && is_numeric($params['maxWidth']) && $params['maxWidth'] > 0) {
            $this->maxWidth = $params['maxWidth'];
        }

        // 最大高度
        if (isset($params['maxHeight']) && is_numeric($params['maxHeight']) && $params['maxHeight'] > 0) {
            $this->maxHeight = $params['maxHeight'];
        }

        // 文件名
        if (isset($params['filename'])) {
            $this->filename = $params['filename'];
        }

        if (!$this->description) {
            $this->description = '格式：' . implode(', ', $this->allowUploadImageTypes) . '，' . $this->maxSize . ' 以内';
        }

        if (!isset($this->ui['accept'])) {
            $this->ui['accept'] = implode(',', $this->allowUploadImageTypes);
        }

        if (!isset($this->ui[':on-success'])) {
            $this->ui[':on-success'] = 'formItemImage_' . $this->name . '_onSuccess';
        }

        if (!isset($this->ui[':before-upload'])) {
            $this->ui[':before-upload'] = 'formItemImage_' . $this->name . '_beforeUpload';
        }

        if (!isset($this->ui[':on-error'])) {
            $this->ui[':on-error'] = 'formItemImage_onError';
        }

        if (!isset($this->ui[':file-list'])) {
            $this->ui[':file-list'] = 'formItems.' . $this->name . '.fileList';
        }

        if (!isset($this->ui[':data'])) {
            $this->ui[':data'] = 'formItems.' . $this->name . '.postData';
        }

        if (!isset($this->ui['limit'])) {
            $this->ui['limit'] = 1;
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
        if (!isset($this->ui['action'])) {
            $this->ui['action'] = beAdminUrl('System.AdminPlugin.uploadImage');
        }

        $html = '<el-form-item';
        foreach ($this->ui['form-item'] as $k => $v) {
            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';

        $html .= '<img v-if="formData.' . $this->name . '" :src="formItems.' . $this->name . '.url" alt="' . $this->label . '" style="max-width:120px;" />';

        $html .= '<el-upload';
        foreach ($this->ui as $k => $v) {
            if ($k == 'form-item') {
                continue;
            }

            if ($v === null) {
                $html .= ' ' . $k;
            } else {
                $html .= ' ' . $k . '="' . $v . '"';
            }
        }
        $html .= '>';
        $html .= '<el-button size="medium" type="primary"><i class="el-icon-upload2"></i> 选择图像文件</el-button>';
        $html .= '<div class="el-upload__tip" slot="tip">' . $this->description . '</div>';
        $html .= '</el-upload>';
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
        $url = null;
        if (strpos($this->value, '/') === false) {
            $url = Be::getRequest()->getUploadUrl() . $this->path . $this->value;
        } else {
            $url = $this->value;
        }

        return [
            'formItems' => [
                $this->name => [
                    'url' => $url,
                    'fileList' => [],
                    'postData' => [
                        'maxWidth' => $this->maxWidth,
                        'maxHeight' => $this->maxHeight,
                        'filename' => $this->filename
                    ],
                ]
            ]
        ];
    }


    /**
     * 获取 vue 方法
     *
     * @return false | array
     */
    public function getVueMethods()
    {
        return [
            'formItemImage_' . $this->name . '_beforeUpload' => 'function(file) {
                if (file.size > ' . $this->maxSizeInt . '){
                    this.$message.error("' . $this->label . ' 图像尺寸须小于 ' . $this->maxSize . '！");
                    return false;
                }
                return true;
            }',
            'formItemImage_' . $this->name . '_onSuccess' => 'function (response, file, fileList) {
                if (response.success) {
                    this.formItems.' . $this->name . '.url = response.url;
                    this.formData.' . $this->name . ' = response.newValue;
                } else {
                    this.$message.error(response.message);
                }
                this.formItems.' . $this->name . '.fileList = [];
            }',
            'formItemImage_onError' => 'function(){
                this.$message.error("上传失败，请重新上传");
            }',
        ];
    }

    /**
     * 提交处理
     *
     * @param $data
     * @throws AdminPluginException
     */
    public function submit($data)
    {
        if (isset($data[$this->name])) {
            $newValue = $data[$this->name];
            $newValue = htmlspecialchars_decode($newValue);

            if ($newValue != $this->value && $this->value != '') {
                $lastPath = Be::getRuntime()->getUploadPath() . $this->path . $this->value;
                if (file_exists($lastPath)) {
                    @unlink($lastPath);
                }
            }

            $pathDstDir = Be::getRuntime()->getUploadPath() . $this->path;
            if (!file_exists($pathDstDir)) {
                mkdir($pathDstDir, 0755, true);
            }

            $pathSrc = Be::getRuntime()->getUploadPath() . '/tmp/' . $newValue;
            $pathDst = $pathDstDir . $newValue;
            if (file_exists($pathSrc)) {
                @copy($pathSrc, $pathDst);
                @unlink($pathSrc);
            }

            $this->newValue = $newValue;
        }
    }

}
