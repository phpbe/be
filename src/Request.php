<?php

namespace Be;

/**
 * Request 静态快速访问类
 *
 * Class Request
 *
 * @package Be
 */
abstract class Request
{
    public static function __callStatic($method, $args)
    {
        return Be::getRequest()->$method(...$args);
    }
}
