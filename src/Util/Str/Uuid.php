<?php

namespace Be\Util\Str;

use Be\Be;

class Uuid
{

    /**
     * 生成一个UUID
     *
     * @param bool $strip 是否移除 "-"
     * @return string
     */
    public static function create(bool $strip = false): string
    {
        if (function_exists('uuid_create')) {
            $uuid = uuid_create();
        } else {
            $uuid = Be::getDb()->uuid();
        }

        if ($strip) {
            $uuid = str_replace('-', '', $uuid);
        }

        return $uuid;
    }

    /**
     * UUID移除 "-"
     *
     * @param string $uuid 标准UUID
     * @return string
     */
    public static function strip(string $uuid): string
    {
        return str_replace('-', '', $uuid);
    }

    /**
     * UUID还原
     *
     * @param string $uuid 32位去除 "-" 的UUID
     * @return string
     */
    public static function restore(string $uuid): string
    {
        if (strlen($uuid) === 32) {
            $p1 = substr($uuid, 0, 8);
            $p2 = substr($uuid, 8, 4);
            $p3 = substr($uuid, 12, 4);
            $p4 = substr($uuid, 16, 4);
            $p5 = substr($uuid, 20);
            return $p1 . '-' . $p2 . '-' . $p3 . '-' . $p4 . '-' . $p5;
        }

        return $uuid;
    }

}
