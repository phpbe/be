<?php

namespace Be\AdminPlugin\Tab;


class Property extends \Be\AdminPlugin\Property
{

    protected string $label = '选项卡';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

