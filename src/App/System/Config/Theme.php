<?php
namespace Be\App\System\Config;


class Theme
{

    // 排除的
    public array $exclude = ['Blank'];

    // 已发现可用主题
    public array $available = ['System'];

    // 默认主题
    public string $default = 'System';

}
