<?php

namespace Be\Storage\Driver;

use Be\Be;
use Be\Storage\StorageException;
use Be\Storage\Driver;
use Be\Util\File\Dir;
use Be\Util\File\FileSize;

/**
 * 本地磁盘
 *
 */
class LocalDisk extends Driver
{

    /**
     * 获取跟网址
     *
     * @return string 跟网址
     */
    public function getRootUrl(): string
    {
        $config = Be::getConfig('App.System.StorageLocalDisk');
        if ($config->rootUrl) {
            return $config->rootUrl;
        }

        return Be::getRequest()->getRootUrl();
    }

    /**
     * 获取指定路径下的文件列表
     *
     * @param string $dirPath 文件夹路径 以 '/' 开头，以 '/' 结尾
     * @param array $option 参数
     * @return array
     */
    public function getFiles(string $dirPath, array $option = []): array
    {
        // 文件夹路径检查
        if (substr($dirPath, 0, 1) !== '/' || substr($dirPath, -1, 1) !== '/' || strpos($dirPath, './') !== false) {
            throw new StorageException('Illegal folder path!');
        }

        $uploadPath = Be::getRuntime()->getRootPath() . '/www';
        $path = $uploadPath . $dirPath;
        if (!is_dir($path)) {
            throw new StorageException('Folder ' . $dirPath . ' does not exists！');
        }

        $configSystem = Be::getConfig('App.System.System');
        $rootUrl = $this->getRootUrl();

        $dirs = [];
        $files = [];

        // 分析目录
        $items = scandir($path);
        foreach ($items as $x => $name) {
            if ($name === "." || $name === "..") continue;

            $itemPath = $path . $name;

            if (is_dir($itemPath)) {
                $dirs[] = [
                    'name' => $name,
                    'type' => 'dir',
                    'size' => 0,
                    'sizeString' => '0 B',
                    'url' => $rootUrl . $dirPath . $name,
                    'createTime' => date('Y-m-d H:i:s', filectime($itemPath)),
                    'updateTime' => date('Y-m-d H:i:s', filemtime($itemPath)),
                ];
            } else {

                $type = strtolower(substr(strrchr($name, '.'), 1));

                // 只显示图像类型时，过滤
                if (isset($option['filterImage']) && $option['filterImage'] === 1) {
                    if (!in_array($type, $configSystem->allowUploadImageTypes)) {
                        continue;
                    }
                }

                $size = filesize($itemPath);
                $sizeString = FileSize::int2String($size);

                $files[$x] = [
                    'name' => $name,
                    'type' => $type,
                    'size' => $size,
                    'sizeString' => $sizeString,
                    'url' => $rootUrl . $dirPath . $name,
                    'createTime' => date('Y-m-d H:i:s', filectime($itemPath)),
                    'updateTime' => date('Y-m-d H:i:s', filemtime($itemPath)),
                ];
            }
        }

        return array_merge($dirs, $files);
    }

    /**
     * 文件 - 上传 用户提交的临时文件
     *
     * @param string $path 文件存储路径
     * @param string $tmpFile 上传的临时文件名或指定的文件
     * @param bool $override 是否醒盖同名文件
     * @param bool $existException 不醒盖但同名文件但存在时是否抛出异常
     * @return string 上传成功的文件的网址
     */
    public function uploadFile(string $path, string $tmpFile, bool $override = false, bool $existException = true): string
    {
        // 路径检查
        if (substr($path, 0, 1) !== '/' || strpos($path, './') !== false) {
            throw new StorageException('Illegal file path：' . $path . '!');
        }

        $uploadPath = Be::getRuntime()->getRootPath() . '/www';

        $newFilePath = $uploadPath . $path;
        $dir = dirname($newFilePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        if (file_exists($newFilePath)) {
            if ($override) {
                unlink($newFilePath);
            } else {
                if ($existException) {
                    throw new StorageException('File ' . $path . ' already exists!');
                } else {
                    return $this->getRootUrl() . $path;
                }
            }
        }

        if (is_uploaded_file($tmpFile)) {
            if (!move_uploaded_file($tmpFile, $newFilePath)) {
                throw new StorageException('Upload file error!');
            }
        } else {
            if (file_exists($tmpFile)) {
                copy($tmpFile, $newFilePath);
                //unlink($tmpFile);
            } else {
                throw new StorageException('Upload file error!!!');
            }
        }

        return $this->getRootUrl() . $path;
    }

    /**
     * 文件 - 上传任意文件
     *
     * @param string $path 文件存储路径
     * @param string $localPath 本地文件绝路路径
     * @param bool $override 是否醒盖同名文件
     * @param bool $existException 不醒盖但同名文件但存在时是否抛出异常
     * @return string 上传成功的文件的网址
     */
    public function putFile(string $path, string $localPath, bool $override = false, bool $existException = true): string
    {
        // 路径检查
        if (substr($path, 0, 1) !== '/' || strpos($path, './') !== false) {
            throw new StorageException('Illegal file path：' . $path . '!');
        }

        $uploadPath = Be::getRuntime()->getRootPath() . '/www';

        $newFilePath = $uploadPath . $path;
        $dir = dirname($newFilePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        if (file_exists($newFilePath)) {
            if ($override) {
                unlink($newFilePath);
            } else {
                if ($existException) {
                    throw new StorageException('File ' . $path . ' already exists!');
                } else {
                    return $this->getRootUrl() . $path;
                }
            }
        }

        if (file_exists($localPath)) {
            copy($localPath, $newFilePath);
            unlink($localPath);
        } else {
            throw new StorageException('Put file error!!!');
        }

        return $this->getRootUrl() . $path;
    }

    /**
     * 文件 - 重命名
     *
     * @param string $oldPath 旧文件夹路径 以 '/' 开头
     * @param string string $newPath 新文件夹路径 以 '/' 开头
     * @return string 重命名成功的新文件的网址
     */
    public function renameFile(string $oldPath, string $newPath): string
    {
        // 文件夹路径检查
        if (substr($oldPath, 0, 1) !== '/' || strpos($oldPath, './') !== false) {
            throw new StorageException('Illegal source file path：' . $oldPath . '!');
        }

        // 文件夹路径检查
        if (substr($newPath, 0, 1) !== '/' || strpos($newPath, './') !== false) {
            throw new StorageException('Illegal destination file path：' . $newPath . '!');
        }

        $type = strtolower(substr(strrchr($newPath, '.'), 1));
        $configSystem = Be::getConfig('App.System.System');
        if (!in_array($type, $configSystem->allowUploadFileTypes)) {
            throw new StorageException('Forbidden file type：' . $type . '!');
        }

        $uploadPath = Be::getRuntime()->getRootPath() . '/www';
        $srcPath = $uploadPath . $oldPath;
        if (!file_exists($srcPath)) {
            throw new StorageException('Original file ' . $oldPath . ' does not exist!');
        }

        $dstPath = $uploadPath . $newPath;
        if (file_exists($dstPath)) {
            throw new StorageException('Destination file ' . $newPath . ' already exists!');
        }

        if (!rename($srcPath, $dstPath)) {
            throw new StorageException('Rename file fail!');
        }

        return $this->getRootUrl() . $newPath;
    }

    /**
     * 删除文件
     *
     * @param string $path 文件存储路径
     * @return true
     */
    public function deleteFile(string $path): bool
    {
        // 路径检查
        if (substr($path, 0, 1) !== '/' || strpos($path, './') !== false) {
            throw new StorageException('Illegal file path：' . $path . '!');
        }

        $filePath = Be::getRuntime()->getRootPath() . '/www' . $path;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        return true;
    }

    /**
     * 文件是否存在
     *
     * @param string $path 文件存储路径
     * @return bool 是否存在
     * @throws StorageException
     */
    public function isFileExist(string $path): bool
    {
        // 路径检查
        if (substr($path, 0, 1) !== '/' || strpos($path, './') !== false) {
            throw new StorageException('Illegal file path：' . $path . '!');
        }

        $filePath = Be::getRuntime()->getRootPath() . '/www' . $path;
        return file_exists($filePath);
    }

    /**
     * 文件夹 - 创建
     *
     * @param string $dirPath 文件夹路径 以 '/' 开头，以 '/' 结尾
     * @return string 创建成功的文件的网址
     */
    public function createDir(string $dirPath): string
    {
        // 文件夹路径检查
        if (substr($dirPath, 0, 1) !== '/' || substr($dirPath, -1, 1) !== '/' || strpos($dirPath, './') !== false) {
            throw new StorageException('Illegal folder path!');
        }

        $uploadPath = Be::getRuntime()->getRootPath() . '/www';
        $path = $uploadPath . $dirPath;
        if (is_dir($path)) {
            throw new StorageException('Folder ' . $dirPath . ' already exists!');
        }

        mkdir($path, 0777, true);
        chmod($path, 0777);

        return $this->getRootUrl() . $dirPath;
    }

    /**
     * 文件夹 - 删除
     *
     * @param string $dirPath 文件夹路径 以 '/' 开头，以 '/' 结尾
     * @return true
     */
    public function deleteDir(string $dirPath): bool
    {
        // 文件夹路径检查
        if (substr($dirPath, 0, 1) !== '/' || substr($dirPath, -1, 1) !== '/' || strpos($dirPath, './') !== false) {
            throw new StorageException('Illegal folder path!');
        }

        $uploadPath = Be::getRuntime()->getRootPath() . '/www';
        $path = $uploadPath . $dirPath;
        if (is_dir($path)) {
            Dir::rm($path);
        }

        return true;
    }

    /**
     * 文件夹 - 重命名
     *
     * @param string $oldDirPath 旧文件夹路径 以 '/' 开头，以 '/' 结尾
     * @param string $newDirPath 新文件夹路径 以 '/' 开头，以 '/' 结尾
     * @return string 重命名成功的新文件夹的网址
     */
    public function renameDir(string $oldDirPath, string $newDirPath): string
    {
        // 文件夹路径检查
        if (substr($oldDirPath, 0, 1) !== '/' || substr($oldDirPath, -1, 1) !== '/' || strpos($oldDirPath, './') !== false) {
            throw new StorageException('Illegal folder path!');
        }

        // 文件夹路径检查
        if (substr($newDirPath, 0, 1) !== '/' || substr($newDirPath, -1, 1) !== '/' || strpos($newDirPath, './') !== false) {
            throw new StorageException('Illegal destination folder path!');
        }

        $uploadPath = Be::getRuntime()->getRootPath() . '/www';
        $oldPath = $uploadPath . $oldDirPath;
        if (!file_exists($oldPath)) {
            throw new StorageException('Original folder ' . $oldDirPath . ' does not exist!');
        }

        $newPath = $uploadPath . $newDirPath;
        if (file_exists($newPath)) {
            throw new StorageException('Destination folder ' . $oldDirPath . ' already exists!');
        }

        if (!rename($oldPath, $newPath)) {
            throw new StorageException('Rename folder fail!');
        }

        return $this->getRootUrl() . $newDirPath;
    }

    /**
     * 文件夹是否存在
     *
     * @param string $dirPath 文件夹路径 以 '/' 开头，以 '/' 结尾
     * @return true
     * @throws StorageException
     */
    public function isDirExist(string $dirPath): bool
    {
        // 路径检查
        if (substr($dirPath, 0, 1) !== '/' || strpos($dirPath, './') !== false) {
            throw new StorageException('Illegal folder path：' . $dirPath . '!');
        }

        $filePath = Be::getRuntime()->getRootPath() . '/www' . $dirPath;
        return is_dir($filePath);
    }

}
