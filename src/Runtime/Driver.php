<?php

namespace Be\Runtime;


/**
 *  运行时
 */
abstract class Driver
{
    protected $mode = null; // 运行模式 Swoole / Common

    protected $rootPath = null;

    protected $dataDir = 'data'; // 存放系统生成的永久性文件，如配置文件

    protected $uploadDir = 'upload'; // 用户上传的数据

    protected $adminAlias = 'admin'; // 后台功能虑拟目录

    public function __construct()
    {
    }

    /**
     * 获取BE框架启动模式
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * 设置BE框架的根路径
     *
     * @param string $rootPath BE框架的根路径，绝对路径
     */
    public function setRootPath($rootPath)
    {
        $this->rootPath = $rootPath;
    }

    /**
     * 获取BE框架的根路径
     *
     * @return string
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }

    /**
     * @return string
     */
    public function getCachePath()
    {
        return $this->rootPath . '/' . $this->dataDir . '/Cache';
    }

    /**
     * @param string $dataDir
     */
    public function setDataDir($dataDir)
    {
        $this->dataDir = $dataDir;
    }

    /**
     * @return string
     */
    public function getDataDir()
    {
        return $this->dataDir;
    }

    /**
     * @return string
     */
    public function getDataPath()
    {
        return $this->rootPath . '/' . $this->dataDir;
    }

    /**
     * @param string $uploadDir
     */
    public function setUploadDir($uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    /**
     * @return string
     */
    public function getUploadDir()
    {
        return $this->uploadDir;
    }

    /**
     * @return string
     */
    public function getUploadPath()
    {
        return $this->rootPath . '/' . $this->uploadDir;
    }

    /**
     * @param string $adminAlias
     */
    public function setAdminAlias($adminAlias)
    {
        $this->adminAlias = $adminAlias;
    }

    /**
     * @return string
     */
    public function getAdminAlias()
    {
        return $this->adminAlias;
    }


    abstract function execute();


    public function stop()
    {
    }

    public function reload()
    {
    }

    public function task($data)
    {
    }

    public function getSwooleHttpServer()
    {
        return null;
    }


}
