<?php

namespace Be\App\System\Controller;

use Be\Be;
use Be\Util\Str\Pinyin;

class Index
{

    public function index()
    {
        $str = 'aa中国人rrr ss  e a 中 国';
        echo $str;
        echo '<br/>';
        //$str = str_replace(' ', '-', $str);
        echo Pinyin::convert($str, '-', false);
        exit;
        Be::getResponse()->display();
    }

}
