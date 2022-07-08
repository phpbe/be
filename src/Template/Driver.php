<?php

namespace Be\Template;

use Be\Be;

/**
 * 模板基类
 */
class Driver
{
    public $title = ''; // 标题
    public $metaKeywords = ''; // meta keywords
    public $metaDescription = '';  // meta description

    public function get(string $key, $default = null)
    {
        if (isset($this->$key)) return $this->$key;
        return $default;
    }

    public function display()
    {

    }

}
