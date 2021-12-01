<?php

namespace Be\Util;

class Validator
{

    /**
     * 是否是合法的手机号码
     *
     * @param string $mobile 手机号码
     * @return bool
     */
    public static function isMobile($mobile)
    {
        return preg_match('/^1[3-9]\d{9}$/', $mobile);
    }

    /**
     * 是否是合法的邮箱
     *
     * @param string $email 邮箱
     * @return bool
     */
    public static function isEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * 密码是否安体
     *
     * @param string $password 密码
     * @param array $check 检查项
     * @throws UtilException
     */
    public static function checkPasswordSecure(string $password, array $check = null)
    {
        if ($check === null) {
            $check = [
                'length' => 8,  // 长度是否大于 8 位
                'uppercase' => false, // 包含大写字母
                'lowercase' => false, // 包含小写字母
                'letter' => true, // 包含字母
                'number' => true, // 包含数字
                'specialChar' => false, // 包含特殊字符
            ];
        }

        if (!isset($check['length'])) {
            $check['length'] = 8;
        }

        if (strlen($password) < $check['length']) {
            throw new UtilException('密码长度最少' . $check['length'] . '位！');
        }

        if (isset($check['uppercase']) && $check['uppercase']) {
            if (!preg_match('/[A-Z]/', $password)) {
                throw new UtilException('密码中须包含大写字母！');
            }
        }

        if (isset($check['uppercase']) && $check['uppercase']) {
            if (!preg_match('/[a-z]/', $password)) {
                throw new UtilException('密码中须包含小写字母！');
            }
        }

        if (isset($check['letter']) && $check['letter']) {
            if (!preg_match('/[A-Za-z]/', $password)) {
                throw new UtilException('密码中须包含字母！');
            }
        }

        if (isset($check['number']) && $check['number']) {
            if (!preg_match('/[0-9]/', $password)) {
                throw new UtilException('密码中须包含数字！');
            }
        }

        if (isset($check['specialChar']) && $check['specialChar']) {
            if (!preg_match('/[~!@#$%^&*()\-_=+{};:<,.>?]/', $password)) {
                throw new UtilException('密码中须包含特殊符号！');
            }
        }
    }

    /**
     * 密码是否安体
     *
     * @param string $password 密码
     * @param array $check 检查项
     * @return bool
     */
    public static function isPasswordSecure(string $password, array $check = null)
    {
        try {
            self::checkPasswordSecure($password, $check);
        } catch (\Throwable $t) {
            return false;
        }

        return true;
    }

    /**
     * 获取密码安全分值
     *
     * @param string $password 密码
     * @return bool
     */
    public static function getPasswordSecureScore(string $password)
    {
        $score = 0;
        if (!preg_match('/[A-Z]/', $password)) {
            $score++;
        }

        if (!preg_match('/[a-z]/', $password)) {
            $score++;
        }

        if (!preg_match('/[0-9]/', $password)) {
            $score++;
        }

        if (!preg_match('/[~!@#$%^&*()\-_=+{};:<,.>?]/', $password)) {
            $score++;
        }

        $len = strlen($password);
        if ($len >= 8) {
            $score++;
        } elseif ($len > 0) {
            $score = (int) ($score * pow(0.8, 8 - $len));
        }

        return $score;
    }

    /**
     * 是否是合法的IP
     *
     * @param string $ip
     * @return bool
     */
    public static function isIp($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP);
    }

    /**
     * 是否是合法的MAC地址
     *
     * @param string $mac
     * @return bool
     */
    public static function isMac($mac)
    {
        return filter_var($mac, FILTER_VALIDATE_MAC);
    }

    /**
     * 是否是合法的域名
     *
     * @param string $domain
     * @return bool
     */
    public static function isDomain($domain)
    {
        return filter_var($domain, FILTER_VALIDATE_DOMAIN);
    }

    /**
     * 是否是合法的网址
     *
     * @param string $url
     * @return bool
     */
    public static function isUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }


    /**
     * 是否是合法的身份证号
     *
     * @param string $idCard
     * @return bool
     */
    public static function isIdCard($idCard)
    {
        if (strlen($idCard) > 18) return false;
        return preg_match("/^\d{6}((1[89])|(2\d))\d{2}((0\d)|(1[0-2]))((3[01])|([0-2]\d))\d{3}(\d|X)$/i", $idCard);
    }

    /**
     * 是否是合法的邮政编码
     *
     * @param string $postcode
     * @return bool
     */
    public static function isPostcode($postcode)
    {
        return preg_match('/\d{6}/', $postcode);
    }

    /**
     * 是否为中文
     *
     * @param string $str
     * @return bool
     */
    public static function isChinese($str)
    {
        return preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $str);
    }


}
