<?php

namespace Be\Util\Crypt\Traits;

trait OpenSSL
{

    /**
     * 加密
     *
     * @param string $str 要加密的字符
     * @param string $pwd 密码
     * @return string
     */
    public static function encrypt(string $str, string $pwd)
    {
        $ivLen = openssl_cipher_iv_length(self::$cipherMethod);
        $iv = $ivLen > 0 ? openssl_random_pseudo_bytes($ivLen) : '';
        $raw = openssl_encrypt($str, self::$cipherMethod, $pwd, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $raw);
    }

    /**
     * 解密
     *
     * @param string $str 要解密的字符
     * @param string $pwd 密码
     * @return string
     */
    public static function decrypt(string $str, string $pwd)
    {
        $str = base64_decode($str);
        $ivLen = openssl_cipher_iv_length(self::$cipherMethod);

        $iv = null;
        $raw = null;
        if ($ivLen > 0) {
            $iv = substr($str, 0, $ivLen);
            $raw = substr($str, $ivLen);
        } else {
            $iv = '';
            $raw = $str;
        }

        $text = openssl_decrypt($raw, self::$cipherMethod, $pwd, OPENSSL_RAW_DATA, $iv);
        return $text;
    }

    /**
     * 设置算法
     *
     * @param string $cipherMethod 算法
     */
    public static function setCipherMethod(string $cipherMethod)
    {
        self::$cipherMethod = $cipherMethod;
    }

}
