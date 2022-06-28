<?php

namespace Be\Theme\System;


class Property extends \Be\Theme\Property
{

    public string  $label = '系统主题';
    public function __construct()
    {
        parent::__construct(__FILE__);
    }

}

