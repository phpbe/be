<?php

namespace Be\Theme\Blank;


class Property extends \Be\Theme\Property
{

    public string $label = '空白主题';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

