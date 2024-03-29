<?php

namespace Be\AdminPlugin\Form\Item;

use Be\Be;
use Be\AdminPlugin\AdminPluginException;
use Be\Util\File\FileSize;

/**
 * 表单项 整型
 */
class FormItemFile extends FormItem
{

    protected string $path = ''; // 保存路径
    protected int $maxSizeInt = 0; // 最大尺寸（整型字节数）
    protected string $maxSize = ''; // 最大尺寸（字符类型）
    protected array $allowUploadFileTypes = []; // 允许上传的文件类型

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

        // 允许上传的文件类型
        if (isset($params['allowUploadFileTypes'])) {
            $this->allowUploadFileTypes = $params['allowUploadFileTypes'];
        } else {
            $this->allowUploadFileTypes = Be::getConfig('App.System.System')->allowUploadFileTypes;
        }

        if (!$this->description) {
            $this->description = '格式：' . implode(', ', $this->allowUploadFileTypes) . '，' . $this->maxSize . ' 以内';
        }

        if ($this->required) {
            if (!isset($this->ui['form-item'][':rules'])) {
                $this->ui['form-item'][':rules'] = '[{required: true, message: \'请上传' . $this->label . '\', trigger: \'blur\' }]';
            }
        }

        if (!isset($this->ui['accept'])) {
            $this->ui['accept'] = implode(',', $this->allowUploadFileTypes);
        }

        if (!isset($this->ui[':on-success'])) {
            $this->ui[':on-success'] = 'formItemFile_' . $this->name . '_onSuccess';
        }

        if (!isset($this->ui[':before-upload'])) {
            $this->ui[':before-upload'] = 'formItemFile_' . $this->name . '_beforeUpload';
        }

        if (!isset($this->ui[':on-error'])) {
            $this->ui[':on-error'] = 'formItemFile_onError';
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
    public function getHtml(): string
    {
        if (!isset($this->ui['action'])) {
            $this->ui['action'] = beAdminUrl('System.AdminPlugin.uploadFile');
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

        $html .= '<a v-if="formData.' . $this->name . '" :href="formItems.' . $this->name . '.url" target="_blank">{{formData.' . $this->name . '}}</a>';
        $html .= '<el-upload';
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
        $html .= '>';
        $html .= '<el-button size="medium" type="primary"><i class="el-icon-upload2"></i> 选择文件</el-button>';
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
            $url = Be::getStorage()->getRootUrl() . $this->path . $this->value;
        } else {
            $url = $this->value;
        }

        return [
            'formItems' => [
                $this->name => [
                    'url' => $url,
                    'fileList' => [],
                    'postData' => [
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
            'formItemFile_' . $this->name . '_beforeUpload' => 'function(file) {
                if (file.size > ' . $this->maxSizeInt . '){
                    this.$message.error("' . $this->label . ' 文件尺寸须小于 ' . $this->maxSize . '！");
                    return false;
                }
                return true;
            }',
            'formItemFile_' . $this->name . '_onSuccess' => 'function (response, file, fileList) {
                if (response.success) {
                    this.formItems.' . $this->name . '.url = response.url;
                    this.formData.' . $this->name . ' = response.newValue;
                } else {
                    this.$message.error(response.message);
                }
                this.formItems.' . $this->name . '.fileList = [];
            }',
            'formItemFile_onError' => 'function(){
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
            if ($newValue !== $this->value) {
                $storage = Be::getStorage();
                if ($this->value !== null) {
                    $oldPath = $this->path . $this->value;
                    $storage->deleteFile($oldPath);
                }

                $newPath = $this->path . $newValue;
                if (!$storage->isFileExist($newPath)) {
                    $file = Be::getRuntime()->getRootPath() . '/www/tmp/' . $newValue;
                    $url = $storage->uploadFile($newPath, $file);
                }
            }

            $this->newValue = $newValue;
        }
    }


}
