<?php

namespace Be\AdminPlugin\MenuPicker;


class Property extends \Be\AdminPlugin\Property
{

    protected string $label = '菜单选择器';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

