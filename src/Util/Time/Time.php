<?php

namespace Be\Util\Time;

class Time
{

    /**
     * 格式化时间长度
     *
     * @param int $secs 秒
     * @param string $space 数字与单位间空格
     * @return string
     */
    public static function length(int $secs, string $space = ' '): string
    {
        $length = ' ';
        if ($secs > 86400) {
            $length = $space . ((int) ($secs / 86400)) . $space . '天';
            $secs = $secs % 86400;
        }

        if ($secs > 3600) {
            $length .= $space . ((int) ($secs / 3600)) . $space . '时';
            $secs = $secs % 3600;
        }

        if ($secs > 60) {
            $length .= $space . ((int) ($secs / 60)) . $space . '分';
            $secs = $secs % 60;
        }

        if ($secs > 0) {
            $length .= $space . $secs . $space . '秒';
        }

        if ($space !== '') {
            $length = trim($length, $space);
        }

        return $length;
    }


}
