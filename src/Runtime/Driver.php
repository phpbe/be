<?php

namespace Be\Runtime;


/**
 *  运行时
 */
abstract class Driver
{

    protected $rootPath = null;

    protected $adminAlias = 'admin'; // 后台功能虑拟目录

    public function __construct()
    {
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


}
