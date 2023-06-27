<?php

namespace Be\Storage\Driver;

use Be\Be;
use Be\Storage\StorageException;
use Be\Storage\Driver;
use Be\Util\File\FileSize;
use Qcloud\Cos\Client;

/**
 * 腾讯云COS对象存储
 *
 */
class TencentCos extends Driver
{

    /**
     * 获取跟网址
     *
     * @return string 根网址
     */
    public function getRootUrl(): string
    {
        return Be::getConfig('App.System.StorageTencentCos')->rootUrl;
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

        $cosDirPath = substr($dirPath, 1);

        $configSystem = Be::getConfig('App.System.System');
        $config = Be::getConfig('App.System.StorageTencentCos');

        try {
            $rootUrl = $this->getRootUrl();
            $files = [];

            $cosClient = $this->newCosClient($config);

            $response = $cosClient->listObjects([
                'Bucket' => $config->bucket, // 存储桶名称，由BucketName-Appid 组成，可以在COS控制台查看 https://console.cloud.tencent.com/cos5/bucket
                'Delimiter' => '/', // Delimiter表示分隔符, 设置为/表示列出当前目录下的object, 设置为空表示列出所有的object
                //'EncodingType' => 'url',// 编码格式，对应请求中的 encoding-type 参数
                'Marker' => $option['nextToken'] ?? '',// 起始对象键标记
                'Prefix' => $cosDirPath, // Prefix表示列出的object的key以prefix开始
                'MaxKeys' => 1000, // 设置最大遍历出多少个对象, 一次listObjects最大支持1000
            ]);

            if (isset($response['CommonPrefixes']) && is_array($response['CommonPrefixes']) && count($response['CommonPrefixes']) > 0) {
                foreach ($response['CommonPrefixes'] as $item) {
                    $name = $item['Prefix'];
                    $name = substr($name, strlen($cosDirPath));
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

            if (isset($response['Contents']) && is_array($response['Contents']) && count($response['Contents']) > 0) {
                foreach ($response['Contents'] as $item) {
                    $name = $item['Key'];
                    $name = substr($name, strlen($cosDirPath));
                    if ($name === '') continue;

                    $type = strtolower(substr(strrchr($name, '.'), 1));

                    // 只显示图像类型时，过滤
                    if (isset($option['filterImage']) && $option['filterImage'] === 1) {
                        if (!in_array($type, $configSystem->allowUploadImageTypes)) {
                            continue;
                        }
                    }

                    $size = $item['Size'];
                    $sizeString = FileSize::int2String($size);

                    $files[] = [
                        'name' => $name,
                        'type' => $type,
                        'size' => $size,
                        'sizeString' => $sizeString,
                        'url' => $rootUrl . $dirPath . $name,
                        'createTime' => date('Y-m-d H:i:s'),
                        'updateTime' => date('Y-m-d H:i:s', strtotime($item['LastModified'])),
                    ];
                }
            }

            return $files;

        } catch (\Throwable $t) {
            throw new StorageException('Tencent COS error：' . $t->getMessage());
        }
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

        $cosPath = substr($path, 1);

        $config = Be::getConfig('App.System.StorageTencentCos');
        try {
            $cosClient = $this->newCosClient($config);

            $exist = $cosClient->doesObjectExist($config->bucket, $cosPath);
            if ($exist) {
                if ($override) {
                    $cosClient->deleteObject([
                        'Bucket' => $config->bucket,
                        'Key' => $cosPath,
                    ]);
                } else {
                    if ($existException) {
                        throw new StorageException('File ' . $path . ' already exists!');
                    } else {
                        return $config->rootUrl . $path;
                    }
                }
            }

            $cosClient->upload($config->bucket, $cosPath, fopen($tmpFile, 'rb'));
        } catch (\Throwable $t) {
            throw new StorageException('Tencent COS error：' . $t->getMessage());
        }
        return $config->rootUrl . $path;
    }

    /**
     * 文件 - 重命名
     *
     * https://help.aliyun.com/document_detail/88514.html
     *
     * @param string $oldPath 旧文件路径 以 '/' 开头
     * @param string $newPath 新文件路径 以 '/' 开头
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

        $cosOldPath = substr($oldPath, 1);
        $cosNewPath = substr($newPath, 1);

        $config = Be::getConfig('App.System.StorageTencentCos');
        try {
            $cosClient = $this->newCosClient($config);

            $exist = $cosClient->doesObjectExist($config->bucket, $cosOldPath);
            if (!$exist) {
                throw new StorageException('Original file ' . $oldPath . ' does not exist!');
            }

            $exist = $cosClient->doesObjectExist($config->bucket, $cosNewPath);
            if ($exist) {
                throw new StorageException('Destination file ' . $newPath . ' already exists!');
            }

            // 拷备文件 简单测了一下，copyObject 比 copy 更快一点，
            $cosClient->copyObject([
                'Bucket' => $config->bucket,
                'Key' => $cosNewPath,
                'CopySource' => urlencode($config->bucket . '.cos.' . $config->region . '.myqcloud.com/' . $cosOldPath),
                'MetadataDirective' => 'Replaced',
            ]);

            /*
            $cosClient->copy(
                $config->bucket,
                $cosNewPath,
                [
                    'Region' => $config->region,
                    'Bucket' => $config->bucket,
                    'Key' => $cosOldPath,
                ]
            );
            */

            // 删除旧文件
            $cosClient->deleteObject([
                'Bucket' => $config->bucket,
                'Key' => $cosOldPath,
            ]);

        } catch (\Throwable $t) {
            throw new StorageException('Tencent COS error：' . $t->getMessage());
        }

        return $config->rootUrl . $newPath;
    }

    /**
     * 删除文件
     *
     * https://help.aliyun.com/document_detail/88513.html
     *
     * @param string $path 文件存储路径，以 '/' 开头
     * @return true
     */
    public function deleteFile(string $path): bool
    {
        // 路径检查
        if (substr($path, 0, 1) !== '/' || strpos($path, './') !== false) {
            throw new StorageException('Illegal file path：' . $path . '!');
        }

        $cosPath = substr($path, 1);

        $config = Be::getConfig('App.System.StorageTencentCos');
        try {
            $cosClient = $this->newCosClient($config);
            $exist = $cosClient->doesObjectExist($config->bucket, $cosPath);
            if ($exist) {
                $cosClient->deleteObject([
                    'Bucket' => $config->bucket,
                    'Key' => $cosPath,
                ]);
            }
        } catch (\Throwable $t) {
            throw new StorageException('Tencent COS error：' . $t->getMessage());
        }

        return true;
    }

    /**
     * 文件是否存在
     *
     * @param string $path 文件存储路径，以 '/' 开头
     * @return bool 是否存在
     * @throws StorageException
     */
    public function isFileExist(string $path): bool
    {
        // 路径检查
        if (substr($path, 0, 1) !== '/' || strpos($path, './') !== false) {
            throw new StorageException('Illegal file path：' . $path . '!');
        }

        $cosPath = substr($path, 1);

        $config = Be::getConfig('App.System.StorageTencentCos');
        try {
            $cosClient = $this->newCosClient($config);
            return $cosClient->doesObjectExist($config->bucket, $cosPath);
        } catch (\Throwable $t) {
            throw new StorageException('Tencent COS error：' . $t->getMessage());
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

        $cosDirPath = substr($dirPath, 1);

        $config = Be::getConfig('App.System.StorageTencentCos');
        try {
            $cosClient = $this->newCosClient($config);

            $exist = $cosClient->doesObjectExist($config->bucket, $cosDirPath);
            if ($exist) {
                throw new StorageException('Folder ' . $dirPath . ' already exists!');
            }

            $cosClient->putObject(array(
                'Bucket' => $config->bucket,
                'Key' => $cosDirPath,
                'Body' => '',
            ));
        } catch (\Throwable $t) {
            throw new StorageException('Tencent COS error：' . $t->getMessage());
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

        $cosDirPath = substr($dirPath, 1);

        $config = Be::getConfig('App.System.StorageTencentCos');
        try {
            $cosClient = $this->newCosClient($config);
            $exist = $cosClient->doesObjectExist($config->bucket, $cosDirPath);
            if ($exist) {

                /*
                $cosClient->deleteObject([
                    'Bucket' => $config->bucket,
                    'Key' => $cosDirPath,
                ]);
                */

                $cosDirPath = substr($cosDirPath, 0, -1);

                // 循环删除所有文件
                $nextMarker = '';
                $isTruncated = true;
                while ($isTruncated) {
                    $result = $cosClient->listObjects([
                        'Bucket' => $config->bucket,
                        'Delimiter' => '',
                        'EncodingType' => 'url',
                        'Marker' => $nextMarker,
                        'Prefix' => $cosDirPath,
                        'MaxKeys' => 1000
                    ]);
                    $isTruncated = $result['IsTruncated'];
                    $nextMarker = $result['NextMarker'];
                    foreach ($result['Contents'] as $content) {
                        $cosClient->deleteObject(array(
                            'Bucket' => $config->bucket,
                            'Key' => $content['Key'],
                        ));
                    }
                }
            }
        } catch (\Throwable $t) {
            throw new StorageException('Tencent COS error：' . $t->getMessage());
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

        $cosOldDirPath = substr($oldDirPath, 1);
        $cosNewDirPath = substr($newDirPath, 1);

        $config = Be::getConfig('App.System.StorageTencentCos');
        try {
            $cosClient = $this->newCosClient($config);

            $exist = $cosClient->doesObjectExist($config->bucket, $cosOldDirPath);
            if (!$exist) {
                throw new StorageException('Original folder ' . $oldDirPath . ' does not exist!');
            }

            $exist = $cosClient->doesObjectExist($config->bucket, $cosNewDirPath);
            if ($exist) {
                throw new StorageException('Destination folder ' . $newDirPath . ' already exists!');
            }

            // 创建新的文件夹
            $cosClient->putObject(array(
                'Bucket' => $config->bucket,
                'Key' => $cosNewDirPath,
                'Body' => '',
            ));

            $cosDirPath = substr($cosOldDirPath, 0, -1);

            // 循环删除所有文件
            $nextMarker = '';
            $isTruncated = true;
            while ($isTruncated) {
                $result = $cosClient->listObjects([
                    'Bucket' => $config->bucket,
                    'Delimiter' => '',
                    'EncodingType' => 'url',
                    'Marker' => $nextMarker,
                    'Prefix' => $cosDirPath,
                    'MaxKeys' => 1000
                ]);
                $isTruncated = $result['IsTruncated'];
                $nextMarker = $result['NextMarker'];
                foreach ($result['Contents'] as $content) {

                    $cosOldPath = $content['Key'];
                    $cosNewPath = $cosNewDirPath . substr($cosOldPath, strlen($cosOldDirPath));

                    // 拷备文件
                    $cosClient->copyObject([
                        'Bucket' => $config->bucket,
                        'Key' => $cosNewPath,
                        'CopySource' => urlencode($config->bucket . '.cos.' . $config->region . '.myqcloud.com/' . $cosOldPath),
                        'MetadataDirective' => 'Replaced',
                    ]);
                }
            }

            // 循环删除所有文件
            $nextMarker = '';
            $isTruncated = true;
            while ($isTruncated) {
                $result = $cosClient->listObjects([
                    'Bucket' => $config->bucket,
                    'Delimiter' => '',
                    'EncodingType' => 'url',
                    'Marker' => $nextMarker,
                    'Prefix' => $cosDirPath,
                    'MaxKeys' => 1000
                ]);
                $isTruncated = $result['IsTruncated'];
                $nextMarker = $result['NextMarker'];
                foreach ($result['Contents'] as $content) {
                    $cosClient->deleteObject(array(
                        'Bucket' => $config->bucket,
                        'Key' => $content['Key'],
                    ));
                }
            }

        } catch (\Throwable $t) {
            throw new StorageException('Tencent COS error：' . $t->getMessage());
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

        $cosDirPath = substr($dirPath, 1);

        $config = Be::getConfig('App.System.StorageTencentCos');
        try {
            $cosClient = $this->newCosClient($config);
            return $cosClient->doesObjectExist($config->bucket, $cosDirPath);
        } catch (\Throwable $t) {
            throw new StorageException('Tencent COS error：' . $t->getMessage());
        }
    }

    /**
     * 生成一个COS客户端
     *
     * @return Client
     */
    public function newCosClient($config = null): Client
    {
        if ($config === null) {
            $config = Be::getConfig('App.System.StorageTencentCos');
        }

        $options = [
            'region' => $config->region,
            'schema' => $config->schema,
            'credentials' => [
                'secretId' => $config->secretId,
                'secretKey' => $config->secretKey,
            ],
            'timeout' => 10,
            'connect_timeout' => 5,
        ];

        if ($config->token) {
            $options['credentials']['token'] = $config->token;
        }

        return new Client($options);
    }

}
