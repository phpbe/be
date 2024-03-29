<?php

namespace Be\Db;


/**
 * 数据库类
 */
abstract class Driver
{

    protected ?string $name = null; // 数据库名称

    /**
     * @var Connection
     */
    protected ?Connection $connection = null; // 查询器

    protected array $cache = [];

    abstract function __construct(string $name, $pdo = null);

    /**
     * 获取数据库名称
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 获取数据库名称
     *
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * 获取数据库名称
     *
     * @return  \PDO|\Swoole\Database\PDOProxy
     */
    public function getPdo()
    {
        return $this->connection->getPdo();
    }

    /**
     * 关闭
     */
    public function close()
    {
        $this->connection->close();
    }

    /**
     * 释放
     */
    public function release()
    {
        $this->connection->release();
        $this->connection = null;
    }

    /**
     * 预编译 sql 语句
     *
     * @param string $sql 查询语句
     * @param array $options 参数
     * @return \PDOStatement|\Swoole\Database\PDOStatementProxy
     * @throws DbException | \PDOException | \Exception
     */
    public function prepare(string $sql, array $options = null)
    {
        return $this->connection->prepare($sql, $options);
    }

    /**
     * 执行 sql 语句
     *
     * @param string $sql 要执行的SQL语句
     * @param array|null $bind 占位符替换数据
     * @param array|null $prepareOptions 预编译选项
     * @return \PDOStatement|\Swoole\Database\PDOStatementProxy
     * @throws DbException
     */
    public function execute(string $sql, array $bind = null, array $prepareOptions = null)
    {
        return $this->connection->execute($sql, $bind, $prepareOptions);
    }

    /**
     * 执行 sql 语句
     *
     * @param string $sql 要执行的SQL语句
     * @param array|null $bind 占位符替换数据
     * @param array|null $prepareOptions 预编译选项
     * @return int 影响的行数
     * @throws DbException
     */
    public function query(string $sql, array $bind = null, array $prepareOptions = null): int
    {
        return $this->connection->query($sql, $bind, $prepareOptions);
    }

    /**
     * 返回单一查询结果, 多行多列记录时, 只返回第一行第一列
     *
     * @param string $sql 查询语句
     * @param array|null $bind 参数
     * @return string|bool
     * @throws DbException
     */
    public function getValue(string $sql, array $bind = null)
    {
        $statement = $this->connection->execute($sql, $bind);
        $array = $statement->fetch(\PDO::FETCH_NUM);
        $statement->closeCursor();
        if ($array === false) return false;
        return $array[0];
    }

    /**
     * 返回查询单列结果的数组
     *
     * @param string $sql 查询语句
     * @param array $bind 参数
     * @return array
     * @throws DbException
     */
    public function getValues(string $sql, array $bind = null): array
    {
        $statement = $this->connection->execute($sql, $bind);
        $values = $statement->fetchAll(\PDO::FETCH_COLUMN);
        $statement->closeCursor();
        return $values;
    }

    /**
     * 返回键值对数组
     * 查询两个或两个以上字段，第一列字段作为 key, 乘二列字段作为 value，多于两个字段时忽略
     *
     * @param string $sql 查询语句
     * @param array $bind 参数
     * @return array
     * @throws DbException
     */
    public function getKeyValues(string $sql, array $bind = null): array
    {
        $statement = $this->connection->execute($sql, $bind);
        $keyValues = $statement->fetchAll(\PDO::FETCH_UNIQUE | \PDO::FETCH_COLUMN);
        $statement->closeCursor();
        return $keyValues;
    }

    /**
     * 返回一个跌代器数组
     *
     * @param string $sql 查询语句
     * @param array $bind 参数
     * @return \Generator
     */
    abstract function getYieldValues(string $sql, array $bind = null): \Generator;

    /**
     * 返回一个数组
     *
     * @param string $sql 查询语句
     * @param array $bind 参数
     * @return array|false 数组，不存在时返回false
     * @throws DbException
     */
    public function getArray(string $sql, array $bind = null)
    {
        $statement = $this->connection->execute($sql, $bind);
        $array = $statement->fetch(\PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $array;
    }

    /**
     * 返回一个二维数组
     *
     * @param string $sql 查询语句
     * @param array $bind 参数
     * @return array
     * @throws DbException
     */
    public function getArrays(string $sql, array $bind = null): array
    {
        $statement = $this->connection->execute($sql, $bind);
        $arrays = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $statement->closeCursor();
        return $arrays;
    }

    /**
     * 返回一个带下标索引的二维数组
     *
     * @param string $sql 查询语句
     * @param null | array $bind 参数
     * @param null | string $key 作为下标索引的字段名
     * @return array
     * @throws DbException
     */
    public function getKeyArrays(string $sql, array $bind = null, string $key = null): array
    {
        $statement = $this->connection->execute($sql, $bind);
        $arrays = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $statement->closeCursor();

        if (count($arrays) === 0) return [];

        if ($key === null) {
            $key = key($arrays[0]);
        }

        $result = [];
        foreach ($arrays as $array) {
            $result[$array[$key]] = $array;
        }

        return $result;
    }

    /**
     * 返回一个跌代器二维数组
     *
     * @param string $sql 查询语句
     * @param array $bind 参数
     * @return \Generator
     */
    abstract function getYieldArrays(string $sql, array $bind = null): \Generator;

    /**
     * 返回一个数据库记录对象
     *
     * @param string $sql 查询语句
     * @param array $bind 参数
     * @return object|false  对象，不存在时返回false
     * @throws DbException
     */
    public function getObject(string $sql, array $bind = null)
    {
        $statement = $this->connection->execute($sql, $bind);
        $object = $statement->fetchObject();
        $statement->closeCursor();
        return $object;
    }

    /**
     * 返回一个对象数组
     *
     * @param string $sql 查询语句
     * @param array $bind 参数
     * @return array(object)
     * @throws DbException
     */
    public function getObjects(string $sql, array $bind = null): array
    {
        $statement = $this->connection->execute($sql, $bind);
        $objects = $statement->fetchAll(\PDO::FETCH_OBJ);
        $statement->closeCursor();
        return $objects;
    }

    /**
     * 返回一个跌代器对象数组
     *
     * @param string $sql 查询语句
     * @param array $bind 参数
     * @return \Generator
     */
    abstract function getYieldObjects(string $sql, array $bind = null): \Generator;

    /**
     * 返回一个带下标索引的对象数组
     *
     * @param string $sql 查询语句
     * @param null | array $bind 参数
     * @param null | string $key 作为下标索引的字段名
     * @return array(object)
     * @throws DbException
     */
    public function getKeyObjects(string $sql, array $bind = null, string $key = null): array
    {
        $statement = $this->connection->execute($sql, $bind);
        $objects = $statement->fetchAll(\PDO::FETCH_OBJ);
        $statement->closeCursor();

        if (count($objects) === 0) return [];

        if ($key === null) {
            $vars = get_object_vars($objects[0]);
            $key = key($vars);
        }

        $result = [];
        foreach ($objects as $object) {
            $result[$object->$key] = $object;
        }

        return $result;
    }

    /**
     * 插入一个对象或数组到数据库
     *
     * @param string $table 表名
     * @param array | object $object 要插入数据库的对象或数组，对象属性或数组键名需要和该表字段一致
     * @return int|string|array 插入的主键
     * @throws DbException
     */
    public function insert(string $table, $object)
    {
        $vars = null;
        if (is_array($object)) {
            $vars = $object;
        } elseif (is_object($object)) {
            $vars = get_object_vars($object);
        } else {
            // 插入的数据格式须为对象或数组
            throw new DbException('Db:insert - Insert data should be object or array!');
        }

        $fields = [];
        foreach (array_keys($vars) as $field) {
            $fields[] = $this->connection->quoteKey($field);
        }

        $sql = 'INSERT INTO ' . $this->connection->quoteKey($table) . '(' . implode(',', $fields) . ') VALUES(' . implode(',', array_fill(0, count($vars), '?')) . ')';
        $statement = $this->connection->execute($sql, array_values($vars));
        $statement->closeCursor();
        return $this->connection->getLastInsertId();
    }

    /**
     * 批量插入多个对象或数组到数据库
     *
     * @param string $table 表名
     * @param array $objects 要插入数据库的对象数组或二维数组，对象属性或数组键名需要和该表字段一致
     * @return array 批量插入的主键列表
     * @throws DbException
     */
    public function insertMany(string $table, array $objects): array
    {
        if (!is_array($objects) || count($objects) === 0) return [];

        $ids = [];
        reset($objects);
        $object = current($objects);
        $vars = null;
        if (is_array($object)) {
            $vars = $object;
        } elseif (is_object($object)) {
            $vars = get_object_vars($object);
        } else {
            // 批量插入的数据格式须为对象或数组
            throw new DbException('Db:insertMany - Insert data should be object or array!');
        }
        ksort($vars);

        $fields = [];
        foreach (array_keys($vars) as $field) {
            $fields[] = $this->connection->quoteKey($field);
        }

        $sql = 'INSERT INTO ' . $this->connection->quoteKey($table) . '(' . implode(',', $fields) . ') VALUES(' . implode(',', array_fill(0, count($vars), '?')) . ')';
        $statement = $this->connection->prepare($sql);
        foreach ($objects as $o) {
            $vars = null;
            if (is_array($o)) {
                $vars = $o;
            } elseif (is_object($o)) {
                $vars = get_object_vars($o);
            } else {
                // 批量插入的数据格式须为对象或数组
                throw new DbException('Db:insertMany - Insert data should be object or array!');
            }
            ksort($vars);
            $statement->execute(array_values($vars));
            $ids[] = $this->connection->getLastInsertId();
        }
        $statement->closeCursor();

        return $ids;
    }

    /**
     * 快速插入一个对象或数组到数据库
     *
     * @param string $table 表名
     * @param array | object $object 要插入数据库的对象或数组，对象属性或数组锓名需要和该表字段一致
     * @return int|string|array 插入的主键
     * @throws DbException
     */
    public function quickInsert(string $table, $object)
    {
        $effectLines = null;

        $vars = null;
        if (is_array($object)) {
            $vars = $object;
        } elseif (is_object($object)) {
            $vars = get_object_vars($object);
        } else {
            // 快速插入的数据格式须为对象或数组
            throw new DbException('Db:quickInsert - Insert data should be object or array!');
        }

        $fields = [];
        foreach (array_keys($vars) as $field) {
            $fields[] = $this->connection->quoteKey($field);
        }

        $sql = 'INSERT INTO ' . $this->connection->quoteKey($table) . '(' . implode(',', $fields) . ') VALUES';
        $values = array_values($vars);
        foreach ($values as &$value) {
            if ($value !== null) {
                $value = $this->connection->quoteValue($value);
            } else {
                $value = 'null';
            }
        }
        $sql .= '(' . implode(',', $values) . ')';
        $statement = $this->connection->execute($sql);
        $statement->closeCursor();

        return $this->connection->getLastInsertId();
    }

    /**
     * 快速批量插入多个对象或数组到数据库
     *
     * @param string $table 表名
     * @param array $objects 要插入数据库的对象数组，对象属性需要和该表字段一致
     * @return int 影响的行数
     * @throws DbException
     */
    public function quickInsertMany(string $table, array $objects): int
    {
        if (!is_array($objects) || count($objects) === 0) return 0;

        reset($objects);
        $object = current($objects);
        $vars = null;
        if (is_array($object)) {
            $vars = $object;
        } elseif (is_object($object)) {
            $vars = get_object_vars($object);
        } else {
            // 快速批量插入的数据格式须为对象或数组
            throw new DbException('Db:quickInsertMany - Insert data should be object or array!');
        }
        ksort($vars);


        $fields = [];
        foreach (array_keys($vars) as $field) {
            $fields[] = $this->connection->quoteKey($field);
        }

        $sql = 'INSERT INTO ' . $this->connection->quoteKey($table) . '(' . implode(',', $fields) . ') VALUES';
        foreach ($objects as $o) {
            $vars = null;
            if (is_array($o)) {
                $vars = $o;
            } elseif (is_object($o)) {
                $vars = get_object_vars($o);
            } else {
                // 快速批量插入的数据格式须为对象或数组
                throw new DbException('Db:quickInsertMany - Insert data should be object or array!');
            }
            ksort($vars);
            $values = array_values($vars);
            foreach ($values as &$value) {
                if ($value !== null) {
                    $value = $this->connection->quoteValue($value);
                } else {
                    $value = 'null';
                }
            }
            $sql .= '(' . implode(',', $values) . '),';
        }
        $sql = substr($sql, 0, -1);
        $statement = $this->connection->execute($sql);
        $effectLines = $statement->rowCount();
        $statement->closeCursor();

        return $effectLines;
    }

    /**
     * 更新一个对象或数组到数据库
     *
     * @param string $table 表名
     * @param array | object $object 要插入数据库的对象，对象属性需要和该表字段一致
     * @param null | string | array $primaryKey 主键或指定键名更新，未指定时自动取表的主键
     * @return int 更新的行数，如果数据无变化，则返回0
     * @throws DbException
     */
    public function update(string $table, $object, $primaryKey = null): int
    {
        $fields = [];
        $fieldValues = [];

        $where = [];
        $whereValue = [];

        if ($primaryKey === null) {
            $primaryKey = $this->getTablePrimaryKey($table);
            if ($primaryKey === null) {
                // 新数据表 $table 无主键，不支持按主键更新
                throw new DbException('Db:update - Db table (' . $table . ') no primary key, not support update with primary key!');
            }
        }

        $vars = null;
        if (is_array($object)) {
            $vars = $object;
        } elseif (is_object($object)) {
            $vars = get_object_vars($object);
        } else {
            // 更新的数据格式须为对象或数组
            throw new DbException('Db:update - Update data should be object or array!');
        }

        foreach ($vars as $key => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }

            if (is_array($primaryKey)) {

                if (in_array($key, $primaryKey)) {
                    $where[] = $this->connection->quoteKey($key) . '=?';
                    $whereValue[] = $value;
                    continue;
                }

            } else {

                // 主键不更新
                if ($key === $primaryKey) {
                    $where[] = $this->connection->quoteKey($key) . '=?';
                    $whereValue[] = $value;
                    continue;
                }
            }

            $fields[] = $this->connection->quoteKey($key) . '=?';
            $fieldValues[] = $value;
        }

        if (!$where) {
            // 更新数据时未指定条件！
            throw new DbException('Db:update - Missing where conditions!');
        }

        $sql = 'UPDATE ' . $this->connection->quoteKey($table) . ' SET ' . implode(',', $fields) . ' WHERE ' . implode(' AND ', $where);
        $fieldValues = array_merge($fieldValues, $whereValue);

        $statement = $this->connection->execute($sql, $fieldValues);
        $effectLines = $statement->rowCount();
        $statement->closeCursor();

        return $effectLines;
    }

    /**
     * 快速更新一个对象或数组到数据库
     *
     * @param string $table 表名
     * @param array | object $object 要插入数据库的对象或数组，对象属性或数组锓名需要和该表字段一致
     * @param null | string | array $primaryKey 主键或指定键名更新，未指定时自动取表的主键
     * @return int 更新的行数，如果数据无变化，则返回0
     * @throws DbException
     */
    public function quickUpdate(string $table, $object, $primaryKey = null): int
    {
        $where = [];
        $fields = [];

        if ($primaryKey === null) {
            $primaryKey = $this->getTablePrimaryKey($table);
            if ($primaryKey === null) {
                // 新数据表 $table 无主键，不支持按主键更新
                throw new DbException('Db:quickUpdate - Db table (' . $table . ') no primary key, not support update with primary key!');
            }
        }

        $vars = null;
        if (is_array($object)) {
            $vars = $object;
        } elseif (is_object($object)) {
            $vars = get_object_vars($object);
        } else {
            // 更新的数据格式须为对象或数组
            throw new DbException('Db:quickUpdate - Update data should be object or array!');
        }

        foreach ($vars as $key => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }

            if (is_array($primaryKey)) {

                if (in_array($key, $primaryKey)) {
                    $where[] = $this->connection->quoteKey($key) . '=' . $this->connection->quoteValue($value);
                    continue;
                }

            } else {

                // 主键不更新
                if ($key === $primaryKey) {
                    $where[] = $this->connection->quoteKey($key) . '=' . $this->connection->quoteValue($value);
                    continue;
                }
            }

            if ($value === null) {
                $fields[] = $this->connection->quoteKey($key) . '=null';
            } else {
                $fields[] = $this->connection->quoteKey($key) . '=' . $this->connection->quoteValue($value);
            }
        }

        if (!$where) {
            // 更新数据时未指定条件
            throw new DbException('Db:quickUpdate - Missing where conditions!');
        }

        $sql = 'UPDATE ' . $this->connection->quoteKey($table) . ' SET ' . implode(',', $fields) . ' WHERE ' . implode(' AND ', $where);
        $statement = $this->connection->execute($sql);
        $effectLines = $statement->rowCount();
        $statement->closeCursor();

        return $effectLines;
    }

    /**
     * 批量更新多个对象或数组到数据库
     *
     * @param string $table 表名
     * @param array $objects $object 要更新的对象数组，对象属性需要和该表字段一致
     * @param null | string | array $primaryKey 主键或指定键名更新，未指定时自动取表的主键
     * @return int 更新的行数，如果数据无变化，则返回0
     * @throws DbException
     */
    public function updateMany(string $table, array $objects, $primaryKey = null): int
    {
        if (!is_array($objects) || count($objects) === 0) return 0;

        if ($primaryKey === null) {
            $primaryKey = $this->getTablePrimaryKey($table);
            if ($primaryKey === null) {
                // 新数据表 $table 无主键，不支持按主键更新
                throw new DbException('Db:updateMany - Table (' . $table . ') no primary key, not support update with primary key!');
            }
        }

        reset($objects);
        $object = current($objects);
        $vars = null;
        if (is_array($object)) {
            $vars = $object;
        } elseif (is_object($object)) {
            $vars = get_object_vars($object);
        } else {
            // 批量更新的数据格式须为对象或数组
            throw new DbException('Db:updateMany -  Update data should be object or array!');
        }
        ksort($vars);

        $fields = [];
        $where = [];
        foreach ($vars as $key => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }

            if (is_array($primaryKey)) {
                if (in_array($key, $primaryKey)) {
                    $where[] = $this->connection->quoteKey($key) . '=?';
                    continue;
                }
            } else {
                // 主键不更新
                if ($key === $primaryKey) {
                    $where[] = $this->connection->quoteKey($key) . '=?';
                    continue;
                }
            }

            $fields[] = $this->connection->quoteKey($key) . '=?';
        }

        if (!$where) {
            // 更新数据时未指定条件
            throw new DbException('Db:updateMany - Missing where conditions!');
        }

        $sql = 'UPDATE ' . $this->connection->quoteKey($table) . ' SET ' . implode(',', $fields) . ' WHERE ' . implode(' AND ', $where);
        $statement = $this->connection->prepare($sql);

        $effectLines = 0;
        foreach ($objects as $o) {
            $vars = null;
            if (is_array($o)) {
                $vars = $o;
            } elseif (is_object($o)) {
                $vars = get_object_vars($o);
            } else {
                // 批量更新的数据格式须为对象或数组
                throw new DbException('Db:updateMany - Update data should be object or array!');
            }
            ksort($vars);

            $fieldValues = [];
            $whereValue = [];
            foreach ($vars as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    continue;
                }

                if (is_array($primaryKey)) {

                    if (in_array($key, $primaryKey)) {
                        $whereValue[] = $value;
                        continue;
                    }

                } else {

                    // 主键不更新
                    if ($key === $primaryKey) {
                        $whereValue[] = $value;
                        continue;
                    }
                }

                $fieldValues[] = $value;
            }

            if (count($whereValue) !== count($where)) {
                // 批量更新的数组未包含必须的主键名
                throw new DbException('Db:updateMany - Update data missing primary key!');
            }

            if (count($fieldValues) !== count($fields)) {
                // 批量更新的数组内部结构不一致
                throw new DbException('Db:updateMany - Update data items have different structure!');
            }

            $fieldValues = array_merge($fieldValues, $whereValue);
            $statement->execute(array_values($fieldValues));
            $effectLines += $statement->rowCount();
        }
        $statement->closeCursor();

        return $effectLines;
    }

    /**
     * 快速批量更新多个对象或数组到数据库
     *
     * @param string $table 表名
     * @param array $objects 要插入数据库的对象数组或二维数组，对象属性或数组锓名需要和该表字段一致
     * @param null | string | array $primaryKey 主键或指定键名更新，未指定时自动取表的主键
     * @return int 更新的行数，如果数据无变化，则返回0
     * @throws DbException
     */
    public function quickUpdateMany(string $table, array $objects, $primaryKey = null): int
    {
        if (!is_array($objects) || count($objects) === 0) return 0;

        if ($primaryKey === null) {
            $primaryKey = $this->getTablePrimaryKey($table);
            if ($primaryKey === null) {
                // 新数据表 $table 无主键，不支持按主键更新
                throw new DbException('Db:quickUpdateMany - Table (' . $table . ') no primary key, not support update with primary key!');
            }
        }

        // 获取第一条记灵的结构作为更新的数据结构
        reset($objects);
        $object = current($objects);
        $vars = null;
        if (is_array($object)) {
            $vars = $object;
        } elseif (is_object($object)) {
            $vars = get_object_vars($object);
        } else {
            // 快速批量更新的数据结构须为对象或数组
            throw new DbException('Db:quickUpdateMany - Update data should be object or array!');
        }
        ksort($vars);
        $fields = array_keys($vars);

        // 检查主键值是否存在
        if (is_array($primaryKey)) {
            foreach ($primaryKey as $pKey) {
                if (!in_array($pKey, $fields)) {
                    // 快速批量更新的数据结构中未包念主键 $pKey
                    throw new DbException('Db:quickUpdateMany - Update data missing primary key: ' . $pKey . '!');
                }
            }
        } else {
            if (!in_array($primaryKey, $fields)) {
                // 快速批量更新的数据结构中未包念主键 $primaryKey
                throw new DbException('Db:quickUpdateMany - Update data missing primary key: ' . $primaryKey . '!');
            }
        }

        $sql = 'UPDATE ' . $this->connection->quoteKey($table) . ' SET ';

        $primaryKeyIn = [];
        $caseMapping = [];
        foreach ($fields as $field) {
            if (is_array($primaryKey)) {
                if (in_array($field, $primaryKey)) {
                    continue;
                }
            } else {
                if ($field === $primaryKey) {
                    continue;
                }
            }

            $caseMapping[$field] = [];
        }

        foreach ($objects as $o) {
            $vars = null;
            if (is_array($o)) {
                $vars = $o;
            } elseif (is_object($o)) {
                $vars = get_object_vars($o);
            } else {
                // 批量更新的数据结构须为对象或数组
                throw new DbException('Db:quickUpdateMany - Update data should be object or array!');
            }
            ksort($vars);

            foreach ($fields as $field) {
                if (!isset($vars[$field])) {
                    // 批量更新的数据结构不一致
                    throw new DbException('Db:quickUpdateMany - Update data items have different structure!');
                }

                if (is_array($primaryKey)) {
                    if (in_array($field, $primaryKey)) {
                        continue;
                    }

                    $when = [];
                    foreach ($primaryKey as $pKey) {
                        $when[] = $this->connection->quoteKey($pKey) . ' = ' . $this->connection->quoteValue($vars[$pKey]);
                    }

                    if ($vars[$field] === null) {
                        $caseMapping[$field][] = 'WHEN ' . implode(' and ', $when) . ' THEN null';
                    } else {
                        $caseMapping[$field][] = 'WHEN ' . implode(' and ', $when) . ' THEN ' . $this->connection->quoteValue($vars[$field]);
                    }

                } else {
                    // 主键不更新
                    if ($field === $primaryKey) {
                        continue;
                    }

                    if ($vars[$field] !== null) {
                        $caseMapping[$field][] = 'WHEN ' . $this->connection->quoteValue($vars[$primaryKey]) . ' THEN null';
                    } else {
                        $caseMapping[$field][] = 'WHEN ' . $this->connection->quoteValue($vars[$primaryKey]) . ' THEN ' . $this->connection->quoteValue($vars[$field]);
                    }
                }
            }

            if (is_array($primaryKey)) {
                $in = [];
                foreach ($primaryKey as $pKey) {
                    $in[] = $this->connection->quoteValue($vars[$pKey]);
                }
                $primaryKeyIn[] = '(' . implode(',', $in) . ')';
            } else {
                $primaryKeyIn[] = $this->connection->quoteValue($vars[$primaryKey]);
            }
        }

        foreach ($caseMapping as $field => $cases) {
            $sql .= $this->connection->quoteKey($field) . ' = case ';

            if (!is_array($primaryKey)) {
                $sql .= $this->connection->quoteKey($primaryKey) . ' ';
            }

            $sql .= implode(' ', $cases);
            $sql .= 'END,';
        }

        $sql = substr($sql, 0, -1);
        $sql .= ' WHERE ';
        if (is_array($primaryKey)) {
            $sql .= '(' . implode(',', $this->connection->quoteKeys($primaryKey)) . ') IN ';
        } else {
            $sql .= $this->connection->quoteKey($primaryKey) . ' IN ';
        }

        $sql .= '(' . implode(',', $primaryKeyIn) . ')';

        $statement = $this->execute($sql);
        $effectLines = $statement->rowCount();
        $statement->closeCursor();

        return $effectLines;
    }

    /**
     * 替换一个对象或数组到数据库
     *
     * @param string $table 表名
     * @param array | object $object 要替换的对象或数组，对象属性或数组键名需要和该表字段一致
     * @return int 影响的行数，如果数据无变化，则返回0，失败时抛出异常
     * @throws DbException
     */
    abstract function replace(string $table, $object): int;

    /**
     * 批量替换多个对象或数组到数据库
     *
     * @param string $table 表名
     * @param array $objects 要替换数据库的对象数组或二维数组，对象属性或数组键名需要和该表字段一致
     * @return int 影响的行数，如果数据无变化，则返回0，失败时抛出异常
     * @throws DbException
     */
    abstract function replaceMany(string $table, array $objects): int;

    /**
     * 快速替换一个对象或数组到数据库
     *
     * @param string $table 表名
     * @param array | object $object 要替换的对象或数组，对象属性或数组锓名需要和该表字段一致
     * @return int 影响的行数，如果数据无变化，则返回0，失败时抛出异常
     * @throws DbException
     */
    abstract function quickReplace(string $table, $object): int;

    /**
     * 快速批量替换多个对象或数组到数据库
     *
     * @param string $table 表名
     * @param array $objects 要替换的对象数组或二维数组，对象属性或数组键名需要和该表字段一致
     * @return int 影响的行数，如果数据无变化，则返回0，失败时抛出异常
     * @throws DbException
     */
    abstract function quickReplaceMany(string $table, array $objects): int;

    /**
     * 生成 UUID
     *
     * @return string
     */
    abstract function uuid(): string;

    /**
     * 快速生成 UUID
     *
     * 优先使用php生成，php不支持时再再调用数据库的方法生成
     *
     * @return string
     */
    public function quickUuid(): string
    {
        if (function_exists('uuid_create')) {
            return uuid_create();
        } else {
            return $this->uuid();
        }
    }

    /**
     * 获取 insert 插入后产生的 id
     *
     * @return string|false
     */
    public function getLastInsertId()
    {
        return $this->connection->getLastInsertId();
    }

    /**
     * 获取当前数据库所有表信息
     *
     * @return array
     */
    abstract function getTables(): array;

    /**
     * 获取当前数据库所有表名
     *
     * @return array
     */
    abstract function getTableNames(): array;

    /**
     * 获取当前连接的所有库信息
     *
     * @return array
     */
    abstract function getDatabases(): array;

    /**
     * 获取当前连接的所有库名
     *
     * @return array
     */
    abstract function getDatabaseNames(): array;

    /**
     * 获取一个表的字段列表
     *
     * @param string $table 表名
     * @return array 对象数组
     * 字段对象典型结构
     * {
     *      'name' => '字段名',
     *      'type' => '类型',
     *      'length' => '长度',
     *      'precision' => '精度',
     *      'scale' => '长度',
     *      'comment' => '备注',
     *      'default' => '默认值',
     *      'nullAble' => '是否允许为空',
     * }
     */
    abstract function getTableFields(string $table): array;

    /**
     * 获取指定表的主银
     *
     * @param string $table 表名
     * @return string | array | null
     */
    abstract function getTablePrimaryKey(string $table);

    /**
     * 删除表
     *
     * @param string $table 表名
     */
    abstract function dropTable(string $table);

    /**
     * 开启事务处理
     *
     * @throws DbException
     */
    public function startTransaction()
    {
        $this->connection->beginTransaction();
    }

    /**
     * 开启事务处理
     *
     * @throws DbException
     */
    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    /**
     * 事务回滚
     *
     * @throws DbException
     */
    public function rollback()
    {
        $this->connection->rollBack();
    }

    /**
     * 事务提交
     *
     * @throws DbException
     */
    public function commit()
    {
        $this->connection->commit();
    }

    /**
     * 是否在事务中
     *
     * @return bool
     * @throws DbException
     */
    public function inTransaction(): bool
    {
        return $this->connection->inTransaction();
    }

    /**
     * 获取 版本号
     *
     * @return string
     * @throws DbException
     */
    public function getVersion(): string
    {
        return $this->connection->getVersion();
    }

    /**
     * 处理插入数据库的字段名或表名
     *
     * @param string $field
     * @return string
     */
    public function quoteKey(string $field): string
    {
        return $this->connection->quoteKey($field);
    }

    /**
     * 处理多个插入数据库的字段名或表名
     *
     * @param array $fields
     * @return array
     */
    public function quoteKeys(array $fields): array
    {
        return $this->connection->quoteKeys($fields);
    }

    /**
     * 处理插入数据库的字符串值，防注入, 使用了PDO提供的quote方法
     *
     * @param string $value
     * @return string
     * @throws DbException
     */
    public function quoteValue($value): string
    {
        return $this->connection->quoteValue($value);
    }

    /**
     * 处理一组插入数据库的字符串值，防注入, 使用了PDO提供的quote方法
     *
     * @param array $values
     * @return array
     * @throws DbException
     */
    public function quoteValues(array $values): array
    {
        return $this->connection->quoteValues($values);
    }

    /**
     * 处理插入数据库的字符串值，防注入, 仅处理敏感字符，不加外层引号，
     * 与 quote 方法的区别可以理解为 quoteValue 比 escape 多了最外层的引号
     *
     * @param string $value
     * @return string
     */
    public function escape($value): string
    {
        return $this->connection->escape($value);
    }


}
