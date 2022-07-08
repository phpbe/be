<?php

namespace Be\App\System\Controller;

use Be\Be;

class Preview
{

    /**
     * 首页
     */
    public function index()
    {
        Be::getResponse()->display();
    }

    /**
     * 用作配置页
     */
    public function page()
    {
        Be::getResponse()->display();
    }

}
