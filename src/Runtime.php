<?php

namespace Be;

/**
 * Runtime 静态快速访问类
 *
 * Class Runtime
 *
 * @package Be
 */
abstract class Runtime
{
    public static function __callStatic($method, $args)
    {
        return Be::getRuntime()->$method(...$args);
    }
}
