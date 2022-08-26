<?php

namespace Be;

/**
 * Cache 静态快速访问类
 *
 * Class Cache
 *
 * @package Be
 * @method static bool close() 手动关闭
 * @method static mixed get($key) 获取 指定的缓存 值
 * @method static array getMany($keys) 获取 多个指定的缓存 值
 * @method static bool set($key, $value, $expire = 0) 设置缓存
 * @method static bool setMany($values, $expire = 0) 设置缓存
 * @method static bool has($key) 指定键名的缓存是否存在
 * @method static bool delete($key) 删除指定键名的缓存
 * @method static false|int increase($key, $step = 1) 自增缓存（针对数值缓存）
 * @method static false|int decrease($key, $step = 1) 自减缓存（针对数值缓存）
 * @method static bool flush() 清除缓存
 * @method static mixed proxy($name, $callable, $expire = 0) 缓存代理
 */
abstract class Cache
{
    public static function __callStatic($method, $args)
    {
        return Be::getCache()->$method(...$args);
    }
}
