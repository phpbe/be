<?php

namespace Be\Util\Time;

class Date
{

    /**
     * 获取后一个天的日期
     *
     * @param string $date 日期 例：2022-02-28
     * @return string 日期 例：2000-03-01
     */
    public static function getNextDay(string $date): string
    {
        return date('Y-m-d', strtotime($date) + 86400);
    }

    /**
     * 获取后N天的日期
     *
     * @param string $date 日期 例：2022-02-28
     * @param int $n 天数
     * @return string 日期 例：2000-03-02
     */
    public static function getNextNDay(string $date, int $n = 1): string
    {
        return date('Y-m-d', strtotime($date) + 86400 * $n);
    }


    /**
     * 获取前一个天的日期
     *
     * @param string $date 日期 例：2022-03-01
     * @return string 日期 例：2000-02-28
     */
    public static function getLastDay(string $date): string
    {
        return date('Y-m-d', strtotime($date) - 86400);
    }

    /**
     * 获取前N天的日期
     *
     * @param string $date 日期 例：2022-03-01
     * @param int $n 天数
     * @return string 日期 例：2000-02-28
     */
    public static function getLastNDay(string $date, int $n = 1): string
    {
        return date('Y-m-d', strtotime($date) - 86400 * $n);
    }

    /**
     * 获取后一个月的日期
     *
     * @param string $date 日期 例：2000-01-31
     * @return string 日期 例：2000-02-29
     */
    public static function getNextMonth(string $date): string
    {
        return self::getNextNMonth($date, 1);
    }

    /**
     * 获取后N个月的日期
     *
     * @param string $date 日期 例：2000-01-31
     * @param int $n 月数
     * @return string 日期 例：2000-03-31
     */
    public static function getNextNMonth(string $date, int $n = 1): string
    {
        $t = strtotime($date);
        $year = date('Y', $t);
        $month = date('n', $t);
        $day = date('j', $t);

        $month += $n;
        if ($month > 12) {
            while ($month > 12) {
                $year++;
                $month -= 12;
            }
        } elseif ($month <= 0) {
            while ($month < 0) {
                $year--;
                $month += 12;
            }
        }

        switch ($month) {
            case 1:
            case 3:
            case 5:
            case 7:
            case 8:
            case 10:
            case 12:
                if ($day > 31) $day = 31;
                break;
            case 2:
                $maxDay = (($year % 4 === 0 && $year % 100 !== 0) || $year % 400 === 0) ? 29 : 28;
                if ($day > $maxDay) $day = $maxDay;
                break;
            case 4:
            case 6:
            case 9:
            case 11:
                if ($day > 30) $day = 30;
                break;
        }

        $t2 = mktime(0, 0, 0, $month, $day, $year);
        return date('Y-m-d', $t2);
    }

    /**
     * 获取前个月
     *
     * @param string $date 日期 例：2000-03-31
     * @return string 日期 例：2000-02-29
     */
    public static function getLastMonth(string $date): string
    {
        return self::getNextNMonth($date, -1);
    }

    /**
     * 获取前N个月
     *
     * @param string $date 日期 例：2000-03-31
     * @param int $n 月数
     * @return string 日期 例：2000-01-31
     */
    public static function getLastNMonth(string $date, int $n = 1): string
    {
        return self::getNextNMonth($date, -$n);
    }

    /**
     * 当前时间
     *
     * @param string $format
     * @return false|string
     */
    public static function now(string $format = 'Y-m-d'): string
    {
        return date($format, time());
    }

}
