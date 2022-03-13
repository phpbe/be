<?php

namespace Be\Util\Time;

class Datetime
{
    /**
     * 格式化时间
     *
     * @param string $datetime 字符型时间， 例如：2000-01-01 12:00:00
     * @param int $maxDays 多少天前或后以默认时间格式输出
     * @param string $defaultFormat 默认时间格式
     * @return string
     */
    public static function formatTime(string $datetime, int $maxDays = 30, string $defaultFormat = 'Y-m-d'): string
    {
        return self::formatTimestamp(strtotime($datetime), $maxDays, $defaultFormat);
    }

    /**
     * 格式化时间
     *
     * @param int $timestamp unix 时间戳
     * @param int $maxDays 多少天前或后以默认时间格式输出
     * @param string $defaultFormat 默认时间格式
     * @return string
     */
    public static function formatTimestamp(int $timestamp, int $maxDays = 30, string $defaultFormat = 'Y-m-d'): string
    {
        $t = time();

        $seconds = $t - $timestamp;

        // 如果是{$maxDays}天前，直接输出日期
        $maxSeconds = $maxDays * 86400;
        if ($seconds > $maxSeconds || $seconds < -$maxSeconds) return date($defaultFormat, $timestamp);

        if ($seconds > 86400) {
            $days = intval($seconds / 86400);
            if ($days === 1) {
                if (date('a', $timestamp) === 'am') return '昨天上午';
                else return '昨天下午';
            } elseif ($days === 2) {
                return '前天';
            }
            return $days . '天前';
        } elseif ($seconds > 3600) return intval($seconds / 3600) . '小时前';
        elseif ($seconds > 60) return intval($seconds / 60) . '分钟前';
        elseif ($seconds >= 0) return '刚才';
        elseif ($seconds > -60) return '马上';
        elseif ($seconds > -3600) return intval(-$seconds / 60) . '分钟后';
        elseif ($seconds > -86400) return intval(-$seconds / 3600) . '小时后';
        else {
            $days = intval(-$seconds / 86400);
            if ($days === 1) {
                if (date('a', $timestamp) === 'am') return '明天上午';
                else return '明天下午';
            } elseif ($days === 2) {
                return '后天';
            }
            return $days . '天后';
        }
    }

    /**
     * 获取后一个天的时间
     *
     * @param string $datetime 日期 例：2022-02-28 12:00:00
     * @return string 日期 例：2000-03-01 12:00:00
     */
    public static function getNextDay(string $datetime): string
    {
        return date('Y-m-d H:i:s', strtotime($datetime) + 86400);
    }

    /**
     * 获取后N天的时间
     *
     * @param string $datetime 日期 例：2022-02-28 12:00:00
     * @param int $n 天数
     * @return string 日期 例：2000-03-02 12:00:00
     */
    public static function getNextNDay(string $datetime, int $n = 1): string
    {
        return date('Y-m-d H:i:s', strtotime($datetime) + 86400 * $n);
    }

    /**
     * 获取前一个天的时间
     *
     * @param string $datetime 日期 例：2022-03-01 12:00:00
     * @return string 日期 例：2000-02-28 12:00:00
     */
    public static function getLastDay(string $datetime): string
    {
        return date('Y-m-d H:i:s', strtotime($datetime) - 86400);
    }

    /**
     * 获取前N天的时间
     *
     * @param string $datetime 日期 例：2022-03-01 12:00:00
     * @param int $n 天数
     * @return string 日期 例：2000-02-28 12:00:00
     */
    public static function getLastNDay(string $datetime, int $n = 1): string
    {
        return date('Y-m-d H:i:s', strtotime($datetime) - 86400 * $n);
    }

    /**
     * 获取后一个月的时间
     *
     * @param string $datetime 时间 例：2000-01-31 12:00:00
     * @return string 时间 例：2000-02-29 12:00:00
     */
    public static function getNextMonth(string $datetime): string
    {
        return self::getNextNMonth($datetime, 1);
    }

    /**
     * 获取后N个月的时间
     *
     * @param string $datetime 时间 例：2000-01-31 12:00:00
     * @param int $n 月数
     * @return string 时间 例：2000-02-29 12:00:00
     */
    public static function getNextNMonth(string $datetime, int $n = 1): string
    {
        $t = strtotime($datetime);
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

        $t2 = mktime(date('G', $t), date('i', $t), date('s', $t), $month, $day, $year);
        return date('Y-m-d H:i:s', $t2);
    }

    /**
     * 获取前个月的时间
     *
     * @param string $datetime 时间 例：2000-03-31 12:00:00
     * @return string 时间 例：2000-02-29 12:00:00
     */
    public static function getLastMonth(string $datetime): string
    {
        return self::getNextNMonth($datetime, -1);
    }

    /**
     * 获取前N个月的时间
     *
     * @param string $datetime 时间 例：2000-03-31 12:00:00
     * @param int $n 月数
     * @return string 时间 例：2000-02-29 12:00:00
     */
    public static function getLastNMonth(string $datetime, int $n = 1): string
    {
        return self::getNextNMonth($datetime, -$n);
    }

    /**
     * 当前时间
     *
     * @param string $format
     * @return false|string
     */
    public static function now(string $format = 'Y-m-d H:i:s'): string
    {
        return date($format, time());
    }

}
