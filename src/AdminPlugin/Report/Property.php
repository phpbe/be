<?php

namespace Be\AdminPlugin\Report;


class Property extends \Be\AdminPlugin\Property
{

    protected string $label = '报表';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

