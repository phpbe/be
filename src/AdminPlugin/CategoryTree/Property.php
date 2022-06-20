<?php

namespace Be\AdminPlugin\CategoryTree;


class Property extends \Be\AdminPlugin\Property
{

    protected string $label = '分类树';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

