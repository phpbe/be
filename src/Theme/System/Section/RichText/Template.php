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
            echo $this->getCssMargin('rich-text');
            echo '</style>';

            echo '<div class="rich-text">';
            if ($this->position === 'middle' && $this->config->width === 'default') {
                echo '<div class="be-container">';
            }
            echo $this->page->tag0('be-section-content');
            echo $this->config->content;
            echo $this->page->tag1('be-section-content');

            if ($this->position === 'middle' && $this->config->width === 'default') {
                echo '</div>';
            }
            echo '</div>';
        }
    }
}

