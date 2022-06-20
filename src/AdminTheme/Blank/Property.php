<?php

namespace Be\AdminTheme\Blank;


class Property extends \Be\AdminTheme\Property
{

    public string $label = '空白主题';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

