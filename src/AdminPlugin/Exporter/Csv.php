<?php

namespace Be\AdminPlugin\Exporter;


class Csv extends Driver
{

    private bool $started = false;

    private $handler = null;
    private int $index = 0;

    /**
     * 准备导出
     * @return Driver
     */
    public function start(): Driver
    {
        if (!$this->started) {
            $this->started = true;
            session_write_close();
            set_time_limit($this->timeLimit);
            ini_set('memory_limit', $this->memoryLimit);

            if ($this->outputType === 'http') {
                header('Content-Type: application/csv');
                header('Content-Transfer-Encoding: binary');
                if ($this->outputFileNameOrPath === null) {
                    header('Content-Disposition: attachment; filename=' . date('YmdHis') . '.csv');
                } else {
                    header('Content-Disposition: attachment; filename=' . $this->outputFileNameOrPath);
                }
                header('Pragma:no-cache');
            } else {
                $this->handler = fopen($this->outputFileNameOrPath, 'w') or die('写入 ' . $this->outputFileNameOrPath . '失败');
            }
        }

        return $this;
    }

    /**
     * 设置表格头
     *
     * @param array $headers
     * @return Driver
     */
    public function setHeaders($headers = []): Driver
    {
        if (!$this->started) {
            $this->start();
        }

        foreach ($headers as &$header) {

            if ($this->charset !== 'UTF-8') {
                $header = iconv('UTF-8', $this->charset . '//IGNORE', $header);
            }

            if (strpos($header, '"') !== false) {
                $header = str_replace('"', '""', $header);
            }
            $header = '"' . $header . '"';
        }
        unset($header);

        $line = implode(',', $headers) . "\r\n";
        if ($this->outputType === 'http') {
            echo $line;
        } else {
            fwrite($this->handler, $line);
        }

        $this->index++;

        return $this;
    }

    /**
     * 添加一行数据
     *
     * @param array $row
     * @return Driver
     */
    public function addRow($row = []): Driver
    {
        if (!$this->started) {
            $this->start();
        }

        foreach ($row as &$x) {
            if (is_numeric($x)) {
                // 大数字防止展示成科学计数法
                $len = strlen($x);
                $dotPos = strpos($x, '.');
                if ($dotPos === false) {
                    if ($len >= 12) {
                        $x .= "\t";
                    }
                } else {
                    if ($len >= 16) {
                        $x .= "\t";
                    } else {
                        if ($dotPos >= 12) {
                            $x .= "\t";
                        }
                    }
                }

            } else {
                if ($this->charset !== 'UTF-8') {
                    $x = iconv('UTF-8', $this->charset . '//IGNORE', $x);
                }
                if (strpos($x, '"') !== false) {
                    $x = str_replace('"', '""', $x);
                }
            }
            $x = '"' . $x . '"';
        }
        unset($x);

        $line = implode(',', $row) . "\r\n";
        if ($this->outputType === 'http') {
            echo $line;

            $this->index++;
            if ($this->index % 5000 === 0) {
                ob_flush();
                flush();
            }
        } else {
            fwrite($this->handler, $line);
            $this->index++;
        }

        return $this;
    }

    /**
     * 结束输出，收尾
     * @return Driver
     */
    public function end(): Driver
    {
        if ($this->outputType === 'file' && is_resource($this->handler)) {
            fclose($this->handler);
        }
        return $this;
    }

}