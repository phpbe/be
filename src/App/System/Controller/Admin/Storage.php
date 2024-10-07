<?php

namespace Be\App\System\Controller\Admin;

use Be\App\ControllerException;
use Be\App\ServiceException;
use Be\Be;
use Be\Util\File\FileSize;
use Be\Util\File\Mime;
use Be\Util\Net\FileUpload;

/**
 * 存储管理器
 *
 * @BeMenuGroup("控制台")
 * @BePermissionGroup("控制台")
 */
class Storage extends Auth
{

    /**
     * @BeMenu("存储", icon = "el-icon-folder", ordering="3.4")
     * @BePermission("存储", ordering="3.4")
     */
    public function index()
    {
        
        
        $session = Be::getSession();

        $sessionKeyPath = 'be-storage-path';
        $sessionKeyView = 'be-storage-view';

        if (Request::isAjax()) {

            $postData = Request::json();
            $formData = $postData['formData'] ?? [];

            // 要查看的路径
            $path = $formData['path'] ?? '';
            if ($path === '') {
                $path = '/';
            }
            $session->set($sessionKeyPath, $path);
            Resonse::set('path', $path);

            // 显示方式 thumbnail 缩略图 list 详细列表
            $view = $formData['view'] ?? '';
            if ($view !== 'thumbnail' && $view !== 'list') {
                $view = 'thumbnail';
            }
            $session->set($sessionKeyView, $view);
            Resonse::set('view', $view);

            if (isset($postData['toggleView']) && $postData['toggleView']) {
                Resonse::set('success', true);
                Resonse::json();
                return;
            }

            // 获取文件列表
            $option = [];
            $storage = Be::getStorage();
            $files = $storage->getFiles($path, $option);
            Resonse::set('files', $files);

            Resonse::set('success', true);
            Resonse::json();
        } else {
            // 要查看的路径
            $path = $session->get($sessionKeyPath, '');
            if ($path === '') {
                $path = '/';
            }
            Resonse::set('path', $path);

            // 显示方式 thumbnail 缩略图 list 详细列表
            $view = $session->get($sessionKeyView, '');
            if ($view !== 'thumbnail' && $view !== 'list') {
                $view = 'thumbnail';
            }
            Resonse::set('view', $view);

            $configStorage = Be::getConfig('App.System.Storage');

            $storageDriver = '';
            switch ($configStorage->driver) {
                case 'LocalDisk':
                    $storageDriver = '本地磁盘';
                    break;
                case 'AliyunOss':
                    $storageDriver = '阿里云OSS';
                    break;
                case 'TencentCos':
                    $storageDriver = '腾讯云COS';
                    break;
                case 'AwsS3':
                    $storageDriver = '亚马逊AWS S3';
                    break;
            }

            Resonse::set('title', '存储（' . $storageDriver . '）');
            Resonse::display();
        }
    }

    /**
     * @BePermission("弹窗管理", ordering="3.41")
     */
    public function pop()
    {
        
        
        $session = Be::getSession();

        // 是否启用过滤，只显示图像
        $filterImage = Request::get('filterImage', -1, 'int');
        if ($filterImage !== 0 && $filterImage !== 1) {
            $filterImage = 0;
        }
        Resonse::set('filterImage', $filterImage);

        // JS 回调代码，base64编码
        $callback = Request::get('callback', '');
        if ($callback) {
            $callback = base64_decode($callback);
        }
        Resonse::set('callback', $callback);

        $sessionKeyPath = 'be-storage-' . $filterImage . '-path';
        $sessionKeyView = 'be-storage-' . $filterImage . '-view';

        if (Request::isAjax()) {

            $postData = Request::json();
            $formData = $postData['formData'] ?? [];

            // 要查看的路径
            $path = $formData['path'] ?? '';
            if ($path === '') {
                $path = '/';
            }
            $session->set($sessionKeyPath, $path);
            Resonse::set('path', $path);

            // 显示方式 thumbnail 缩略图 list 详细列表
            $view = $formData['view'] ?? '';
            if ($view !== 'thumbnail' && $view !== 'list') {
                $view = 'thumbnail';
            }
            $session->set($sessionKeyView, $view);
            Resonse::set('view', $view);

            if (isset($postData['toggleView']) && $postData['toggleView']) {
                Resonse::set('success', true);
                Resonse::json();
                return;
            }

            // 获取文件列表
            $option = [];
            $option['filterImage'] = $filterImage;
            $storage = Be::getStorage();
            $files = $storage->getFiles($path, $option);
            Resonse::set('files', $files);

            Resonse::set('success', true);
            Resonse::json();
        } else {
            // 要查看的路径
            $path = $session->get($sessionKeyPath, '');
            if ($path === '') {
                $path = '/';
            }
            Resonse::set('path', $path);

            // 显示方式 thumbnail 缩略图 list 详细列表
            $view = $session->get($sessionKeyView, '');
            if ($view !== 'thumbnail' && $view !== 'list') {
                $view = 'thumbnail';
            }
            Resonse::set('view', $view);

            Resonse::display();
        }
    }

    /**
     * @BePermission("创建文件夹", ordering="3.42")
     */
    public function createDir()
    {
        
        

        $postData = Request::json();
        $formData = $postData['formData'] ?? [];

        try {
            if (!isset($formData['path'])) {
                throw new ControllerException('参数（path）缺失！');
            }
            $path = $formData['path'];

            if (!isset($formData['dirName'])) {
                throw new ControllerException('参数（dirName）缺失！');
            }
            $dirName = $formData['dirName'];

            $storage = Be::getStorage();
            $fullPath = $path . $dirName . '/';
            if ($storage->isDirExist($fullPath)) {
                throw new ControllerException('文件夹（' . $fullPath . '）已存在！');
            }
            $storage->createDir($fullPath);

            Resonse::set('success', true);
            Resonse::set('message', '创建文件夹成功！');
            Resonse::json();
        } catch (\Throwable $t) {
            Resonse::set('success', false);
            Resonse::set('message', '创建文件夹失败：' . $t->getMessage());
            Resonse::json();
        }
    }

    /**
     * 修改文件夹名称
     *
     * @BePermission("修改文件夹名称", ordering="3.43")
     */
    public function renameDir()
    {
        
        

        $postData = Request::json();
        $formData = $postData['formData'] ?? [];

        try {
            if (!isset($formData['path'])) {
                throw new ControllerException('参数（path）缺失！');
            }
            $path = $formData['path'];

            if (!isset($formData['oldDirName'])) {
                throw new ControllerException('参数（oldDirName）缺失！');
            }
            $oldDirName = $formData['oldDirName'];

            if (!isset($formData['newDirName'])) {
                throw new ControllerException('参数（newDirName）缺失！');
            }
            $newDirName = $formData['newDirName'];

            $storage = Be::getStorage();
            $oldFullPath = $path . $oldDirName . '/';
            $newFullPath = $path . $newDirName . '/';

            if ($storage->isDirExist($newFullPath)) {
                throw new ControllerException('文件夹（' . $newFullPath . '）已存在！');
            }

            $storage->renameDir($oldFullPath, $newFullPath);

            Resonse::set('success', true);
            Resonse::set('message', '修改文件夹名称成功！');
            Resonse::json();
        } catch (\Throwable $t) {
            Resonse::set('success', false);
            Resonse::set('message', '修改文件夹名称失败：' . $t->getMessage());
            Resonse::json();
        }
    }

    /**
     * 删除文件夹
     *
     * @BePermission("删除文件夹", ordering="3.44")
     */
    public function deleteDir()
    {
        
        

        $postData = Request::json();
        $formData = $postData['formData'] ?? [];

        try {
            if (!isset($formData['path'])) {
                throw new ControllerException('参数（path）缺失！');
            }
            $path = $formData['path'];

            if (!isset($formData['dirName'])) {
                throw new ControllerException('参数（dirName）缺失！');
            }
            $dirName = $formData['dirName'];

            $storage = Be::getStorage();
            $fullPath = $path . $dirName . '/';

            $storage->deleteDir($fullPath);

            Resonse::set('success', true);
            Resonse::set('message', '删除文件夹成功！');
            Resonse::json();
        } catch (\Throwable $t) {
            Resonse::set('success', false);
            Resonse::set('message', '删除文件夹失败：' . $t->getMessage());
            Resonse::json();
        }
    }

    /**
     * 上传图像
     *
     * @BePermission("上传图像", ordering="3.45")
     */
    public function uploadImage()
    {
        
        

        try {
            $path = Request::post('path');
            if (!$path) {
                throw new ControllerException('参数（path）缺失！');
            }

            $file = Request::files('file');
            if ($file['error'] !== 0) {
                throw new ControllerException(FileUpload::errorDescription($file['error']));
            }

            $configSystem = Be::getConfig('App.System.System');
            $maxSize = $configSystem->uploadMaxSize;
            $maxSizeInt = FileSize::string2Int($maxSize);
            $size = filesize($file['tmp_name']);
            if ($size > $maxSizeInt) {
                throw new ControllerException('您上传的文件尺寸已超过最大限制：' . $maxSize . '！');
            }

            $fulName = trim($file['name']);
            $defaultExt = strrchr($fulName, '.');
            if ($defaultExt && strlen($defaultExt) > 1) {
                $defaultExt = substr($defaultExt, 1);
                $defaultExt = strtolower($defaultExt);
                $defaultExt = trim($defaultExt);
            } else {
                $defaultExt = '';
            }

            $name = $fulName;
            $rPos = strrpos($fulName, '.');
            if ($rPos !== false) {
                $name = substr($fulName, 0, $rPos);
            }

            $fileExt = Mime::detectExt($file['tmp_name'], $defaultExt);

            if (!in_array($fileExt, $configSystem->allowUploadImageTypes)) {
                throw new ControllerException('禁止上传的图像类型：' . $fileExt . '！');
            }

            $storage = Be::getStorage();
            $fullPath = $path . $name . '.' . $fileExt;

            if ($storage->isFileExist($fullPath)) {
                throw new ControllerException('图像（' . $fullPath . '）已存在！');
            }

            $url = $storage->uploadFile($fullPath, $file['tmp_name']);

            Resonse::set('success', true);
            Resonse::set('message', '上传成功！');
            Resonse::set('url', $url);
            Resonse::json();
        } catch (\Throwable $t) {
            Resonse::set('success', false);
            Resonse::set('message', '上传图像失败：' . $t->getMessage());
            Resonse::json();
        }
    }

    /**
     * 上传文件
     *
     * @BePermission("上传文件", ordering="3.46")
     */
    public function uploadFile()
    {
        
        

        try {
            $path = Request::post('path');
            if (!$path) {
                throw new ControllerException('参数（path）缺失！');
            }

            $file = Request::files('file');
            if ($file['error'] !== 0) {
                throw new ControllerException(FileUpload::errorDescription($file['error']));
            }

            $configSystem = Be::getConfig('App.System.System');
            $maxSize = $configSystem->uploadMaxSize;
            $maxSizeInt = FileSize::string2Int($maxSize);
            $size = filesize($file['tmp_name']);
            if ($size > $maxSizeInt) {
                throw new ControllerException('您上传的文件尺寸已超过最大限制：' . $maxSize . '！');
            }

            $fulName = trim($file['name']);
            $defaultExt = strrchr($fulName, '.');
            if ($defaultExt && strlen($defaultExt) > 1) {
                $defaultExt = substr($defaultExt, 1);
                $defaultExt = strtolower($defaultExt);
                $defaultExt = trim($defaultExt);
            } else {
                $defaultExt = '';
            }

            $name = $fulName;
            $rPos = strrpos($fulName, '.');
            if ($rPos !== false) {
                $name = substr($fulName, 0, $rPos);
            }

            $fileExt = Mime::detectExt($file['tmp_name'], $defaultExt);

            if (!in_array($fileExt, $configSystem->allowUploadFileTypes)) {
                throw new ControllerException('禁止上传的文件类型：' . $fileExt . '！');
            }

            $storage = Be::getStorage();
            $fullPath = $path . $name . '.' . $fileExt;

            if ($storage->isFileExist($fullPath)) {
                throw new ControllerException('文件（' . $fullPath . '）已存在！');
            }

            $url = $storage->uploadFile($fullPath, $file['tmp_name']);

            Resonse::set('success', true);
            Resonse::set('message', '上传文件成功！');
            Resonse::set('url', $url);
            Resonse::json();
        } catch (\Throwable $t) {
            Resonse::set('success', false);
            Resonse::set('message', '上传文件失败：' . $t->getMessage());
            Resonse::json();
        }
    }

    /**
     * 修改文件名称
     *
     * @BePermission("修改文件名称", ordering="3.47")
     */
    public function renameFile()
    {
        
        

        $postData = Request::json();
        $formData = $postData['formData'] ?? [];

        try {

            $filterImage = Request::get('filterImage', -1, 'int');

            if (!isset($formData['path'])) {
                throw new ControllerException('参数（path）缺失！');
            }
            $path = $formData['path'];

            if (!isset($formData['oldFileName'])) {
                throw new ControllerException('参数（oldFileName）缺失！');
            }
            $oldFileName = $formData['oldFileName'];

            if (!isset($formData['newFileName'])) {
                throw new ControllerException('参数（newFileName）缺失！');
            }
            $newFileName = $formData['newFileName'];

            $ext = '';
            $rPos = strrpos($newFileName, '.');
            if ($rPos !== false) {
                $ext = substr($newFileName, $rPos + 1);
            }

            $configSystem = Be::getConfig('App.System.System');
            if ($filterImage) {
                if (!in_array($ext, $configSystem->allowUploadImageTypes)) {
                    throw new ControllerException('禁止使用的图像类型：' . $ext . '！');
                }
            } else {
                if (!in_array($ext, $configSystem->allowUploadFileTypes)) {
                    throw new ControllerException('禁止使用的文件类型：' . $ext . '！');
                }
            }

            $storage = Be::getStorage();
            $oldFullPath = $path . $oldFileName;
            $newFullPath = $path . $newFileName;

            if ($storage->isFileExist($newFullPath)) {
                throw new ControllerException('文件（' . $newFullPath . '）已存在！');
            }

            $storage->renameFile($oldFullPath, $newFullPath);

            Resonse::set('success', true);
            Resonse::set('message', '修改文件名称成功！');
            Resonse::json();
        } catch (\Throwable $t) {
            Resonse::set('success', false);
            Resonse::set('message', '修改文件名称失败：' . $t->getMessage());
            Resonse::json();
        }
    }

    /**
     * 删除文件
     *
     * @BePermission("删除文件", ordering="3.48")
     */
    public function deleteFile()
    {
        
        

        $postData = Request::json();
        $formData = $postData['formData'] ?? [];

        try {
            if (!isset($formData['path'])) {
                throw new ControllerException('参数（path）缺失！');
            }
            $path = $formData['path'];

            if (!isset($formData['fileName'])) {
                throw new ControllerException('参数（fileName）缺失！');
            }
            $fileName = $formData['fileName'];

            $storage = Be::getStorage();
            $fullPath = $path . $fileName;

            $storage->deleteFile($fullPath);

            Resonse::set('success', true);
            Resonse::set('message', '删除文件成功！');
            Resonse::json();
        } catch (\Throwable $t) {
            Resonse::set('success', false);
            Resonse::set('message', '删除文件失败：' . $t->getMessage());
            Resonse::json();
        }
    }


}
