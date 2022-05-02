<?php

namespace Be\Log\Driver;

use Be\Be;
use Be\Log\Driver;

/**
 * 日志驱动
 */
class File extends Driver
{

    /**
     * 日志存储实现
     *
     * @param array $content 日志内容
     */
    protected function write(array $content)
    {
        $t = time();

        $year = date('Y', $t);
        $month = date('m', $t);
        $day = date('d', $t);

        $dir = Be::getRuntime()->getDataPath() . '/log/' .  $year . '/' . $month . '/' . $day . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        $logFileName = $content['id'];

        $logFilePath = $dir . $logFileName;

        if (!file_exists($logFilePath)) {
            file_put_contents($logFilePath, json_encode($content));
            chmod($logFilePath, 0777);
        }

        $indexFilePath = $dir . 'index';
        $f = fopen($indexFilePath, 'ab+');
        if ($f) {
            fwrite($f, pack('H*', $logFileName));
            fwrite($f, pack('L', $t));
            fclose($f);
        }
    }

}
