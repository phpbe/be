<?php

namespace Be\AdminPlugin\Table;


class Property extends \Be\AdminPlugin\Property
{

    protected string $label = '表格';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

