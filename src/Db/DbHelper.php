<?php

namespace Be\Db;

use Be\Be;

/**
 * 数据库表行记录
 */
class DbHelper
{

    /**
     * 更新 表属性 TableProperty
     *
     * @param string $tableName 表名
     * @param string $dbName 库名
     * @throws \Exception
     */
    public static function updateTableProperty($tableName, $dbName = 'master')
    {
        $db = Be::getDb($dbName);

        $fields = $db->getTableFields($tableName);
        $primaryKey = $db->getTablePrimaryKey($tableName);

        $runtime = Be::getRuntime();
        foreach ($fields as &$field) {
            if (strpos($field['extra'], 'auto_increment') !== false) {
                $field['autoIncrement'] = 1;
            } else {
                $field['autoIncrement'] = 0;
            }

            if (in_array($field['type'], [
                'int', 'tinyint', 'smallint', 'mediumint', 'bigint', 'float', 'double', 'decimal'
            ])) {
                $field['isNumber'] = 1;
            } else {
                $field['isNumber'] = 0;
            }
        }
        unset($field);

        $code = '<?php' . "\n";
        $code .= 'namespace Be\\Data\\Cache\\TableProperty\\' . $dbName . ';' . "\n";
        $code .= "\n";
        $code .= 'class ' . $tableName . ' extends \\Be\\Db\\TableProperty' . "\n";
        $code .= '{' . "\n";
        $code .= '    protected $_dbName = \'' . $dbName . '\'; // 数据库名' . "\n";
        $code .= '    protected $_tableName = \'' . $tableName . '\'; // 表名' . "\n";
        $code .= '    protected $_primaryKey = ' . var_export($primaryKey, true) . '; // 主键' . "\n";
        $code .= '    protected $_fields = ' . var_export($fields, true) . '; // 字段列表' . "\n";
        $code .= '}' . "\n";
        $code .= "\n";

        $path = $runtime->getCachePath() . '/TableProperty/' . $dbName . '/' . $tableName . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        file_put_contents($path, $code, LOCK_EX);
        @chmod($path, 0755);
    }

    /**
     * 更新表 Table
     *
     * @param string $tableName 表名
     * @param string $dbName 库名
     * @throws \Exception
     */
    public static function updateTable($tableName, $dbName = 'master')
    {
        $tableProperty = Be::getTableProperty($tableName, $dbName);

        $runtime = Be::getRuntime();
        $code = '<?php' . "\n";
        $code .= 'namespace Be\\Data\\Cache\\Table\\' . $dbName . ';' . "\n";
        $code .= "\n";
        $code .= 'class ' . $tableName . ' extends \\Be\\Db\\Table' . "\n";
        $code .= '{' . "\n";
        $code .= '    protected $_dbName = \'' . $dbName . '\'; // 数据库名' . "\n";
        $code .= '    protected $_tableName = \'' . $tableName . '\'; // 表名' . "\n";
        $code .= '    protected $_primaryKey = ' . var_export($tableProperty->getPrimaryKey(), true) . '; // 主键' . "\n";
        $code .= '    protected $_fields = [\'' . implode('\',\'', array_column($tableProperty->getFields(), 'name')) . '\']; // 字段列表' . "\n";
        $code .= '}' . "\n";
        $code .= "\n";

        $path = $runtime->getCachePath() . '/Table/' . $dbName . '/' . $tableName . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        file_put_contents($path, $code, LOCK_EX);
        @chmod($path, 0755);
    }

    /**
     * 更新 行记灵对象 Tuple
     *
     * @param string $tableName 表名
     * @param string $dbName 库名
     * @throws \Exception
     */
    public static function updateTuple($tableName, $dbName = 'master')
    {
        $tableProperty = Be::getTableProperty($tableName, $dbName);
        $fields = $tableProperty->getFields();

        $runtime = Be::getRuntime();
        $code = '<?php' . "\n";
        $code .= 'namespace Be\\Data\\Cache\\Tuple\\' . $dbName . ';' . "\n";
        $code .= "\n";
        $code .= 'class ' . $tableName . ' extends \\Be\\Db\\Tuple' . "\n";
        $code .= '{' . "\n";
        $code .= '    protected $_dbName = \'' . $dbName . '\'; // 数据库名' . "\n";
        $code .= '    protected $_tableName = \'' . $tableName . '\'; // 表名' . "\n";
        $code .= '    protected $_primaryKey = ' . var_export($tableProperty->getPrimaryKey(), true) . '; // 主键' . "\n";

        foreach ($fields as $field) {
            if ($field['isNumber']) {
                $code .= '    public $' . $field['name'] . ' = ' . $field['default'] . ';';
            } else {
                $code .= '    public $' . $field['name'] . ' = ' . ($field['default'] === null ? 'null' : ('\'' . $field['default'] . '\'')) . ';';
            }

            if ($field['comment']) $code .= ' // ' . $field['comment'];
            $code .= "\n";
        }

        $code .= '}' . "\n";
        $code .= "\n";

        $path = $runtime->getCachePath() . '/Tuple/' . $dbName . '/' . $tableName . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        file_put_contents($path, $code, LOCK_EX);
        @chmod($path, 0755);
    }

}


