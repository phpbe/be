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
     * @return string 跟网址
     */
    abstract function getRootUrl(): string;

    /**
     * 获取文件网址
     *
     * @param string $path 文件存储路径
     * @return string 文件网址
     */
    public function getFileUrl(string $path): string {
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
     * 文件 - 上传
     *
     * @param string $path 文件存储路径
     * @param string $tmpFile 上传的临时文件名
     * @return string 上传成功的文件的网址
     */
    abstract function uploadFile(string $path, string $tmpFile): string;

    /**
     * 文件 - 重命名
     *
     * @param string $oldPath 旧文件夹路径 以 '/' 开头
     * @param string string $newPath 新文件夹路径 以 '/' 开头
     * @return string 重命名成功的新文件的网址
     */
    abstract function renameFile(string $oldPath, string $newPath): string;

    /**
     * 文件 - 删除
     *
     * @param string $path 文件存储路径
     */
    abstract function deleteFile(string $path): bool;

    /**
     * 文件是否存在
     *
     * @param string $path 文件存储路径
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

