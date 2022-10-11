<?php

namespace Be\App\System\Controller;

use Be\Be;

class Home
{

    /**
     * 首页
     *
     * @BeMenu("系统首页")
     * @BeRoute ("/system/home")
     */
    public function index()
    {
        Be::getResponse()->display();
    }


}
