<?php

namespace Be\App\System\Controller;

use Be\Be;

class Home
{

    public function index()
    {
        Be::getResponse()->display();
    }

}
