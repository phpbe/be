<?php

namespace Be;

/**
 * Runtime 静态快速访问类
 *
 * Class Runtime
 *
 * @package Be
 * @method static string getMode() 获取BE框架启动模式
 * @method static string setRootPath($rootPath) 设置BE框架的根路径
 * @method static string getRootPath() 获取BE框架的根路径
 * @method static setAdminAlias($adminAlias) 设置后台路径别名
 * @method static string getAdminAlias($uploadDir) 获取后台路径别名
 * @method static execute() 启动
 * @method static stop() 停止
 * @method static reload() 重启
 * @method static task($data) 投递计划任务
 * @method static getSwooleHttpServer() 获取Swoole Http Server 实例
 */
abstract class Runtime
{
    public static function __callStatic($method, $args)
    {
        return Be::getRuntime()->$method(...$args);
    }
}
