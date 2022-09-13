<?php

namespace Be\Language;

use Be\Be;

/**
 * 语言基类
 */
class Driver
{

    public string $package = '';
    public string $name = '';

    public array $keyValues = [];

    /**
     * 翻译
     * @param string $text
     * @param string $args
     * @return string
     */
    public function translate(string $text, string ...$args): string
    {
        if (isset($this->keyValues[$text])) {
            $text = $this->keyValues[$text];
        }

        if (count($args) > 0) {
            $i = 0;
            foreach ($args as $arg) {
                $text = str_replace('{' . $i . '}', $arg, $text);
                $i++;
            }
        }

        return $text;
    }

}
