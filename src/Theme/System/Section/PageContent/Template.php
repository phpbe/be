<?php

namespace Be\Theme\System\Section\PageContent;

use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['middle', 'center'];

    public function before()
    {
        if ($this->config->enable) {
            echo '<style type="text/css">';

            echo $this->getCssBackgroundColor('page-content');
            echo $this->getCssPadding('page-content');
            echo $this->getCssMargin('page-content');

            echo '#' . $this->id . ' .page-content img {';
            echo 'max-width: 100%;';
            echo '}';
            
            echo '</style>';

            echo '<div class="page-content">';

            if ($this->position === 'middle' && $this->config->width === 'default') {
                echo '<div class="be-container">';
            }
        }
    }

    public function after()
    {
        if ($this->config->enable) {
            if ($this->position === 'middle' && $this->config->width === 'default') {
                echo '</div>';
            }

            echo '</div>';
        }
    }
}

