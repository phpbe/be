<?php

namespace Be;

/**
 * Response 静态快速访问类
 *
 * Class Response
 *
 * @package Be
 */
abstract class Response
{
    public static function __callStatic($method, $args)
    {
        return Be::getResponse()->$method(...$args);
    }
}
