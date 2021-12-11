<?php

namespace Be\App\System;


class Property extends \Be\App\Property
{

    protected $label = '系统';
    protected $icon = 'el-icon-setting';
    protected $description = '系统基本管理功能';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}
