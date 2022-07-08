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

            echo '<div id="rich-text-' . $this->id . '">';
            echo $this->config->content;
            echo '</div>';
        }
    }
}

