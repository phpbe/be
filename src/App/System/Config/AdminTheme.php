<?php
namespace Be\App\System\Config;


class AdminTheme
{

    // 排除的
    public $exclude = ['Blank', 'Installer', 'Nude'];

    // 已发现可用主题
    public $available = ['Admin', 'Blank'];

    // 可用的
    public $enable = ['Admin', 'Blank'];

}
