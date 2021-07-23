<?php

namespace Be\AdminPlugin\Exporter;


class Property extends \Be\AdminPlugin\Property
{

    protected $label = '导出器';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

