<?php

namespace Be\Task;


class TaskHelper
{


    /**
     * 是否有效的执行计划表达式
     * @param $schedule
     * @return bool
     */
    public static function isScheduleValid($schedule)
    {
        $parts = explode(' ', trim($schedule));
        if (count($parts) !== 5) {
            return false;
        }

        // 分
        if (is_numeric($parts[0])) {
            $value = (int)$parts[0];
            if ($value > 59 || $value < 0) {
                return false;
            }
        }

        // 时
        if (is_numeric($parts[1])) {
            $value = (int)$parts[1];
            if ($value > 23 || $value < 0) {
                return false;
            }
        }

        // 日
        if (is_numeric($parts[2])) {
            $value = (int)$parts[2];
            if ($value > 31 || $value < 1) {
                return false;
            }
        }

        // 月
        if (is_numeric($parts[3])) {
            $value = (int)$parts[3];
            if ($value > 12 || $value < 1) {
                return false;
            }
        }

        // 周
        if (is_numeric($parts[4])) {
            $value = (int)$parts[4];
            if ($value > 6 || $value < 0) {
                return false;
            }
        }

        foreach ($parts as $part) {
            if (is_numeric($part)) {
                if ($part !== (string)(int)$part) {
                    return false;
                }

                continue;
            }

            if ($part === '*') {
                continue;
            }

            $rules = explode(',', $part);
            foreach ($rules as $rule) {

                if (is_numeric($rule)) {
                    continue;
                }

                // 0-29/3
                if (strpos($rule, '/')) {
                    $fraction = explode('/', $rule);
                    if (count($fraction) !== 2) {
                        return false;
                    }

                    $numerator = $fraction[0];
                    $denominator = $fraction[1];

                    if (!is_numeric($denominator)) {
                        return false;
                    }

                    if (strpos($numerator, '-')) {
                        $scheduleRuleValues = explode('-', $numerator);
                        if (count($scheduleRuleValues) !== 2) {
                            return false;
                        }

                        if (!is_numeric($scheduleRuleValues[0]) || !is_numeric($scheduleRuleValues[1])) {
                            return false;
                        }

                    } else {
                        if ($numerator !== '*') {
                            return false;
                        }
                    }
                } elseif (strpos($rule, '-')) {
                    // 30-59
                    $ruleValues = explode('-', $rule);
                    if (count($ruleValues) !== 2) {
                        return false;
                    }

                    if (!is_numeric($ruleValues[0]) || !is_numeric($ruleValues[1])) {
                        return false;
                    }
                } else {
                    return false;
                }

            }

        }

        return true;
    }

    /**
     * 执行计划是否匹配对应时间
     *
     * @param string $schedule 执行计划，如: 0-29/2,30-59/3 1-2,4 1,3,5,7,9 1-6 *
     * @param int $timestamp 指定时间戳
     * @return bool
     */
    public static function isOnTime($schedule, $timestamp = 0)
    {
        $schedule = explode(' ', $schedule);
        if (count($schedule) !== 5) return false;

        if ($timestamp === 0) $timestamp = time();

        return self::isScheduleMatch($schedule[0], date('i', $timestamp)) &&
            self::isScheduleMatch($schedule[1], date('G', $timestamp)) &&
            self::isScheduleMatch($schedule[2], date('j', $timestamp)) &&
            self::isScheduleMatch($schedule[3], date('n', $timestamp)) &&
            self::isScheduleMatch($schedule[4], date('N', $timestamp));
    }

    /**
     * 比对计划任务时间配置项是否匹配当前时间
     *
     * @param string $scheduleRule 计划任务时间配置项规则
     * @param int $timeValue 时间值
     * @return bool
     */
    protected static function isScheduleMatch($scheduleRule, $timeValue)
    {
        if (!is_numeric($timeValue)) return false;
        $timeValue = intval($timeValue);

        $match = false;
        if ($scheduleRule === '*') {
            $match = true;
        } else {
            $scheduleRules = explode(',', $scheduleRule);
            foreach ($scheduleRules as $scheduleRule) {
                // 0-29/3
                if (strpos($scheduleRule, '/')) {
                    $fraction = explode('/', $scheduleRule);
                    if (count($fraction) !== 2) {
                        continue;
                    }

                    $numerator = $fraction[0];
                    $denominator = $fraction[1];

                    if (!is_numeric($denominator)) {
                        continue;
                    }

                    $denominator = (int)$denominator;

                    if (strpos($numerator, '-')) {

                        $scheduleRuleValues = explode('-', $numerator);
                        if (count($scheduleRuleValues) !== 2) {
                            continue;
                        }

                        if (!is_numeric($scheduleRuleValues[0]) || !is_numeric($scheduleRuleValues[1])) {
                            continue;
                        }

                        $scheduleRule0 = (int)$scheduleRuleValues[0];
                        $scheduleRule1 = (int)$scheduleRuleValues[1];

                        if ($scheduleRule0 <= $timeValue && $timeValue <= $scheduleRule1) {
                            if (($timeValue - $scheduleRule0) % $denominator === 0) {
                                $match = true;
                                break;
                            }
                        }

                    } else {
                        if ($numerator === '*') {
                            if ($timeValue % $denominator === 0) {
                                $match = true;
                                break;
                            }
                        }
                    }
                } else {
                    // 30-59
                    if (strpos($scheduleRule, '-')) {
                        $scheduleRuleValues = explode('-', $scheduleRule);
                        if (count($scheduleRuleValues) !== 2) {
                            continue;
                        }

                        if (!is_numeric($scheduleRuleValues[0]) || !is_numeric($scheduleRuleValues[1])) {
                            continue;
                        }

                        $scheduleRule0 = (int)$scheduleRuleValues[0];
                        $scheduleRule1 = (int)$scheduleRuleValues[1];

                        if ($scheduleRule0 <= $timeValue && $timeValue <= $scheduleRule1) {
                            $match = true;
                            break;
                        }
                    } else {
                        if ($scheduleRule === '*') {
                            $match = true;
                            break;
                        } else {
                            $scheduleRule = (int)$scheduleRule;
                            if ($scheduleRule === $timeValue) {
                                $match = true;
                                break;
                            }
                        }
                    }
                }
            }
        }

        return $match;
    }

}
