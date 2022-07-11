<?php

namespace Be\App\System\Controller\Admin;

use Be\Be;

class Preview
{
    /**
     * 用作配置页
     */
    public function page()
    {
        Be::getResponse()->display();
    }

}
