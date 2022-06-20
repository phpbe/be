<?php

namespace Be\AdminTheme\Installer;


class Property extends \Be\AdminTheme\Property
{

    public string $label = '安装器主题';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

