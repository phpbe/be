<?php
namespace Be\Theme\System\Section\Js;

use Be\Theme\Section;

class Template extends Section
{
    public array $positions = ['*'];

    public function display()
    {
        if ($this->config->enable) {
            echo '<script>';
            echo $this->config->content;
            echo '</script>';
        }
    }
}

