<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("计划任务")
 */
class Task
{

    /**
     * @BeConfigItem("密钥",
     *     driver="FormItemInput",
     *     description="普通PHP环境下，计划任务需要第三方触发（如 Linux 的 crontab, Windows 的计划任务），Token用来阻止非法访问")
     */
    public $password = '';


}
