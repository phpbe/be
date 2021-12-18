<?php

namespace Be\App\System\Service\Admin;

use Be\Be;
use Be\App\ServiceException;

class Log
{

    /**
     * 获取日志年份列表
     *
     * @return array
     */
    public function getYears()
    {
        $dir = Be::getRuntime()->getDataPath() . '/log';
        $years = array();
        if (file_exists($dir) && is_dir($dir)) {
            $fileNames = scandir($dir);
            foreach ($fileNames as $fileName) {
                if ($fileName != '.' && $fileName != '..' && is_dir($dir . '/' . $fileName)) {
                    $years[] = $fileName;
                }
            }
        }
        return $years;
    }

    /**
     * 获取日志月份列表
     *
     * @param int $year 年
     * @return array
     */
    public function getMonths($year)
    {
        $dir = Be::getRuntime()->getDataPath() . '/log/' . $year;
        $months = array();
        if (file_exists($dir) && is_dir($dir)) {
            $fileNames = scandir($dir);
            foreach ($fileNames as $fileName) {
                if ($fileName != '.' && $fileName != '..' && is_dir($dir . '/' . $fileName)) {
                    $months[] = $fileName;
                }
            }
        }
        return $months;
    }

    /**
     * 获取日志日期列表
     *
     * @param int $year 年
     * @param int $month 月
     * @return array
     */
    public function getDays($year, $month)
    {
        $dir = Be::getRuntime()->getDataPath() . '/log/' . $year . '/' . $month;
        $days = array();
        if (file_exists($dir) && is_dir($dir)) {
            $fileNames = scandir($dir);
            foreach ($fileNames as $fileName) {
                if ($fileName != '.' && $fileName != '..' && is_dir($dir . '/' . $fileName)) {
                    $days[] = $fileName;
                }
            }
        }
        return $days;
    }

    /**
     * 获取指定日期的日志列表
     * @param int $year 年
     * @param int $month 月
     * @param int $day 日
     * @param int $offset 分面偏移量
     * @param int $limit 分页大小
     * @return array
     */
    public function getLogs($year, $month, $day, $offset = 0, $limit = 100)
    {
        $dataDir = Be::getRuntime()->getDataPath() . '/log/' . $year . '/' . $month . '/' . $day . '/';
        $indexPath = Be::getRuntime()->getDataPath() . '/log/' . $year . '/' . $month . '/' . $day . '/index';
        if (!is_file($indexPath)) return [];

        if ($offset < 0) $offset = 0;
        if ($limit <= 0) $limit = 20;
        if ($limit > 500) $limit = 500;

        $max = intval(filesize($indexPath) / 20) - 1;
        if ($max < 0) return [];

        $from = $offset;
        $to = $offset + $limit - 1;

        if ($from > $max) $from = $max;
        if ($to > $max) $to = $max;

        $fIndex = fopen($indexPath, 'rb');
        if (!$fIndex) return [];

        $logs = [];
        for ($i = $from; $i <= $to; $i++) {
            fseek($fIndex, $i * 20);

            $dataHashName = implode('', unpack('H*', fread($fIndex, 16)));
            $createTime = intval(implode('', unpack('L', fread($fIndex, 4))));

            $path = $dataDir . $dataHashName;
            if (file_exists($path)) {
                $data = file_get_contents($path);
                $data = json_decode($data, true);

                $log = [];
                $log['year'] = $year;
                $log['month'] = $month;
                $log['day'] = $day;
                $log['hash'] = $data['extra']['hash'];
                $log['file'] = $data['context']['file'];
                $log['line'] = $data['context']['line'];
                $log['code'] = $data['context']['code'];
                $log['message'] = $data['message'];
                $log['create_time'] = date('Y-m-d H:i:s', $createTime);
                $log['record_time'] = date('Y-m-d H:i:s', $data['extra']['record_time']);
                $logs[] = $log;
            }
        }
        fclose($fIndex);
        return $logs;
    }

    /**
     * 获取指定日期的日志总数
     *
     * @param int $year 年
     * @param int $month 月
     * @param int $day 日
     * @return int
     */
    public function getLogCount($year, $month, $day)
    {
        $path = Be::getRuntime()->getDataPath() . '/log/' . $year . '/' . $month . '/' . $day . '/index';
        if (!is_file($path)) return 0;
        return intval(filesize($path) / 20);
    }

    /**
     * 获取指定日期和索引的日志明细
     * @param int $year 年
     * @param int $month 月
     * @param int $day 日
     * @param string $hashName 日志的 hash 文件名
     * @return array
     * @throws ServiceException
     */
    public function getLog($year, $month, $day, $hashName)
    {
        $dataPath = Be::getRuntime()->getDataPath() . '/log/' . $year . '/' . $month . '/' . $day . '/' . $hashName;
        if (!is_file($dataPath)) {
            throw new ServiceException('打开日志数据文件不存在！');
        }

        $data = file_get_contents($dataPath);
        $data = json_decode($data, true);
        return $data;
    }

    /**
     * 删除日志
     *
     * @param string $range 删除范围
     * @param int $year 年
     * @param int $month 月
     * @param int $day 日
     */
    public function deleteLogs($range, $year, $month = 0, $day = 0)
    {
        $dir = null;
        if ($range == 'year') {
            $dir = Be::getRuntime()->getDataPath() . '/log/' . $year;
        } elseif ($range == 'month') {
            $dir = Be::getRuntime()->getDataPath() . '/log/' . $year . '/' . $month;
        } elseif ($range == 'day') {
            $dir = Be::getRuntime()->getDataPath() . '/log/' . $year . '/' . $month . '/' . $day;
        }

        \Be\Util\FileSystem\Dir::rm($dir);
    }
}
