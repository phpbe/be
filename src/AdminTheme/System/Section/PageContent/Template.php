<?php

namespace Be\AdminTheme\System\Section\PageContent;

use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['middle', 'center'];

    public function before()
    {
        if ($this->config->enable) {
            echo '<div class="page-content">';
        }
    }

    public function after()
    {
        if ($this->config->enable) {
            echo '</div>';
        }
    }
}

