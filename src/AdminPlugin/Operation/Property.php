<?php

namespace Be\AdminPlugin\Operation;


class Property extends \Be\AdminPlugin\Property
{

    protected string $label = '操作';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

