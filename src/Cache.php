<?php

namespace Be;

/**
 * Cache 静态快速访问类
 *
 * Class Cache
 *
 * @package Be
 */
abstract class Cache
{
    public static function __callStatic($method, $args)
    {
        return Be::getCache()->$method(...$args);
    }
}
