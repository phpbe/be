<?php
namespace Be\App\System\Config;


class AdminTheme
{

    // 排除的
    public $exclude = ['Blank', 'Installer'];

    // 已发现可用主题
    public $available = ['System'];

    // 默认主题
    public $default = 'System';

}
