<?php

namespace Be\App\System\Controller;

use Be\Be;

class Index
{

    public function index()
    {
        Be::getResponse()->display();
    }

}
