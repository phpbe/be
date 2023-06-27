<?php

namespace Be\Storage\Driver;

use Be\Be;
use Be\Storage\StorageException;
use Be\Storage\Driver;
use Be\Util\File\FileSize;
use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

/**
 * 亚马逊AWS S3对象存储
 *
 */
class AwsS3 extends Driver
{

    /**
     * 获取跟网址
     *
     * @return string 根网址
     */
    public function getRootUrl(): string
    {
        return Be::getConfig('App.System.StorageAwsS3')->rootUrl;
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

        $s3DirPath = substr($dirPath, 1);
        try {
            $configSystem = Be::getConfig('App.System.System');
            $rootUrl = $this->getRootUrl();
            $files = [];

            $config = Be::getConfig('App.System.StorageAwsS3');
            $credentials = new Credentials($config->key, $config->secret);

            $s3Client = new S3Client([
                'credentials' => $credentials,
                'region' => $config->region,
                'version' => 'latest',
                'http' => ['verify' => false],
            ]);

            $params  = [
                'Bucket' => $config->bucket,
                'Prefix' => $s3DirPath,  // 前缀，即路径
                'Delimiter' => '/',
                'MaxKeys' => 1000, // 超过 1000 个分页
            ];

            foreach ($option as $key => $val) {
                if (in_array($key, [
                    'Delimiter',
                    'MaxKeys',
                    'ContinuationToken',
                ]) ) {
                    $params[$key] = $val;
                }
            }

            $contents = $s3Client->listObjectsV2($params);

            //print_r($contents);

            if (isset($contents['CommonPrefixes']) && is_array($contents['CommonPrefixes']) && count($contents['CommonPrefixes']) > 0) {
                foreach ($contents['CommonPrefixes'] as $content) {

                    $name = $content['Prefix'];
                    $name = substr($name, strlen($s3DirPath));
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

            foreach ($contents['Contents'] as $content) {
                $name = $content['Key'];
                $name = substr($name, strlen($s3DirPath));
                if ($name === '') continue;

                $size = (int) $content['Size'];
                $updateTime = $content['LastModified']->getTimestamp();
                $type = strtolower(substr(strrchr($name, '.'), 1));

                // 只显示图像类型时，过滤
                if (isset($option['filterImage']) && $option['filterImage'] === 1) {
                    if (!in_array($type, $configSystem->allowUploadImageTypes)) {
                        continue;
                    }
                }

                $files[] = [
                    'name' => $name,
                    'type' => $type,
                    'size' => $size,
                    'sizeString' => FileSize::int2String($size),
                    'url' => $rootUrl . $dirPath . $name,
                    'createTime' => date('Y-m-d H:i:s', $updateTime),
                    'updateTime' => date('Y-m-d H:i:s', $updateTime),
                ];
            }

            return $files;

        } catch (AwsException $e) {
            throw new StorageException('AWS S3 error：' . $e->getMessage());
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

        $s3Path = substr($path, 1);

        $config = Be::getConfig('App.System.StorageAwsS3');
        try {

            $credentials = new Credentials($config->key, $config->secret);

            $s3Client = new S3Client([
                'credentials' => $credentials,
                'region' => $config->region,
                'version' => 'latest',
                'http' => ['verify' => false],
            ]);

            $exist = $s3Client->doesObjectExist($config->bucket, $s3Path);
            if ($exist) {
                if ($override) {
                    $s3Client->deleteObject([
                        'Bucket' => $config->bucket,
                        'Key' => $s3Path
                    ]);
                } else {
                    if ($existException) {
                        throw new StorageException('File ' . $path . ' already exists!');
                    } else {
                        return $config->rootUrl . $path;
                    }
                }
            }

            $s3Client->putObject([
                'Bucket' => $config->bucket,
                'Key' => $s3Path,
                'SourceFile' => $tmpFile,
            ]);
        } catch (AwsException $e) {
            throw new StorageException('AWS S3 error：' . $e->getMessage());
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

        $s3OldPath = substr($oldPath, 1);
        $s3NewPath = substr($newPath, 1);

        $config = Be::getConfig('App.System.StorageAwsS3');
        try {
            $credentials = new Credentials($config->key, $config->secret);

            $s3Client = new S3Client([
                'credentials' => $credentials,
                'region' => $config->region,
                'version' => 'latest',
                'http' => ['verify' => false],
            ]);

            $exist = $s3Client->doesObjectExist($config->bucket, $s3OldPath);
            if (!$exist) {
                throw new StorageException('Original file ' . $oldPath . ' does not exist!');
            }

            $exist = $s3Client->doesObjectExist($config->bucket, $s3NewPath);
            if ($exist) {
                throw new StorageException('Destination file ' . $newPath . ' already exists!');
            }

            // 拷备文件
            $s3Client->copyObject([
                'Bucket' => $config->bucket,
                'CopySource' => $config->bucket . '/' . $s3OldPath,
                'Key' => $s3NewPath
            ]);

            // 删除旧文件
            $s3Client->deleteObject([
                'Bucket' => $config->bucket,
                'Key' => $s3OldPath
            ]);
        } catch (AwsException $e) {
            throw new StorageException('AWS S3 error：' . $e->getMessage());
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

        $s3Path = substr($path, 1);

        $config = Be::getConfig('App.System.StorageAwsS3');
        try {
            $credentials = new Credentials($config->key, $config->secret);

            $s3Client = new S3Client([
                'credentials' => $credentials,
                'region' => $config->region,
                'version' => 'latest',
                'http' => ['verify' => false],
            ]);

            $exist = $s3Client->doesObjectExist($config->bucket, $s3Path);
            if ($exist) {
                $s3Client->deleteObject([
                    'Bucket' => $config->bucket,
                    'Key' => $s3Path
                ]);
            }
        } catch (AwsException $e) {
            throw new StorageException('AWS S3 error：' . $e->getMessage());
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

        $s3Path = substr($path, 1);

        $config = Be::getConfig('App.System.StorageAwsS3');
        try {
            $credentials = new Credentials($config->key, $config->secret);

            $s3Client = new S3Client([
                'credentials' => $credentials,
                'region' => $config->region,
                'version' => 'latest',
                'http' => ['verify' => false],
            ]);

            return $s3Client->doesObjectExist($config->bucket, $s3Path);
        } catch (AwsException $e) {
            throw new StorageException('AWS S3 error：' . $e->getMessage());
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

        $s3DirPath = substr($dirPath, 1);

        $config = Be::getConfig('App.System.StorageAwsS3');
        try {
            $credentials = new Credentials($config->key, $config->secret);

            $s3Client = new S3Client([
                'credentials' => $credentials,
                'region' => $config->region,
                'version' => 'latest',
                'http' => ['verify' => false],
            ]);

            $exist = $s3Client->doesObjectExist($config->bucket, $s3DirPath);
            if ($exist) {
                throw new StorageException('Folder ' . $dirPath . ' already exists!');
            }

            $s3Client->putObject([
                'Bucket' => $config->bucket,
                'Key' => $s3DirPath,
                'Body' => '',
            ]);
        } catch (AwsException $e) {
            throw new StorageException('AWS S3 error：' . $e->getMessage());
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

        $s3DirPath = substr($dirPath, 1);

        $config = Be::getConfig('App.System.StorageAwsS3');
        try {
            $credentials = new Credentials($config->key, $config->secret);

            $s3Client = new S3Client([
                'credentials' => $credentials,
                'region' => $config->region,
                'version' => 'latest',
                'http' => ['verify' => false],
            ]);

            $exist = $s3Client->doesObjectExist($config->bucket, $s3DirPath);

            if ($exist) {

                $delete = \Aws\S3\BatchDelete::fromListObjects($s3Client, [
                    'Bucket' => $config->bucket,
                    'Prefix' => $s3DirPath
                ]);

                $delete->delete();

                // 异步;
                //$promise = $delete->promise();
            }
        } catch (AwsException $e) {
            throw new StorageException('AWS S3 error：' . $e->getMessage());
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

        $s3OldDirPath = substr($oldDirPath, 1);
        $s3NewDirPath = substr($newDirPath, 1);

        $config = Be::getConfig('App.System.StorageAwsS3');
        try {
            $credentials = new Credentials($config->key, $config->secret);

            $s3Client = new S3Client([
                'credentials' => $credentials,
                'region' => $config->region,
                'version' => 'latest',
                'http' => ['verify' => false],
            ]);

            $exist = $s3Client->doesObjectExist($config->bucket, $s3OldDirPath);
            if (!$exist) {
                throw new StorageException('Original folder ' . $oldDirPath . ' does not exist!');
            }

            $exist = $s3Client->doesObjectExist($config->bucket, $s3NewDirPath);
            if ($exist) {
                throw new StorageException('Destination folder ' . $newDirPath . ' already exists!');
            }

            // 创建新的文件夹
            $s3Client->putObject([
                'Bucket' => $config->bucket,
                'Key' => $s3NewDirPath,
                'Body' => '',
            ]);

            // 循环删除旧文件
            $nextContinuationToken = null;
            while (true) {

                $params  = [
                    'Bucket' => $config->bucket,
                    'Prefix' => $s3OldDirPath,
                    'Delimiter' => '/',
                    'MaxKeys' => 1000,
                ];

                if ($nextContinuationToken !== null) {
                    $params['ContinuationToken'] = $nextContinuationToken;
                }

                $contents = $s3Client->listObjectsV2($params);

                foreach ($contents['Contents'] as $content) {
                    if ($content['Key'] === $s3OldDirPath) continue;

                    $s3NewPath = $s3NewDirPath . substr($content['Key'], strlen($s3OldDirPath));

                    // 拷备文件
                    $s3Client->copyObject([
                        'Bucket' => $config->bucket,
                        'CopySource' => $config->bucket . '/' .$content['Key'],
                        'Key' => $s3NewPath
                    ]);
                }

                if (isset($contents['IsTruncated']) &&
                    $contents['IsTruncated'] &&
                    isset($contents['NextContinuationToken']) &&
                    $contents['NextContinuationToken']) {
                    $nextContinuationToken = $contents['NextContinuationToken'];
                } else {
                    break;
                }
            }

            //$s3Client->deleteMatchingObjects($config->bucket, $s3DirPath);

            // 删除旧文件夹
            $delete = \Aws\S3\BatchDelete::fromListObjects($s3Client, [
                'Bucket' => $config->bucket,
                'Prefix' => $s3OldDirPath
            ]);

            $delete->delete();

        } catch (AwsException $e) {
            throw new StorageException('AWS S3 error：' . $e->getMessage());
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

        $s3DirPath = substr($dirPath, 1);

        $config = Be::getConfig('App.System.StorageAwsS3');
        try {
            $credentials = new Credentials($config->key, $config->secret);

            $s3Client = new S3Client([
                'credentials' => $credentials,
                'region' => $config->region,
                'version' => 'latest',
                'http' => ['verify' => false],
            ]);

            return $s3Client->doesObjectExist($config->bucket, $s3DirPath);
        } catch (AwsException $e) {
            throw new StorageException('AWS S3 error：' . $e->getMessage());
        }
    }

}
