<?php

namespace Be\App\System\Controller;

use Be\Be;


class Index
{

    /**
     * @BeRoute("home")
     */
    public function index()
    {
        Be::getResponse()->display();
    }

}
