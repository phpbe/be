<?php
namespace Be\Theme\System\Section\RichText;

use Be\Theme\Section;

class Template extends Section
{
    public array $positions = ['*'];

    public function display()
    {
        if ($this->config->enable) {
            echo '<style type="text/css">';
            echo $this->getCssBackgroundColor('rich-text');
            echo $this->getCssPadding('rich-text');
            echo '</style>';

            echo '<div class="rich-text">';
            echo $this->theme->tag0('be-section-content');
            echo $this->config->content;
            echo $this->theme->tag1('be-section-content');
            echo '</div>';
        }
    }
}

