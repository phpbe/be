<?php
namespace Be\Theme\System\Config;

class Page
{

    public int $north = 1;

    public int $middle = 0;

    public int $west = 25;

    public int $center = 50;

    public int $east = 25;

    public int $south = 1;

    public array $northSections = [
        [
            'name' => 'Theme.System.Header',
            'config' => [
                'enable' => 1,
                'logoType' => 'image',
                'logoImage' => 'https://cdn.phpbe.com/images/logo/be.png',
                'logoImageMaxWidth' => 240,
                'logoImageMaxHeight' => 64,
                'backgroundColor' => '#fff',
            ],
        ],
    ];

    public array $middleSections = [
        [
            'name' => 'be-page-title',
        ],
        [
            'name' => 'be-page-content',
        ],
    ];

    public array $westSections = [

    ];

    public array $centerSections = [
        [
            'name' => 'be-page-title',
        ],
        [
            'name' => 'be-page-content',
        ],
    ];

    public array $eastSections = [

    ];

    public array $southSections = [
        [
            'name' => 'Theme.System.Footer',
        ],
    ];

}
