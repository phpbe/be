<?php

namespace Be\Storage;

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
    abstract function getRootUrl();

    /**
     * 上传文件
     *
     * @param string $path 文件存储路径
     * @param string $tmpFile 上传的临时文件名
     * @return string 上传成功的文件的网址
     */
    abstract function uploadFile(string $path, string $tmpFile);

    /**
     * 删除文件
     *
     * @param string $path 文件存储路径
     */
    abstract function removeFile(string $path);

}

