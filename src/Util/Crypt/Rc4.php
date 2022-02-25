<?php

namespace Be\Util\Crypt;


class Rc4
{

    /**
     * 加密
     *
     * @param string $str 要加密的字符
     * @param string $pwd 密码
     * @return string
     */
    public static function encrypt(string $str, string $pwd): string
    {
        $raw = openssl_encrypt($str, 'RC4', $pwd, OPENSSL_RAW_DATA);
        return base64_encode($raw);
    }

    /**
     * 解密
     *
     * @param string $str 要解密的字符
     * @param string $pwd 密码
     * @return string
     */
    public static function decrypt(string $str, string $pwd): string
    {
        $str = base64_decode($str);
        $text = openssl_decrypt($str, 'RC4', $pwd, OPENSSL_RAW_DATA);
        return $text;
    }

}
