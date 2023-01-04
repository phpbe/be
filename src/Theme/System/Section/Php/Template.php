<?php
namespace Be\Theme\System\Section\Php;

use Be\Theme\Section;

class Template extends Section
{
    public array $positions = ['*'];

    public function display()
    {
        if ($this->config->enable) {
            eval($this->config->content);
        }
    }
}

