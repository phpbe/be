<?php

namespace Be\AdminPlugin\Exporter;

use Be\AdminPlugin\AdminPluginException;
use Be\AdminPlugin\Driver;
use Be\AdminPlugin\Exporter\Driver as ExporterDriver;

/**
 * 导出器
 *
 * Class Exporter
 * @package Be\AdminPlugin\Exporter
 */
class Exporter extends Driver
{

    private ?ExporterDriver $driver = null;

    public function setDriver($driverName): ExporterDriver
    {
        switch ($driverName) {
            case 'csv':
                $this->driver = new \Be\AdminPlugin\Exporter\Csv();
                break;
            case 'excel':
                $this->driver = new \Be\AdminPlugin\Exporter\Excel();
                break;
            default:
                throw new AdminPluginException('不支持的导出类型（可选值：csv/excel）！');
        }

        return $this->driver;
    }

    public function __call($name, $arguments)
    {
        if ($this->driver === null) {
            throw new AdminPluginException('请先设置导出类型！');
        }

        return call_user_func_array(array($this->driver, $name), $arguments);
    }

}