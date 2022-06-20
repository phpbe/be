<?php

namespace Be\AdminPlugin\Config;


class Property extends \Be\AdminPlugin\Property
{

    protected string $label = '配置';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

