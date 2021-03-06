<?php

namespace Be\App\System\Service\Admin;

use Be\Be;
use Be\Util\Str\CaseConverter;

class Db
{

    /**
     * 获取指定应用下的相关表
     *
     * @param string $app 应用
     * @param string $dbName 库名
     * @return array
     * @throws \Exception
     */
    public function getTables($app, $dbName = 'master')
    {
        $tables = [];
        $prefix = CaseConverter::camel2Underline($app) . '_';
        $db = Be::getDb($dbName);
        $tableNames = $db->getValues('SHOW TABLES LIKE \'' . $prefix . '%\'');
        if ($tableNames) {
            foreach ($tableNames as $tableName) {
                $tables[] = Be::getTable($tableName, $dbName);
            }
        }
        return $tables;
    }

}
