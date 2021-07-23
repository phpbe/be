<?php
namespace Be\App\System;

/**
 * 应用安装器
 */
class UnInstaller extends \Be\App\UnInstaller
{

    /**
     * 卸载时需要执行的操作，如删除数据库表
     */
	public function uninstall()
	{
        throw new \Exception('系统应用不支持卸载！');
	}

}
