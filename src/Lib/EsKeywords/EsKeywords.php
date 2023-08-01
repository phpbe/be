<?php

namespace Be\Lib\EsKeywords;

use Be\Lib\LibException;

/**
 *  使用ES提取关键词
 *
 * @package Be\Lib\EsKeywords
 * @author liu12 <i@liu12.com>
 */
class EsKeywords
{

    private ?string $dictPath = null;
    private ?string $dictHandler = null;

    public function __construct()
    {
        $this->dictPath = dirname(__FILE__) . '/dict.db';
        $this->dictHandler = file_get_contents($this->dictPath);
    }

    /**
     * 设置字典位置
     *
     * @param $path
     * @return void
     */
    public function setDict($path)
    {
        if (is_file($path)) {
            throw new LibException('File(' . $path . ') does not exist!');
        }

        $this->dictPath = $path;
        $this->dictHandler = file_get_contents($this->dictPath);
    }

    /**
     * 使用ES从指定肉容中提取关健词
     *
     * @param string $content
     * @return void
     */
    public function extract(string $content)
    {

    }

}
