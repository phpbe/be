<?php

namespace Be;

/**
 * Session 静态快速访问类
 *
 * Class Session
 *
 * @package Be
 * @method static string getId() 获取 session id
 * @method static string getName() 获取 session name
 * @method static int getExpire() 获取 session 超时时间
 * @method static start() 启动 SESSION
 * @method static string | array | \stdClass get($name = null, $default = null) 获取session 值
 * @method static set($name, $value) 向session中赋值
 * @method static bool has($name) 是否已设置指定名称的 session
 * @method static string | array | \stdClass delete($name) 删除除指定名称的 session
 */
abstract class Session
{
    public static function __callStatic($method, $args)
    {
        return Be::getSession()->$method(...$args);
    }
}
