<?php

namespace Be\AdminPlugin\Importer;


class Property extends \Be\AdminPlugin\Property
{

    protected string $label = '导入器';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

