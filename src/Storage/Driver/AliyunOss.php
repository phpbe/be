<?php

namespace Be\Storage\Driver;

use Be\Be;
use Be\Storage\StorageException;
use Be\Storage\Driver;
use Be\Util\FileSystem\FileSize;
use OSS\OssClient;
use OSS\Core\OssException;

/**
 * 阿里云OSS对象存储
 *
 */
class AliyunOss extends Driver
{

    /**
     * 获取跟网址
     *
     * @return string 跟网址
     */
    public function getRootUrl(): string
    {
        return Be::getConfig('App.System.StorageAliyunOss')->rootUrl;
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

        $ossDirPath = substr($dirPath, 1);
        try {
            $configSystem = Be::getConfig('App.System.System');
            $rootUrl = $this->getRootUrl();
            $files = [];

            $config = Be::getConfig('App.System.StorageAliyunOss');
            $endpoint = $config->internal ? $config->endpointInternal : $config->endpoint;
            $ossClient = new OssClient($config->accessKeyId, $config->accessKeySecret, $endpoint);
            $ossClient->setConnectTimeout(30);

            // 填写对文件分组的字符，例如/。
            $delimiter = '/';
            $ossOption = [
                'delimiter' => $delimiter,
                'prefix' => $ossDirPath,  // 前缀，即路径
                'max-keys' => 1000, // 超过 1000 个分页
                'marker' => $option['nextPage'] ?? '', // 分页
            ];

            $listObjects = $ossClient->listObjects($config->bucket, $ossOption);

            $prefixList = $listObjects->getPrefixList(); // 目录列表。当匹配prefix的目录下无子目录或者设置delimiter为空时，目录列表不显示。
            if (!empty($prefixList)) {
                foreach ($prefixList as $prefixInfo) {
                    $name = $prefixInfo->getPrefix();
                    $name = substr($name, strlen($ossDirPath));
                    if ($name === '') continue;

                    $name = substr($name, 0, -1);
                    $files[] = [
                        'name' => $name,
                        'type' => 'dir',
                        'size' => 0,
                        'sizeString' => '0 B',
                        'url' => $rootUrl . '/' . $name,
                        'createTime' => '',
                        'updateTime' => '',
                    ];
                }
            }

            $objectList = $listObjects->getObjectList(); // 文件列表。
            if (!empty($objectList)) {
                foreach ($objectList as $objectInfo) {
                    $name = $objectInfo->getKey();
                    $name = substr($name, strlen($ossDirPath));
                    if ($name === '') continue;

                    $type = strtolower(substr(strrchr($name, '.'), 1));

                    // 只显示图像类型时，过滤
                    if (isset($option['filterImage']) && $option['filterImage'] === 1) {
                        if (!in_array($type, $configSystem->allowUploadImageTypes)) {
                            continue;
                        }
                    }

                    $size = $objectInfo->getSize();
                    $sizeString = FileSize::int2String($size);

                    $files[] = [
                        'name' => $name,
                        'type' => $type,
                        'size' => $size,
                        'sizeString' => $sizeString,
                        'url' => $rootUrl . $dirPath . $name,
                        'createTime' => date('Y-m-d H:i:s'),
                        'updateTime' => date('Y-m-d H:i:s', strtotime($objectInfo->getLastModified())),
                    ];
                }
            }

            return $files;

        } catch (OssException $e) {
            throw new StorageException('Aliyun OSS error：' . $e->getMessage());
        }
    }

    /**
     * 上传文件
     *
     * @param string $path 文件存储路径
     * @param string $tmpFile 上传的临时文件名
     * @return string 上传成功的文件的网址
     * @throws StorageException
     */
    public function uploadFile(string $path, string $tmpFile): string
    {
        // 路径检查
        if (substr($path, 0, 1) !== '/' || strpos($path, './') !== false) {
            throw new StorageException('Illegal file path：' . $path . '!');
        }

        $ossPath = substr($path, 1);

        $config = Be::getConfig('App.System.StorageAliyunOss');
        try {
            $endpoint = $config->internal ? $config->endpointInternal : $config->endpoint;
            $ossClient = new OssClient($config->accessKeyId, $config->accessKeySecret, $endpoint);
            $ossClient->setConnectTimeout(30);
            $exist = $ossClient->doesObjectExist($config->bucket, $ossPath);
            if ($exist) {
                throw new StorageException('File ' . $path . ' already exists!');
            }
            $ossClient->uploadFile($config->bucket, $ossPath, $tmpFile);
        } catch (OssException $e) {
            throw new StorageException('Aliyun OSS error：' . $e->getMessage());
        }
        return $config->rootUrl . $path;
    }

    /**
     * 文件 - 重命名
     *
     * https://help.aliyun.com/document_detail/88514.html
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
        $config = Be::getConfig('App.System.System');
        if (!in_array($type, $config->allowUploadFileTypes)) {
            throw new StorageException('Forbidden destination file type：' . $type . '!');
        }

        $ossOldPath = substr($oldPath, 1);
        $ossNewPath = substr($newPath, 1);

        $config = Be::getConfig('App.System.StorageAliyunOss');
        try {
            $endpoint = $config->internal ? $config->endpointInternal : $config->endpoint;
            $ossClient = new OssClient($config->accessKeyId, $config->accessKeySecret, $endpoint);
            $ossClient->setConnectTimeout(30);
            $exist = $ossClient->doesObjectExist($config->bucket, $ossOldPath);
            if (!$exist) {
                throw new StorageException('Original file ' . $oldPath . ' does not exist!');
            }

            $exist = $ossClient->doesObjectExist($config->bucket, $ossNewPath);
            if ($exist) {
                throw new StorageException('Destination file ' . $newPath . ' already exists!');
            }

            // 拷备文件
            $ossClient->copyObject($config->bucket, $ossOldPath, $config->bucket, $ossNewPath);

            // 删除旧文件
            $ossClient->deleteObject($config->bucket, $ossOldPath);
        } catch (OssException $e) {
            throw new StorageException('Aliyun OSS error：' . $e->getMessage());
        }

        return $config->rootUrl . $newPath;
    }

    /**
     * 删除文件
     *
     * https://help.aliyun.com/document_detail/88513.html
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

        $ossPath = substr($path, 1);

        $config = Be::getConfig('App.System.StorageAliyunOss');
        try {
            $endpoint = $config->internal ? $config->endpointInternal : $config->endpoint;
            $ossClient = new OssClient($config->accessKeyId, $config->accessKeySecret, $endpoint);
            $ossClient->setConnectTimeout(30);
            $exist = $ossClient->doesObjectExist($config->bucket, $ossPath);
            if (!$exist) {
                throw new StorageException('File ' . $path . ' does not exist!');
            }

            $ossClient->deleteObject($config->bucket, $ossPath);
        } catch (OssException $e) {
            throw new StorageException('Aliyun OSS error：' . $e->getMessage());
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

        $ossPath = substr($path, 1);

        $config = Be::getConfig('App.System.StorageAliyunOss');
        try {
            $endpoint = $config->internal ? $config->endpointInternal : $config->endpoint;
            $ossClient = new OssClient($config->accessKeyId, $config->accessKeySecret, $endpoint);
            $ossClient->setConnectTimeout(30);
            return $ossClient->doesObjectExist($config->bucket, $ossPath);
        } catch (OssException $e) {
            throw new StorageException('Aliyun OSS error：' . $e->getMessage());
        }
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

        $ossDirPath = substr($dirPath, 1);

        $config = Be::getConfig('App.System.StorageAliyunOss');
        try {
            $endpoint = $config->internal ? $config->endpointInternal : $config->endpoint;
            $ossClient = new OssClient($config->accessKeyId, $config->accessKeySecret, $endpoint);
            $ossClient->setConnectTimeout(30);
            $exist = $ossClient->doesObjectExist($config->bucket, $ossDirPath);
            if ($exist) {
                throw new StorageException('Folder ' . $dirPath . ' already exists!');
            }

            $ossClient->putObject($config->bucket, $ossDirPath, '');
        } catch (OssException $e) {
            throw new StorageException('Aliyun OSS error：' . $e->getMessage());
        }
        return $config->rootUrl . $dirPath;
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

        $ossDirPath = substr($dirPath, 1);

        $config = Be::getConfig('App.System.StorageAliyunOss');
        try {
            $endpoint = $config->internal ? $config->endpointInternal : $config->endpoint;
            $ossClient = new OssClient($config->accessKeyId, $config->accessKeySecret, $endpoint);
            $ossClient->setConnectTimeout(30);
            $exist = $ossClient->doesObjectExist($config->bucket, $ossDirPath);
            if (!$exist) {
                throw new StorageException('Folder ' . $dirPath . ' does not exist!');
            }

            $ossClient->deleteObject($config->bucket, $ossDirPath);
        } catch (OssException $e) {
            throw new StorageException('Aliyun OSS error：' . $e->getMessage());
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

        $ossOldDirPath = substr($oldDirPath, 1);
        $ossNewDirPath = substr($newDirPath, 1);

        $config = Be::getConfig('App.System.StorageAliyunOss');
        try {
            $endpoint = $config->internal ? $config->endpointInternal : $config->endpoint;
            $ossClient = new OssClient($config->accessKeyId, $config->accessKeySecret, $endpoint);
            $ossClient->setConnectTimeout(30);
            $exist = $ossClient->doesObjectExist($config->bucket, $ossOldDirPath);
            if (!$exist) {
                throw new StorageException('Original folder ' . $oldDirPath . ' does not exist!');
            }

            $exist = $ossClient->doesObjectExist($config->bucket, $ossNewDirPath);
            if ($exist) {
                throw new StorageException('Destination folder ' . $newDirPath . ' already exists!');
            }

            // 拷备文件
            $ossClient->copyObject($config->bucket, $ossOldDirPath, $config->bucket, $ossNewDirPath);

            // 删除旧文件
            $ossClient->deleteObject($config->bucket, $ossOldDirPath);

        } catch (OssException $e) {
            throw new StorageException('Aliyun OSS error：' . $e->getMessage());
        }

        return $config->rootUrl . $newDirPath;
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

        $ossDirPath = substr($dirPath, 1);

        $config = Be::getConfig('App.System.StorageAliyunOss');
        try {
            $endpoint = $config->internal ? $config->endpointInternal : $config->endpoint;
            $ossClient = new OssClient($config->accessKeyId, $config->accessKeySecret, $endpoint);
            $ossClient->setConnectTimeout(30);
            return $ossClient->doesObjectExist($config->bucket, $ossDirPath);
        } catch (OssException $e) {
            throw new StorageException('Aliyun OSS error：' . $e->getMessage());
        }
    }

}
