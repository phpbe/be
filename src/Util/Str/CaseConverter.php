<?php

namespace Be\Util\Str;

class CaseConverter
{

    /**
     * 下划线转驼峰
     *
     * @param string $str
     * @param bool|null $ucFirst 是否转换首字母大小写， null：不转换、true：转为大写、false：转为小写
     * @return string
     */
    public static function underline2Camel(string $str, bool $ucFirst = null):string
    {
        $str = str_replace([
            '_a', '_b', '_c', '_d', '_e', '_f', '_g', '_h', '_i', '_j', '_k', '_l', '_m', '_n', '_o', '_p', '_q', '_r', '_s', '_t', '_u', '_v', '_w', '_x', '_y', '_z'
        ], [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        ], $str);

        if ($ucFirst === true) {
            return ucfirst($str);
        } elseif ($ucFirst === false) {
            return lcfirst($str);
        }

        return $str;
    }

    /**
     * 下划线转首字母大写驼峰
     *
     * @param string $str
     * @return string
     */
    public static function underline2CamelUcFirst(string $str):string
    {
        return self::underline2Camel($str, true);
    }

    /**
     * 下划线转首字母小写驼峰
     *
     * @param string $str
     * @return string
     */
    public static function underline2CamelLcFirst(string $str):string
    {
        return self::underline2Camel($str, false);
    }

    /**
     * 连字号（中划线）转驼峰
     *
     * @param string $str
     * @param bool|null $ucFirst 是否转换首字母大小写， null：不转换、true：转为大写、false：转为小写
     * @return string
     */
    public static function Hyphen2Camel(string $str, bool $ucFirst = null):string
    {
        $str = str_replace([
            '-a', '-b', '-c', '-d', '-e', '-f', '-g', '-h', '-i', '-j', '-k', '-l', '-m', '-n', '-o', '-p', '-q', '-r', '-s', '-t', '-u', '-v', '-w', '-x', '-y', '-z'
        ], [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        ], $str);

        if ($ucFirst === true) {
            return ucfirst($str);
        } elseif ($ucFirst === false) {
            return lcfirst($str);
        }

        return $str;
    }

    /**
     * 连字号（中划线）转首字母大写驼峰
     *
     * @param string $str
     * @return string
     */
    public static function Hyphen2CamelUcFirst(string $str):string
    {
        return self::Hyphen2Camel($str, true);
    }

    /**
     * 连字号（中划线）转转首字母小写驼峰
     *
     * @param string $str
     * @return string
     */
    public static function Hyphen2CamelLcFirst(string $str):string
    {
        return self::Hyphen2Camel($str, false);
    }

    /**
     * 驼峰转下划线
     *
     * @param string $str
     * @param bool $trimFirst 是否修前第一个字符
     * @return string
     */
    public static function camel2Underline(string $str, bool $trimFirst = true):string
    {
        $str = str_replace([
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        ], [
            '_a', '_b', '_c', '_d', '_e', '_f', '_g', '_h', '_i', '_j', '_k', '_l', '_m', '_n', '_o', '_p', '_q', '_r', '_s', '_t', '_u', '_v', '_w', '_x', '_y', '_z'
        ], $str);

        if ($trimFirst && substr($str, 0, 1) === '_') $str = substr($str, 1);

        return $str;
    }


    /**
     * 驼峰转连字号（中划线）
     *
     * @param string $str
     * @param bool $trimFirst 是否修前第一个字符
     * @return string
     */
    public static function camel2Hyphen(string $str, bool $trimFirst = true):string
    {
        $str = str_replace([
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        ], [
            '-a', '-b', '-c', '-d', '-e', '-f', '-g', '-h', '-i', '-j', '-k', '-l', '-m', '-n', '-o', '-p', '-q', '-r', '-s', '-t', '-u', '-v', '-w', '-x', '-y', '-z'
        ], $str);

        if ($trimFirst && substr($str, 0, 1) === '-') $str = substr($str, 1);

        return $str;
    }



}
