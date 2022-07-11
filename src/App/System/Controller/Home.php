<?php

namespace Be\App\System\Controller;

use Be\App\ControllerException;
use Be\Be;

class Home
{

    /**
     * 首页
     *
     * @BeMenu("系统首页")
     */
    public function index()
    {
        Be::getResponse()->display();
    }


}
