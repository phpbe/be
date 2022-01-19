<?php

namespace Be\Util\Crypt;


class Sha
{

    /**
     * sha256
     *
     * @param string $str 要加密的字符
     * @return string
     */
    public static function sha256(string $str)
    {
        return hash('sha256', $str);
    }

    /**
     * sha512
     *
     * @param string $str 要加密的字符
     * @return string
     */
    public static function sha512(string $str)
    {
        return hash('sha512', $str);
    }



}
