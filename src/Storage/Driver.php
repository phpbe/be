<?php

namespace Be\Storage;

use Be\Be;

/**
 * 存储 驱动
 */
abstract class Driver
{

    /**
     * 获取跟网址
     *
     * @return string 根网址
     */
    abstract function getRootUrl(): string;

    /**
     * 获取文件网址
     *
     * @param string $path 文件存储路径
     * @return string 文件网址
     */
    public function getFileUrl(string $path): string
    {
        return $this->getRootUrl() . $path;
    }

    /**
     * 获取指定路径下的文件列表
     *
     * @param string $dirPath 文件夹路径 以 '/' 开头，以 '/' 结尾
     * @param array $option 参数
     * @return array
     */
    abstract function getFiles(string $dirPath, array $option = []): array;

    /**
     * 文件 - 上传 用户提交的临时文件
     *
     * @param string $path 文件存储路径
     * @param string $tmpFile 上传的临时文件名或指定的文件
     * @param bool $override 是否醒盖同名文件
     * @param bool $existException 不醒盖但同名文件但存在时是否抛出异常
     * @return string 上传成功的文件的网址
     */
    abstract function uploadFile(string $path, string $tmpFile, bool $override = false, bool $existException = true): string;

    /**
     * 文件 - 重命名
     *
     * @param string $oldPath 旧文件路径 以 '/' 开头
     * @param string $newPath 新文件路径 以 '/' 开头
     * @return string 重命名成功的新文件的网址
     */
    abstract function renameFile(string $oldPath, string $newPath): string;

    /**
     * 文件 - 删除
     *
     * @param string $path 文件存储路径，以 '/' 开头
     */
    abstract function deleteFile(string $path): bool;

    /**
     * 文件是否存在
     *
     * @param string $path 文件存储路径，以 '/' 开头
     * @return bool 是否存在
     * @throws StorageException
     */
    abstract function isFileExist(string $path): bool;

    /**
     * 文件夹 - 创建
     *
     * @param string $dirPath 文件夹路径 以 '/' 开头，以 '/' 结尾
     * @return string 创建成功的文件的网址
     */
    abstract function createDir(string $dirPath): string;

    /**
     * 文件夹 - 上传文件夹
     *
     * @param string $path 文件存储路径
     * @param string $localPath 本地文件绝路路径
     * @param bool $override 是否醒盖同名文件
     * @param bool $existException 不醒盖但同名文件但存在时是否抛出异常
     * @return string 上传成功的文件的网址
     */
    public function uploadDir(string $path, string $localPath, bool $override = false, bool $existException = false): string
    {
        $srcDirSource = opendir($localPath);
        if ($srcDirSource) {
            while (false !== ($file = readdir($srcDirSource))) {
                if ($file !== '.' && $file !== '..') {
                    $tmpLocalPath = $localPath . '/' . $file;
                    $tmpPath = $path . '/' . $file;
                    if (is_dir($tmpLocalPath)) {
                        $this->uploadDir($tmpPath, $tmpLocalPath, $override, $existException);
                    } else {
                        $this->uploadFile($tmpPath, $tmpLocalPath, $override, $existException);
                    }
                }
            }
        }
        closedir($srcDirSource);
        return true;
    }

    /**
     * 文件夹 - 删除
     *
     * @param string $dirPath 文件夹路径 以 '/' 开头，以 '/' 结尾
     * @return true
     */
    abstract function deleteDir(string $dirPath): bool;

    /**
     * 文件夹 - 重命名
     *
     * @param string $oldDirPath 旧文件夹路径 以 '/' 开头，以 '/' 结尾
     * @param string $newDirPath 新文件夹路径 以 '/' 开头，以 '/' 结尾
     * @return string 重命名成功的新文件夹的网址
     */
    abstract function renameDir(string $oldDirPath, string $newDirPath): string;

    /**
     * 文件夹是否存在
     *
     * @param string $dirPath 文件夹路径 以 '/' 开头，以 '/' 结尾
     * @return true
     */
    abstract function isDirExist(string $dirPath): bool;

}

