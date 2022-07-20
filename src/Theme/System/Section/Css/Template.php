<?php
namespace Be\Theme\System\Section\Css;

use Be\Theme\Section;

class Template extends Section
{
    public array $positions = ['*'];

    public function display()
    {
        if ($this->config->enable) {
            echo '<style type="text/css">';
            echo $this->config->content;
            echo '</style>';
        }
    }
}

