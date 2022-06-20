<?php

namespace Be\AdminPlugin\Toolbar;


class Property extends \Be\AdminPlugin\Property
{

    protected string $label = '工具栏';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

