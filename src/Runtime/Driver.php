<?php

namespace Be\Runtime;


/**
 *  运行时
 */
abstract class Driver
{
    protected $mode = null; // 运行模式 Swoole / Common

    protected $rootPath = null;

    protected $adminAlias = 'admin'; // 后台功能虑拟目录

    public function __construct()
    {
    }

    /**
     * 获取BE框架启动模式
     *
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * 当前是否Swoole模式
     *
     * @return bool
     */
    public function isSwooleMode(): bool
    {
        return $this->mode === 'Swoole';
    }

    /**
     * 当前是否Worker进程
     *
     * @return bool
     */
    public function isWorkerProcess(): bool
    {
        return false;
    }

    /**
     * 当前是否Task进程
     *
     * @return bool
     */
    public function isTaskProcess(): bool
    {
        return false;
    }

    /**
     * 当前是否用户自定义进程
     *
     * @return bool
     */
    public function isUserProcess(): bool
    {
        return false;
    }

    /**
     * 设置BE框架的根路径
     *
     * @param string $rootPath BE框架的根路径，绝对路径
     */
    public function setRootPath(string $rootPath)
    {
        $this->rootPath = $rootPath;
    }

    /**
     * 获取BE框架的根路径
     *
     * @return string
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * @param string $adminAlias
     */
    public function setAdminAlias(string $adminAlias)
    {
        $this->adminAlias = $adminAlias;
    }

    /**
     * @return string
     */
    public function getAdminAlias(): string
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
