<?php

namespace Be\Util\Str;

/**
 * HTML处理
 */
class Html
{

    /**
     * 清理HTML
     *
     * @param string $html HTML 代码
     * @return string
     */
    private function clean(string $html): string
    {
        $html = trim($html);
        $html = strip_tags($html);
        $html = str_replace(array('&nbsp;', '&ldquo;', '&rdquo;', '　'), '', $html);
        $html = preg_replace("/\t/", '', $html);
        $html = preg_replace("/\r/", '', $html);
        $html = preg_replace("/\n/", '', $html);
        $html = preg_replace("/ /", '', $html);
        return $html;
    }

}
