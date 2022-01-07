<?php

namespace Be\AdminPlugin\Exporter;


use Be\AdminPlugin\AdminPluginException;

abstract class Driver
{

    protected $timeLimit = 3600;
    protected $outputType = 'http';
    protected $charset = 'GBK';
    protected $outputFileNameOrPath = null;
    protected $memoryLimit = '1g';
    protected $split = null; // 数据量过大时，拆分为多个文件，打成一个 zip 压缩包

    /**
     * 设置执行超时时间
     *
     * @param int $timeLimit
     * @return Driver
     */
    public function setTimeLimit($timeLimit)
    {
        $this->timeLimit = $timeLimit;
        return $this;
    }

    /**
     * 设置输出
     *
     * @param string $outputType 类型：http - 保存到HTTP下载 / file - 保存到指定路径
     * @param string $outputFileNameOrPath 文件名或或路径
     * @return Driver
     * @throws AdminPluginException
     */
    public function setOutput($outputType, $outputFileNameOrPath = null)
    {
        if (!in_array($outputType, ['http', 'file'])) {
            throw new AdminPluginException('输出类型' . $outputType . '不支持！');
        }

        if ($outputType === 'file' && !$outputFileNameOrPath) {
            throw new AdminPluginException('输出类型为' . $outputType . '时必须设置输出路径！');
        }

        $this->outputType = $outputType;
        $this->outputFileNameOrPath = $outputFileNameOrPath;
        return $this;
    }

    /**
     * 设置字符编码
     *
     * @param string $charset 字符编码
     * @return Driver
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * TODO 设置拆分为多个文件的行数
     *
     * @param int $split 行数，到达该行时生成一个新的文件
     * @return Driver
     */
    public function setSplit($split)
    {
        $this->split = $split;
        return $this;
    }

    /**
     * 设置最大内存占用
     *
     * @param int $memoryLimit 最大内存占用
     * @return Driver
     */
    public function setMemoryLimit($memoryLimit)
    {
        $this->memoryLimit = $memoryLimit;
        return $this;
    }

    /**
     * 设置表格头
     *
     * @param array $headers
     * @return Driver
     */
    abstract public function setHeaders($headers = []);

    /**
     * 添加一行数据
     *
     * @param array $row
     * @return Driver
     */
    abstract public function addRow($row = []);

    /**
     * 添加多行数据
     *
     * @param array $rows
     * @return Driver
     */
    public function addRows($rows = [])
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }

        return $this;
    }

    /**
     * 结束输出，收尾
     * @return Driver
     */
    public function end()
    {
        return $this;
    }
}