<?php

namespace Be\Theme\System\Section\PageTitle;

use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['middle', 'center'];

    public function before()
    {
        if ($this->config->enable) {
            echo '<style type="text/css">';
            echo $this->getCssBackgroundColor('page-title');
            echo $this->getCssPadding('page-title');
            echo $this->getCssMargin('page-title');
            echo '</style>';

            echo '<div class="page-title">';

            if ($this->position === 'middle' && $this->config->width === 'default') {
                echo '<div class="be-container">';
            }

            echo '<h1 class="be-' . $this->config->size . ' be-ta-' . $this->config->align . '">';
        }
    }

    public function after()
    {
        if ($this->config->enable) {
            echo '</h1>';

            if ($this->position === 'middle' && $this->config->width === 'default') {
                echo '</div>';
            }

            echo '</div>';
        }
    }
}

