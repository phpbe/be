<?php

namespace Be\Db;

use Be\Be;
use Be\Config\Annotation\BeConfigItem;
use Be\Util\Annotation;

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
                'int', 'tinyint', 'smallint', 'mediumint', 'bigint', 'float', 'double'
            ])) {
                $field['isNumber'] = 1;
            } else {
                $field['isNumber'] = 0;
            }
        }
        unset($field);

        $code = '<?php' . "\n";
        $code .= 'namespace Be\\Data\\Runtime\\TableProperty\\' . $dbName . ';' . "\n";
        $code .= "\n";
        $code .= 'class ' . $tableName . ' extends \\Be\\Db\\TableProperty' . "\n";
        $code .= '{' . "\n";
        $code .= '    protected string $_dbName = \'' . $dbName . '\'; // 数据库名' . "\n";
        $code .= '    protected string $_tableName = \'' . $tableName . '\'; // 表名' . "\n";
        $code .= '    protected $_primaryKey = ' . var_export($primaryKey, true) . '; // 主键' . "\n";
        $code .= '    protected array $_fields = ' . var_export($fields, true) . '; // 字段列表' . "\n";
        $code .= '}' . "\n";
        $code .= "\n";

        $path = $runtime->getRootPath() . '/data/Runtime/TableProperty/' . $dbName . '/' . $tableName . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }
        file_put_contents($path, $code, LOCK_EX);
        chmod($path, 0777);
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
        $code .= 'namespace Be\\Data\\Runtime\\Table\\' . $dbName . ';' . "\n";
        $code .= "\n";
        $code .= 'class ' . $tableName . ' extends \\Be\\Db\\Table' . "\n";
        $code .= '{' . "\n";
        $code .= '    protected string $_dbName = \'' . $dbName . '\'; // 数据库名' . "\n";
        $code .= '    protected string $_tableName = \'' . $tableName . '\'; // 表名' . "\n";
        $code .= '    protected $_primaryKey = ' . var_export($tableProperty->getPrimaryKey(), true) . '; // 主键' . "\n";
        $code .= '    protected array $_fields = [\'' . implode('\',\'', array_column($tableProperty->getFields(), 'name')) . '\']; // 字段列表' . "\n";
        $code .= '}' . "\n";
        $code .= "\n";

        $path = $runtime->getRootPath() . '/data/Runtime/Table/' . $dbName . '/' . $tableName . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }
        file_put_contents($path, $code, LOCK_EX);
        chmod($path, 0777);
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
        $code .= 'namespace Be\\Data\\Runtime\\Tuple\\' . $dbName . ';' . "\n";
        $code .= "\n";
        $code .= 'class ' . $tableName . ' extends \\Be\\Db\\Tuple' . "\n";
        $code .= '{' . "\n";
        $code .= '    protected string $_dbName = \'' . $dbName . '\'; // 数据库名' . "\n";
        $code .= '    protected string $_tableName = \'' . $tableName . '\'; // 表名' . "\n";
        $code .= '    protected $_primaryKey = ' . var_export($tableProperty->getPrimaryKey(), true) . '; // 主键' . "\n";

        foreach ($fields as $field) {
            if ($field['default'] === null) {
                $code .= '    protected $' . $field['name'] . ' = null;';
            } else {
                if ($field['isNumber']) {
                    $code .= '    protected $' . $field['name'] . ' = ' . $field['default'] . ';';
                } else {
                    $code .= '    protected $' . $field['name'] . ' = \'' . $field['default'] . '\';';
                }
            }

            if ($field['comment']) $code .= ' // ' . $field['comment'];
            $code .= "\n";
        }

        $code .= '}' . "\n";
        $code .= "\n";

        $path = $runtime->getRootPath() . '/data/Runtime/Tuple/' . $dbName . '/' . $tableName . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }
        file_put_contents($path, $code, LOCK_EX);
        chmod($path, 0777);
    }

    /**
     * 获取 DB 配置键值对
     * @return array
     */
    public static function getConfigKeyValues(): array
    {
        $keyValues = [];
        $className = '\\Be\\App\\System\\Config\\Db';
        if (class_exists($className)) {
            $originalConfigInstance = new $className();
            $reflection = new \ReflectionClass($className);
            $properties = $reflection->getProperties(\ReflectionMethod::IS_PUBLIC);
            foreach ($properties as $property) {
                $itemName = $property->getName();
                $itemComment = $property->getDocComment();
                $parseItemComments = Annotation::parse($itemComment);

                if (isset($parseItemComments['BeConfigItem'][0])) {
                    $annotation = new BeConfigItem($parseItemComments['BeConfigItem'][0]);
                    $configItem = $annotation->toArray();
                    if (isset($configItem['value'])) {
                        $keyValues[$itemName] = $configItem['value'];
                    }
                } else {
                    $fn = '_' . $itemName;
                    if (is_callable([$originalConfigInstance, $fn])) {
                        $configItem = $originalConfigInstance->$fn($itemName);
                        if (isset($configItem['label'])) {
                            $keyValues[$itemName] = $configItem['label'];
                        }
                    }
                }
            }
        }

        $config = Be::getConfig('App.System.Db');
        $arrConfig = get_object_vars($config);
        foreach ($arrConfig as $k => $v) {
            if (!isset($keyValues[$k])) {
                $keyValues[$k] = $k;
            }
        }

        return $keyValues;
    }

}

