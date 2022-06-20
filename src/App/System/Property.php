<?php

namespace Be\App\System;


class Property extends \Be\App\Property
{

    protected string $label = '系统';
    protected string $icon = 'el-icon-setting';
    protected string $description = '系统基本管理功能';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}
