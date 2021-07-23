<?php

namespace Be\AdminPlugin\FileManager;


class Property extends \Be\AdminPlugin\Property
{

    protected $label = '文件管理器';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

