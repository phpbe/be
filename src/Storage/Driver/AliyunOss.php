<?php

namespace Be\Storage\Driver;

use Be\Be;
use Be\Storage\StorageException;
use Be\Storage\Driver;
use OSS\OssClient;
use OSS\Core\OssException;

class AliyunOss extends Driver
{


    /**
     * 获取跟网址
     *
     * @return string 跟网址
     */
    public function getRootUrl()
    {
        return Be::getConfig('App.System.StorageAliyunOss')->rootUrl;
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
        $config = Be::getConfig('App.System.StorageAliyunOss');
        try {
            $endpoint = $config->internal ? $config->endpointInternal : $config->endpoint;
            $ossClient = new OssClient($config->accessKeyId, $config->accessKeySecret, $endpoint);
            $ossClient->setConnectTimeout(30);
            $exist = $ossClient->doesObjectExist($config->bucket, $path);
            if (!$exist) {
                $ossClient->uploadFile($config->bucket, $path, $tmpFile);
            }
        } catch (OssException $e) {
            throw new StorageException('Aliyun OSS upload file error：' . $e->getMessage());
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
        $config = Be::getConfig('App.System.StorageAliyunOss');
        try {
            $endpoint = $config->internal ? $config->endpointInternal : $config->endpoint;
            $ossClient = new OssClient($config->accessKeyId, $config->accessKeySecret, $endpoint);
            $ossClient->setConnectTimeout(30);
            $exist = $ossClient->doesObjectExist($config->bucket, $path);
            if ($exist) {
                $ossClient->deleteObject($config->bucket, $path);
            }
        } catch (OssException $e) {
            throw new StorageException('Aliyun OSS remove file error：' . $e->getMessage());
        }
    }


}
