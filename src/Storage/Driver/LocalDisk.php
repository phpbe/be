<?php

namespace Be\Storage\Driver;

use Be\Be;
use Be\Storage\StorageException;
use Be\Storage\Driver;

class LocalDisk extends Driver
{

    /**
     * 获取跟网址
     *
     * @return string 跟网址
     */
    public function getRootUrl()
    {
        return Be::getConfig('App.System.StorageLocalDisk')->rootUrl;
    }

    /**
     * 上传文件
     *
     * @param string $path 文件存储路径
     * @param string $tmpFile 上传的临时文件名
     * @return string 上传成功的文件的网址
     * @throws StorageException
     */
    public function uploadFile(string $path, string $tmpFile)
    {
        $config = Be::getConfig('App.System.StorageLocalDisk');

        $uploadPath = Be::getRuntime()->getUploadPath();

        $newFilePath = $uploadPath . $path;
        $dir = dirname($newFilePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            @chmod($dir, 0755);
        }

        if (strpos($tmpFile, $uploadPath) !== false) {
            // 移动文件
            if (file_exists($tmpFile)) {
                @copy($tmpFile, $newFilePath);
                @unlink($tmpFile);
            } else {
                throw new StorageException('Upload file error!!!');
            }
        } else {
            if (move_uploaded_file($tmpFile, $newFilePath)) {
                throw new StorageException('Upload file error!');
            }
        }

        return $config->rootUrl . $path;
    }

    /**
     * 删除文件
     *
     * @param string $path 文件存储路径
     */
    public function removeFile(string $path)
    {
        $filePath = Be::getRuntime()->getUploadPath() . $path;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

}
