<?php

namespace Be;

/**
 * Session 静态快速访问类
 *
 * Class Session
 *
 * @package Be
 */
abstract class Session
{
    public static function __callStatic($method, $args)
    {
        return Be::getSession()->$method(...$args);
    }
}
