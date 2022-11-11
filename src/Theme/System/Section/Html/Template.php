<?php
namespace Be\Theme\System\Section\Html;

use Be\Theme\Section;

class Template extends Section
{
    public array $positions = ['*'];

    public function display()
    {
        if ($this->position === 'middle' && $this->config->width === 'default') {
            echo '<div class="be-container">';
        }

        if ($this->config->enable) {
            echo $this->config->content;
        }

        if ($this->position === 'middle' && $this->config->width === 'default') {
            echo '</div>';
        }
    }
}

