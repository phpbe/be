<?php

namespace Be\AdminPlugin\Curd;


class Property extends \Be\AdminPlugin\Property
{

    protected string $label = '增删改查';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

