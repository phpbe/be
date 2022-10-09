<?php
namespace Be\AdminTheme\System\Config;

class Page
{

    public int $north = 1;
    public int $middle = 0;
    public int $west = 20;
    public int $center = 80;
    public int $east = 0;
    public int $south = 0;

    public array $centerSections = [
        [
            'name' => 'AdminTheme.System.PageTitle',
        ],
        [
            'name' => 'AdminTheme.System.PageContent',
        ],
    ];

}
