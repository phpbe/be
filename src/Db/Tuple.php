<?php

namespace Be\Db;

use Be\Be;

/**
 * 数据库表行记录
 *
 */
abstract class Tuple
{
    /**
     * 默认查询的数据库
     *
     * @var string
     */
    protected string $_dbName = 'master';

    /**
     * 表全名
     *
     * @var string
     */
    protected string $_tableName = '';

    /**
     * 主键
     *
     * @var null | string | array
     */
    protected $_primaryKey = null;

    /**
     * 是否已加载数据
     *
     * @var bool
     */
    protected bool $_loaded = false;

    /**
     * 原始数据
     *
     * @var array
     */
    protected array $_init = [];

    /**
     * 是否有改变
     *
     * @var bool
     */
    protected bool $_changed = false;


    /**
     * 绑定数据
     * 可绑定 GET, POST, 或者一个数组, 对象
     *
     * @param array | object $data 要绑定的数据数组或对象
     * @return Tuple
     * @throws TupleException
     */
    public function bind($data): Tuple
    {
        if (!is_object($data) && !is_array($data)) {
            // 数据格式须为对象或数组
            throw new TupleException('Tuple:bind - Bind data should be object or array!');
        }

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        $tableProperty = Be::getTableProperty($this->_tableName, $this->_dbName);
        $fields = $tableProperty->getFields();
        $properties = $this->toArray();
        foreach ($properties as $fieldName => $value) {
            $field = $fields[$fieldName];
            if (isset($data[$fieldName])) {
                $value = $data[$fieldName];
                if (in_array($field['type'], ['int', 'tinyint', 'smallint', 'mediumint', 'bigint'])) {
                    $this->$fieldName = (int)$value;
                } elseif (in_array($field['type'], ['float', 'double'])) {
                    $this->$fieldName = (float)$value;
                } else {
                    $this->$fieldName = $value;
                }
            }
        }

        return $this;
    }

    /**
     * 按主锓加载记录
     *
     * @param string | array $primaryKeyValue 主锓的值，当为数组时格式为键值对
     * @return Tuple
     * @throws TupleException
     */
    public function load($primaryKeyValue): Tuple
    {
        if ($this->_primaryKey === null) {
            // 表 $this->_tableName 无主键，不支持按主键载入数据
            throw new TupleException('Tuple:load - Table ' . $this->_tableName . ' has no primary key!');
        }

        $db = Be::getDb($this->_dbName);

        $data = null;
        if (is_array($primaryKeyValue)) {
            if (!is_array($this->_primaryKey)) {
                // 表 $this->_tableName 非复合主键，不支持按复合主键载入数据
                throw new TupleException('Tuple:load - Table ' . $this->_tableName . ' has no union primary keys, not support load by array!');
            }

            $keys = [];
            $values = [];
            foreach ($this->_primaryKey as $primaryKey) {
                $keys[] = $db->quoteKey($primaryKey) . '=?';

                if (!isset($primaryKeyValue[$primaryKey])) {
                    //  表 $this->_tableName 按复合主键载入数据时未指定主键 $primaryKey 的值
                    throw new TupleException('Tuple:load - Table ' . $this->_tableName . ' load by union primary keys missing value of "' . $primaryKey . '"!');
                }

                $values[] = $primaryKeyValue[$primaryKey];
            }

            $sql = 'SELECT * FROM ' . $db->quoteKey($this->_tableName) . ' WHERE ' . implode(' AND ', $keys);
            $data = $db->getArray($sql, $values);

        } else {
            if (is_array($this->_primaryKey)) {
                // 表 $this->_tableName 是复合主键，不支持章个主键载入数据
                throw new TupleException('Tuple:load - Table ' . $this->_tableName . ' has union primary keys, not support load by simple primary key!');
            }

            $sql = 'SELECT * FROM ' . $db->quoteKey($this->_tableName) . ' WHERE ' . $db->quoteKey($this->_primaryKey) . '=?';
            $data = $db->getArray($sql, [$primaryKeyValue]);
        }

        if (!$data) {
            if (is_array($primaryKeyValue)) {
                // 主键编号（ implode(',', $this->_primaryKey)  ）为  implode(',', $primaryKeyValue)  的记录不存在
                throw new TupleException('Tuple:load - The record of primary key (' . implode(',', $this->_primaryKey) . '), values ' . implode(',', $primaryKeyValue) . ' does not exist!');
            } else {
                // 主键编号（$this->_primaryKey）为  $primaryKeyValue 的记录不存在
                throw new TupleException('Tuple:load - The record of primary key (' . $this->_primaryKey . '), value ' . $primaryKeyValue . ' does not exist!');
            }
        }

        $this->_init = $data;
        $this->_loaded = true;

        return $this->bind($data);
    }

    /**
     * 按条件加载记录
     * 当 $value === null 时， $field 必须为键值对数据，按指定的键值对加载，
     *
     * @param string | array $field 要加载数据的键名或銉值对
     * @param string $value 要加载的键的值
     * @return Tuple
     * @throws TupleException
     */
    public function loadBy($field, $value = null): Tuple
    {
        $db = Be::getDb($this->_dbName);

        $data = null;
        if ($value === null) {
            if (is_array($field)) {
                $keys = [];
                $values = [];
                foreach ($field as $key => $val) {
                    $keys[] = $db->quoteKey($key) . '=?';
                    $values[] = $val;
                }
                $sql = 'SELECT * FROM ' . $db->quoteKey($this->_tableName) . ' WHERE ' . implode(' AND ', $keys);
                $data = $db->getArray($sql, $values);
            } else {
                $sql = 'SELECT * FROM ' . $db->quoteKey($this->_tableName) . ' WHERE ' . $field;
                $data = $db->getArray($sql);
            }
        } else {
            if (is_array($field)) {
                // 方法参数错误
                throw new TupleException('Tuple:loadBy - parameter error!');
            }
            $sql = 'SELECT * FROM ' . $db->quoteKey($this->_tableName) . ' WHERE ' . $db->quoteKey($field) . '=?';
            $data = $db->getArray($sql, [$value]);
        }

        if (!$data) {
            // 未找到指定数据记录
            throw new TupleException('Tuple:loadBy - no record found!');
        }

        $this->_init = $data;
        $this->_loaded = true;

        return $this->bind($data);
    }

    /**
     * 插入数据到数据库
     *
     * @return Tuple | mixed
     */
    public function insert(): Tuple
    {
        $db = Be::getDb($this->_dbName);

        // UUID主键如果用户未眶值，默认值为 'uuid()'，自动生成 UUID
        if (is_array($this->_primaryKey)) {
            foreach ($this->_primaryKey as $primaryKey) {
                if (strtolower($this->$primaryKey) === 'uuid()') {
                    $this->$primaryKey = $db->quickUuid();
                }
            }
        } else {
            $primaryKey = $this->_primaryKey;
            if (strtolower($this->$primaryKey) === 'uuid()') {
                $this->$primaryKey = $db->quickUuid();
            }
        }

        $tableProperty = Be::getTableProperty($this->_tableName, $this->_dbName);
        $fields = $tableProperty->getFields();
        foreach ($fields as $field) {
            if ($field['type'] === 'timestamp' || $field['type'] === 'datetime') {
                $fieldName = $field['name'];
                if ($this->$fieldName === 'CURRENT_TIMESTAMP') {
                    $this->$fieldName = date('Y-m-d H:i:s');
                }
            }
        }

        if (is_array($this->_primaryKey)) {
            $db->insert($this->_tableName, $this->toArray());
            foreach ($this->_primaryKey as $primaryKey) {
                $field = $fields[$primaryKey];
                if (isset($field['autoIncrement']) && $field['autoIncrement']) {
                    $this->$primaryKey = $db->getLastInsertId();
                    break;
                }
            }
        } else {
            $primaryKey = $this->_primaryKey;
            $db->insert($this->_tableName, $this->toArray());
            $field = $fields[$primaryKey];
            if (isset($field['autoIncrement']) && $field['autoIncrement']) {
                $this->$primaryKey = $db->getLastInsertId();
            }
        }

        // 更新 init 数据
        $this->changed();

        return $this;
    }

    /**
     * 更新数据到数据库
     *
     * @return Tuple
     * @throws TupleException
     */
    public function update(): Tuple
    {
        if ($this->_primaryKey === null) {
            // 表 $this->_tableName 无主键, 不支持按主键更新
            throw new TupleException('Tuple:update - Table ' . $this->_tableName . ' has no primary key!');
        }

        if (is_array($this->_primaryKey)) {
            foreach ($this->_primaryKey as $primaryKey) {
                if (!$this->$primaryKey) {
                    // 表 $this->_tableName 主键 $primaryKey 未指定值, 不支持按主键更新
                    throw new TupleException('Tuple:update - Table  ' . $this->_tableName . ' missing value of "' . $primaryKey . '"!');
                }
            }
        } else {
            $primaryKey = $this->_primaryKey;
            if (!$this->$primaryKey) {
                // 表 $this->_tableName 主键 $primaryKey 未指定值, 不支持按主键更新
                throw new TupleException('Tuple:update - Table  ' . $this->_tableName . ' missing value of "' . $primaryKey . '"!');
            }
        }

        if ($this->_changed) {
            $changedFields = $this->getChanges();
            if (count($changedFields) > 0) {
                if (is_array($this->_primaryKey)) {
                    foreach ($this->_primaryKey as $primaryKey) {
                        $changedFields[$primaryKey] = $this->$primaryKey;
                    }
                } else {
                    $primaryKey = $this->_primaryKey;
                    $changedFields[$primaryKey] = $this->$primaryKey;
                }

                Be::getDb($this->_dbName)->update($this->_tableName, $changedFields, $this->_primaryKey);

                // 更新 init 数据
                $this->changed();
            }
        }

        return $this;
    }

    /**
     * 保存数据到数据库
     * 跟据主键是否有值自动识别插入或更新
     *
     * @return Tuple
     */
    public function save(): Tuple
    {
        $db = Be::getDb($this->_dbName);

        $insert = false;
        if ($this->_primaryKey === null) {
            $insert = true;
        } else {
            if (is_array($this->_primaryKey)) {
                foreach ($this->_primaryKey as $primaryKey) {
                    if (!$this->$primaryKey) {
                        $insert = true;
                        break;
                    }

                    if (strtolower($this->$primaryKey) === 'uuid()') {
                        $this->$primaryKey = $db->quickUuid();
                        $insert = true;
                        break;
                    }
                }
            } else {
                $primaryKey = $this->_primaryKey;
                if ($this->$primaryKey) {
                    if (strtolower($this->$primaryKey) === 'uuid()') {
                        $this->$primaryKey = $db->quickUuid();
                        $insert = true;
                    }
                } else {
                    $insert = true;
                }
            }
        }

        if ($insert) {

            $tableProperty = Be::getTableProperty($this->_tableName, $this->_dbName);
            $fields = $tableProperty->getFields();
            foreach ($fields as $field) {
                if ($field['type'] === 'timestamp' || $field['type'] === 'datetime') {
                    $fieldName = $field['name'];
                    if ($this->$fieldName === 'CURRENT_TIMESTAMP') {
                        $this->$fieldName = date('Y-m-d H:i:s');
                    }
                }
            }

            $db->insert($this->_tableName, $this->toArray());

            if ($this->_primaryKey !== null) {
                if (is_array($this->_primaryKey)) {
                    foreach ($this->_primaryKey as $primaryKey) {
                        $field = $tableProperty->getField($primaryKey);
                        if (isset($field['autoIncrement']) && $field['autoIncrement']) {
                            $this->$primaryKey = $db->getLastInsertId();
                            break;
                        }
                    }
                } else {
                    $primaryKey = $this->_primaryKey;
                    $field = $tableProperty->getField($primaryKey);
                    if (isset($field['autoIncrement']) && $field['autoIncrement']) {
                        $this->$primaryKey = $db->getLastInsertId();
                    }
                }
            }
        } else {
            if ($this->_changed) {
                $changedFields = $this->getChanges();
                if (count($changedFields) > 0) {
                    if (is_array($this->_primaryKey)) {
                        foreach ($this->_primaryKey as $primaryKey) {
                            $changedFields[$primaryKey] = $this->$primaryKey;
                        }
                    } else {
                        $primaryKey = $this->_primaryKey;
                        $changedFields[$primaryKey] = $this->$primaryKey;
                    }

                    $db->update($this->_tableName, $changedFields, $this->_primaryKey);

                    // 更新 init 数据
                    $this->changed();
                }
            }
        }

        return $this;
    }

    /**
     * 是否已加载
     *
     * @return bool
     */
    public function isLoaded(): bool
    {
        return $this->_loaded;
    }

    /**
     * 是否有改动
     *
     * @return bool
     */
    public function hasChange(): bool
    {
        return $this->_changed;
    }

    /**
     * 获取改动项
     *
     * @return array
     */
    public function getChanges(): array
    {
        $changedFields = [];
        $tableProperty = Be::getTableProperty($this->_tableName, $this->_dbName);
        $fields = $tableProperty->getFields();
        $properties = $this->toArray();
        foreach ($properties as $fieldName => $value) {
            $field = $fields[$fieldName];
            $initValue = $this->_init[$fieldName] ?? null;
            if (in_array($field['type'], ['int', 'tinyint', 'smallint', 'mediumint', 'bigint'])) {
                if ($initValue !== null) {
                    $initValue = (int)$initValue;
                }

                if ($value !== null) {
                    $value = (int)$value;
                }
            } elseif (in_array($field['type'], ['float', 'double'])) {
                if ($initValue !== null) {
                    $initValue = (float)$initValue;
                }

                if ($value !== null) {
                    $value = (float)$value;
                }
            }

            // 有更新
            if ($initValue !== $value) {
                $changedFields[$fieldName] = $value;
            }
        }

        return $changedFields;
    }

    /**
     * 获取改动项明细
     *
     * @return array
     */
    public function getChangeDetails(): array
    {
        $changedFields = [];
        $tableProperty = Be::getTableProperty($this->_tableName, $this->_dbName);
        $fields = $tableProperty->getFields();
        $properties = $this->toArray();
        foreach ($properties as $fieldName => $value) {
            $field = $fields[$fieldName];
            $initValue = $this->_init[$fieldName] ?? null;
            if (in_array($field['type'], ['int', 'tinyint', 'smallint', 'mediumint', 'bigint'])) {
                if ($initValue !== null) {
                    $initValue = (int)$initValue;
                }

                if ($value !== null) {
                    $value = (int)$value;
                }
            } elseif (in_array($field['type'], ['float', 'double'])) {
                if ($initValue !== null) {
                    $initValue = (float)$initValue;
                }

                if ($value !== null) {
                    $value = (float)$value;
                }
            }

            // 有更新
            if ($initValue !== $value) {
                $changedFields[$fieldName] = ['from' => $initValue, 'to' => $value];
            }
        }

        return $changedFields;
    }

    /**
     * 是否有改动
     */
    protected function changed()
    {
        $tableProperty = Be::getTableProperty($this->_tableName, $this->_dbName);
        $fields = $tableProperty->getFields();
        $properties = $this->toArray();
        foreach ($properties as $fieldName => $value) {
            $field = $fields[$fieldName];
            if ($value !== null) {
                if (in_array($field['type'], ['int', 'tinyint', 'smallint', 'mediumint', 'bigint'])) {
                    $value = (int)$value;
                } elseif (in_array($field['type'], ['float', 'double'])) {
                    $value = (float)$value;
                }
            }
            $this->_init[$fieldName] = $value;
        }

        $this->_changed = false;
    }

    /**
     * 删除指定主键值的记录
     *
     * @param int | string | array | null $primaryKeyValue 主键值
     * @return Tuple
     * @throws TupleException
     */
    public function delete($primaryKeyValue = null): Tuple
    {
        if ($this->_primaryKey === null) {
            // 表 $this->_tableName 无主键, 不支持按主键删除
            throw new TupleException('Tuple:delete - Table ' . $this->_tableName . ' has no primary key!');
        }

        if ($primaryKeyValue === null) {
            if ($this->_primaryKey === null) {
                // 参数缺失, 请指定要删除记录的编号
                throw new TupleException('Tuple:delete - ' . $this->_tableName . ' primary key missing!');
            } elseif (is_array($this->_primaryKey)) {
                $primaryKeyValue = [];
                foreach ($this->_primaryKey as $primaryKey) {
                    $primaryKeyValue[$primaryKey] = $this->$primaryKey;
                }
            } else {
                $primaryKey = $this->_primaryKey;
                $primaryKeyValue = $this->$primaryKey;
            }
        } else {
            if (is_array($this->_primaryKey)) {
                foreach ($this->_primaryKey as $primaryKey) {
                    if (!isset($primaryKeyValue[$primaryKey])) {
                        // 表 $this->_tableName 按复合主键删除时未指定主键 $primaryKey 的值
                        throw new TupleException('Tuple:delete - ' . $this->_tableName . ' union primary key missing value of ' . $primaryKey . '!');
                    }
                }
            }
        }

        $db = Be::getDb($this->_dbName);
        if (is_array($primaryKeyValue)) {
            $keys = [];
            $values = [];
            foreach ($primaryKeyValue as $key => $value) {
                $keys[] = $db->quoteKey($key) . '=?';
                $values[] = $value;
            }
            $db->query('DELETE FROM ' . $db->quoteKey($this->_tableName) . ' WHERE ' . implode(' AND ', $keys), [$values]);
        } else {
            $db->query('DELETE FROM ' . $db->quoteKey($this->_tableName) . ' WHERE ' . $db->quoteKey($this->_primaryKey) . '=?', [$primaryKeyValue]);
        }
        return $this;
    }

    /**
     * 自增某个字段
     *
     * @param string $field 字段名
     * @param int $step 自增量
     * @return Tuple
     * @throws TupleException
     */
    public function increase(string $field, int $step = 1): Tuple
    {
        if ($this->_primaryKey === null) {
            // 表 $this->_tableName 无主键, 不支持字段自增
            throw new TupleException('Tuple:increment - Table ' . $this->_tableName . ' has no primary key!');
        }

        $db = Be::getDb($this->_dbName);
        if (is_array($this->_primaryKey)) {
            $keys = [];
            $values = [];
            foreach ($this->_primaryKey as $primaryKey) {
                $keys[] = $db->quoteKey($primaryKey) . '=?';
                $values[] = $this->$primaryKey;
            }
            $sql = 'UPDATE ' . $db->quoteKey($this->_tableName) . ' SET ' . $db->quoteKey($field) . '=' . $db->quoteKey($field) . '+' . $step . ' WHERE ' . implode(' AND ', $keys);
            $db->query($sql, [$values]);
        } else {
            $primaryKey = $this->_primaryKey;
            $primaryKeyValue = $this->$primaryKey;
            $sql = 'UPDATE ' . $db->quoteKey($this->_tableName) . ' SET ' . $db->quoteKey($field) . '=' . $db->quoteKey($field) . '+' . $step . ' WHERE ' . $db->quoteKey($this->_primaryKey) . '=?';
            $db->query($sql, [$primaryKeyValue]);
        }
        return $this;
    }

    /**
     * 自减某个字段
     *
     * @param string $field 字段名
     * @param int $step 自减量
     * @return Tuple
     * @throws TupleException
     */
    public function decrease(string $field, int $step = 1): Tuple
    {
        if ($this->_primaryKey === null) {
            // 表 $this->_tableName 无主键, 不支持字段自减
            throw new TupleException('Tuple:decrement - Table ' . $this->_tableName . ' has no primary key!');
        }

        $db = Be::getDb($this->_dbName);
        if (is_array($this->_primaryKey)) {
            $keys = [];
            $values = [];
            foreach ($this->_primaryKey as $primaryKey) {
                $keys[] = $db->quoteKey($primaryKey) . '=?';
                $values[] = $this->$primaryKey;
            }
            $sql = 'UPDATE ' . $db->quoteKey($this->_tableName) . ' SET ' . $db->quoteKey($field) . '=' . $db->quoteKey($field) . '-' . $step . ' WHERE ' . implode(' AND ', $keys);
            $db->query($sql, [$values]);
        } else {
            $primaryKey = $this->_primaryKey;
            $primaryKeyValue = $this->$primaryKey;
            $sql = 'UPDATE ' . $db->quoteKey($this->_tableName) . ' SET ' . $db->quoteKey($field) . '=' . $db->quoteKey($field) . '-' . $step . ' WHERE ' . $db->quoteKey($this->_primaryKey) . '=?';
            $db->query($sql, [$primaryKeyValue]);
        }
        return $this;
    }

    /**
     * 初始化
     *
     * @return Tuple
     */
    public function init(): Tuple
    {
        $tableProperty = Be::getTableProperty($this->_tableName, $this->_dbName);
        $fields = $tableProperty->getFields();
        foreach ($fields as $field) {
            $fieldName = $field['name'];
            if ($field['default'] === null) {
                $this->$fieldName = null;
            } else {
                if (in_array($field['type'], ['int', 'tinyint', 'smallint', 'mediumint', 'bigint'])) {
                    $this->$fieldName = (int)$field['default'];
                } elseif (in_array($field['type'], ['float', 'double'])) {
                    $this->$fieldName = (float)$field['default'];
                } else {
                    $this->$fieldName = $field['default'];
                }
            }
        }

        $this->_init = [];
        $this->_changed = false;

        return $this;
    }

    /**
     * 获取数据库名
     *
     * @return string
     */
    public function getDbName(): string
    {
        return $this->_dbName;
    }

    /**
     * 获取表名
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->_tableName;
    }

    /**
     * 获取主键名
     *
     * @return null | string | array
     */
    public function getPrimaryKey()
    {
        return $this->_primaryKey;
    }

    /**
     * 将行模型数据转成简单数组
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = get_object_vars($this);
        unset($array['_dbName'], $array['_tableName'], $array['_primaryKey'], $array['_loaded'], $array['_init'], $array['_changed']);

        return $array;
    }

    /**
     * 将行模型数据转成简单对象
     *
     * @return object
     */
    public function toObject(): object
    {
        return (object)$this->toArray();
    }

    /**
     * 赋值
     *
     * @param string $fieldName
     * @param $value
     */
    public function __set(string $fieldName, $value)
    {
        $tableProperty = Be::getTableProperty($this->_tableName, $this->_dbName);
        $field = $tableProperty->getField($fieldName);

        if ($field === null) return;

        $initValue = $this->_init[$fieldName] ?? null;
        if (in_array($field['type'], ['int', 'tinyint', 'smallint', 'mediumint', 'bigint'])) {
            if ($initValue !== null) {
                $initValue = (int)$initValue;
            }

            if ($value !== null) {
                $value = (int)$value;
            }
        } elseif (in_array($field['type'], ['float', 'double'])) {
            if ($initValue !== null) {
                $initValue = (float)$initValue;
            }

            if ($value !== null) {
                $value = (float)$value;
            }
        }

        $this->$fieldName = $value;

        // 有更新
        if ($initValue !== $value) {
            $this->_changed = true;
        }
    }

    /**
     * 取值
     *
     * @param string $fieldName
     * @return mixed
     */
    public function __get(string $fieldName)
    {
        return $this->$fieldName;
    }

    /**
     * 变量是否存在
     *
     * @param string $fieldName
     * @return mixed
     */
    public function __isset(string $fieldName)
    {
        return isset($this->$fieldName);
    }

    /**
     * unset 变量
     *
     * @param string $fieldName
     */
    public function __unset(string $fieldName)
    {
        unset($this->$fieldName);
    }

}


