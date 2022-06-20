<?php

namespace Be\AdminPlugin\Task;


class Property extends \Be\AdminPlugin\Property
{

    protected string $label = '计划任务';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}

