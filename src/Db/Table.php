<?php

namespace Be\Db;

use Be\Be;

/**
 * 数据库表 查询器
 */
class Table
{
    /**
     * 数据库名
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
     * @var string|array|null
     */
    protected $_primaryKey = null;

    protected array $_fields = []; // 字段列表

    protected string $_alias = ''; // 当前表的别名
    protected array $_join = []; // 表连接
    protected array $_where = []; // where 条件
    protected string $_groupBy = ''; // 分组
    protected string $_having = ''; // having
    protected int $_offset = 0; // 分页编移
    protected int $_limit = 0; // 分页大小
    protected string $_orderBy = ''; // 排序

    protected ?array $_lastSql = null; // 上次执行的 SQL

    /**
     * 给当前表设置别名
     *
     * @param string $alias 别名
     * @return Table
     */
    public function alias($alias): Table
    {
        $this->_alias = $alias;
        return $this;
    }


    /**
     * 左连接
     *
     * @param string $table 表名
     * @param string $on 连接条件
     * @return Table
     */
    public function leftJoin(string $table, string $on): Table
    {
        return $this->_join('LEFT JOIN', $table, $on);
    }

    /**
     * 右连接
     *
     * @param string $table 表名
     * @param string $on 连接条件
     * @return Table
     */
    public function rightJoin(string $table, string $on): Table
    {
        return $this->_join('RIGHT JOIN', $table, $on);
    }

    /**
     * 内连接
     *
     * @param string $table 表名
     * @param string $on 连接条件
     * @return Table
     */
    public function innerJoin(string $table, string $on): Table
    {
        return $this->_join('INNER JOIN', $table, $on);
    }

    /**
     * 内连接 同 innerJoin
     *
     * @param string $table 表名
     * @param string $on 连接条件
     * @return Table
     */
    public function join(string $table, string $on): Table
    {
        return $this->_join('INNER JOIN', $table, $on);
    }

    /**
     * 全连接
     *
     * @param string $table 表名
     * @param string $on 连接条件
     * @return Table
     */
    public function fullJoin(string $table, string $on): Table
    {
        return $this->_join('FULL JOIN', $table, $on);
    }

    /**
     * 交叉连接
     *
     * @param string $table 表名
     * @param string $on 连接条件
     * @return Table
     */
    public function crossJoin(string $table, string $on): Table
    {
        return $this->_join('CROSS JOIN', $table, $on);
    }

    protected function _join(string $type, string $table, string $on): Table
    {
        $alias = null;
        if (strpos($table, ' ') !== false) {
            $splitter = ' ';
            if (strpos($table, ' as ') !== false) {
                $splitter = ' as ';
            } elseif (strpos($table, ' AS ') !== false) {
                $splitter = ' AS ';
            }
            $tables = explode($splitter, $table);
            $table = trim($tables[0]);
            $alias = trim($tables[1]);
        }

        $this->_join[] = [$type, $table, $on, $alias];
        return $this;
    }

    /**
     * 设置单个查询条件
     *
     * @param string|array $field 字段名或需要直接拼接进SQL的字符
     * @param string|null $op 操作类型：=/<>/!=/>/</>=/<=/between/not between/in/not in/like/not like
     * @param string|array|null $value 值，
     * @return Table
     * @example
     * <pre>
     * $table->where("username LIKE 'Tom%'");
     * $table->where('username','Tom');
     * $table->where('username','=','Tom');
     * $table->where('age','=',18);
     * $table->where('age','>',18);
     * $table->where('age','between', array(18, 30));
     * $table->where('userId','in', array(1, 2, 3, 4));
     * $table->where(["username LIKE 'Tom%'"]);
     * $table->where(['username','Tom']);
     * $table->where(['username','=', 'Tom']);
     * </pre>
     */
    public function where($field, string $op = null, $value = null): Table
    {
        if (is_array($field)) {
            $this->where(...$field);
        } else {
            if (count($this->_where) > 0) {
                $this->_where[] = 'AND';
            }

            $field = trim($field);
            if ($op === null) {  // 第二个参数为空时，第一个参数直接拼入 sql
                $this->_where[] = $field;
            } elseif ($value === null) {
                $this->_where[] = [$field, '=', $op]; // 等值查询
            } else {
                $this->_where[] = [$field, $op, $value]; // 普通条件查询
            }
        }

        return $this;
    }

    /**
     * 设置一组查询条件
     *
     * @param array $conditions 一组查询条件
     * @return Table
     * @example
     * <pre>
     * $table->conditon([
     *     ['username','Tom'],
     *     'OR',
     *     ['age','>',18],
     *]); // 最终SQL: WHERE (username='Tom' OR age>18)
     * </pre>
     */
    public function condition(array $conditions): Table
    {
        $fieldCount = count($conditions);

        if ($fieldCount === 0) return $this;

        if ($fieldCount === 1) {
            return $this->where($conditions[0]);
        }

        if (count($this->_where) > 0) {
            $this->_where[] = 'AND';
        }

        $this->_where[] = '(';
        foreach ($conditions as $w) {
            if (is_array($w)) {
                $len = count($w);
                if ($len === 1) {
                    $this->_where[] = $w[0];
                } elseif ($len === 2) {
                    $this->_where[] = [$w[0], '=', $w[1]];
                } elseif ($len === 3) {
                    $this->_where[] = [$w[0], $w[1], $w[2]];
                }
            } else {
                $this->_where[] = $w;
            }
        }
        $this->_where[] = ')';

        return $this;
    }

    /**
     * 分组
     *
     * @param string $field 分组条件
     * @return Table
     */
    public function groupBy(string $field): Table
    {
        $this->_groupBy = $field;
        return $this;
    }

    /**
     * Having 筛选
     *
     * @param string $having
     * @return Table
     */
    public function having(string $having): Table
    {
        $this->_having = $having;
        return $this;
    }

    /**
     * 偏移量
     *
     * @param int $offset 偏移量
     * @return Table
     */
    public function offset(int $offset = 0): Table
    {
        $this->_offset = (int)$offset;
        return $this;
    }

    /**
     * 最多返回多少条记录
     *
     * @param int $limit 要返回的记录条数
     * @return Table
     */
    public function limit(int $limit = 20): Table
    {
        $this->_limit = (int)$limit;
        return $this;
    }

    /**
     * 排序
     *
     * @param string $field 要排序的字段
     * @param string|null $dir 排序方向：ASC | DESC
     * @return Table
     */
    public function orderBy(string $field, string $dir = null): Table
    {
        $field = trim($field);
        if ($dir === null) {
            $this->_orderBy = $field;
        } else {
            $dir = strtoupper(trim($dir));
            if ($dir !== 'ASC' && $dir !== 'DESC') {
                $this->_orderBy = $field;
            } else {
                $this->_orderBy = Be::getDb($this->_dbName)->quoteKey($field) . ' ' . $dir;
            }
        }
        return $this;
    }

    /**
     * 查询单个字段第一条记录
     *
     * @param string $field 查询的字段
     * @return string
     */
    public function getValue(string $field): string
    {
        return $this->query('getValue', $field);
    }

    /**
     * 获取由符合条件的记录中的指定字段的值组成的一维数组
     *
     * @param string $field 字段名
     * @return array 数组
     */
    public function getValues(string $field): array
    {
        return $this->query('getValues', $field);
    }

    /**
     * 查询单个字段的所有记录, 跌代器方式
     *
     * @param string $field 查询的字段
     * @return \Generator
     */
    public function getYieldValues(string $field): \Generator
    {
        return $this->query('getYieldValues', $field);
    }

    /**
     * 查询键值对
     *
     * @param string $keyField 键字段
     * @param string $valueField 值字段
     * @return array 数组
     */
    public function getKeyValues(string $keyField, string $valueField): array
    {
        return $this->query('getKeyValues', $keyField . ',' . $valueField);
    }

    /**
     * 查询单条记录
     *
     * @param string|null $fields 查询用到的字段列表
     * @return array 数组
     */
    public function getArray(string $fields = null): array
    {
        return $this->query('getArray', $fields);
    }

    /**
     * 获取多条二维数组格式的记录
     *
     * @param string|null $fields 字段列表，未指定时返回该表所有字段。
     * @return array 二维数组
     */
    public function getArrays(string $fields = null): array
    {
        return $this->query('getArrays', $fields);
    }

    /**
     * 查询多条记录, 跌代器方式
     *
     * @param string|null $fields 查询用到的字段列表
     * @return \Generator
     */
    public function getYieldArrays(string $fields = null): \Generator
    {
        return $this->query('getYieldArrays', $fields);
    }

    /**
     * 查询多条记录
     *
     * @param string $keyField 键字段
     * @param string $fields 查询用到的字段列表
     * @return array 二维数组
     */
    public function getKeyArrays(string $keyField, string $fields = null): array
    {
        return $this->query('getKeyArrays', $fields, $keyField);
    }

    /**
     * 查询单条记录
     *
     * @param string|null $fields 查询用到的字段列表
     * @return object 对象
     */
    public function getObject(string $fields = null)
    {
        return $this->query('getObject', $fields);
    }

    /**
     * 获取多条对象数组格式的记录
     *
     * @param string|null $fields 字段列表，未指定时返回该表所有字段。
     * @return array
     */
    public function getObjects(string $fields = null): array
    {
        return $this->query('getObjects', $fields);
    }

    /**
     * 查询多条记录, 跌代器方式
     *
     * @param string|null $fields 查询用到的字段列表
     * @return \Generator
     */
    public function getYieldObjects(string $fields = null): \Generator
    {
        return $this->query('getYieldObjects', $fields);
    }

    /**
     * 查询多条记发
     *
     * @param string $keyField 键字段
     * @param string|null $fields 查询用到的字段列表
     * @return array 对象列表
     */
    public function getKeyObjects(string $keyField, string $fields = null): array
    {
        return $this->query('getKeyObjects', $fields, $keyField);
    }

    /**
     * 执行数据库查询
     *
     * @param string $fn 指定数据库查询函数名
     * @param string $fields 查询用到的字段列表
     * @return mixed
     */
    private function query(string $fn, string $fields = null, string $keyField = null)
    {
        $db = Be::getDb($this->_dbName);

        $sqlData = $this->prepareSql();
        $sql = null;
        if ($fields === null) {
            $fields = '*';
        }

        $sql = 'SELECT ' . $fields;
        $sql .= ' FROM ' . $db->quoteKey($this->_tableName);

        if ($this->_alias) {
            $sql .= ' AS ' . $this->_alias;
        }

        if ($this->_join) {
            foreach ($this->_join as $join) {
                $sql .= $join[0] . ' ' . $db->quoteKey($join[1]);
                if ($join[3]) {
                    $sql .= ' AS ' . $db->quoteKey($join[3]);
                }
                $sql .= ' ON ' . $join[2];
            }
        }

        $sql .= $sqlData[0];

        $this->_lastSql = [$sql, $sqlData[1]];

        $result = $keyField === null ? $db->$fn($sql, $sqlData[1]) : $db->$fn($sql, $sqlData[1], $keyField);

        return $result;
    }

    /**
     * 纺计数量
     *
     * @param string $field 字段
     * @return int
     */
    public function count(string $field = '*'): int
    {
        return (int)$this->query('getValue', 'COUNT(' . $field . ')');
    }

    /**
     * 求和
     *
     * @param string $field 字段名
     * @return string|null
     */
    public function sum(string $field)
    {
        return $this->query('getValue', 'SUM(' . $field . ')');
    }

    /**
     * 取最小值
     *
     * @param string $field 字段名
     * @return string|null
     */
    public function min(string $field)
    {
        return $this->query('getValue', 'MIN(' . $field . ')');
    }

    /**
     * 取最大值
     *
     * @param string $field 字段名
     * @return string|null
     */
    public function max(string $field)
    {
        return $this->query('getValue', 'MAX(' . $field . ')');
    }

    /**
     * 取平均值
     *
     * @param string $field 字段名
     * @return string|null
     */
    public function avg(string $field)
    {
        return $this->query('getValue', 'AVG(' . $field . ')');
    }

    /**
     * 自增某个字段
     *
     * @param string $field 字段名
     * @param int $step 自增量
     * @return Table
     */
    public function increment(string $field, int $step = 1): Table
    {
        $db = Be::getDb($this->_dbName);

        $sqlData = $this->prepareSql();
        $sql = 'UPDATE ' . $db->quoteKey($this->_tableName);

        if ($this->_alias) {
            $sql .= ' AS ' . $this->_alias;
        }

        if ($this->_join) {
            foreach ($this->_join as $join) {
                $sql .= ' ' . $join[0] . ' ' . $db->quoteKey($join[1]);
                if ($join[3]) {
                    $sql .= ' AS ' . $db->quoteKey($join[3]);
                }
                $sql .= ' ON ' . $join[2];
            }
        }

        $quotedField = null;
        if (strpos($field, '.') !== false) {
            $fieldParts = explode('.', $field);
            $quotedFieldParts = [];
            foreach ($fieldParts as $fieldPart) {
                $quotedFieldParts[] = $db->quoteKey($fieldPart);
            }
            $quotedField = implode('.', $quotedFieldParts);
        } else {
            $quotedField = $db->quoteKey($field);
        }

        $sql .= ' SET ' . $quotedField . '=' . $quotedField . '+' . intval($step);
        $sql .= $sqlData[0];
        $this->_lastSql = array($sql, $sqlData[1]);

        $db->query($sql, $sqlData[1]);

        return $this;
    }

    /**
     * 自减某个字段
     *
     * @param string $field 字段名
     * @param int $step 自减量
     * @return Table
     */
    public function decrement(string $field, int $step = 1): Table
    {
        $db = Be::getDb($this->_dbName);

        $sqlData = $this->prepareSql();
        $sql = 'UPDATE ' . $db->quoteKey($this->_tableName);

        if ($this->_alias) {
            $sql .= ' AS ' . $this->_alias;
        }

        if ($this->_join) {
            foreach ($this->_join as $join) {
                $sql .= ' ' . $join[0] . ' ' . $db->quoteKey($join[1]);
                if ($join[3]) {
                    $sql .= ' AS ' . $db->quoteKey($join[3]);
                }
                $sql .= ' ON ' . $join[2];
            }
        }

        $quotedField = null;
        if (strpos($field, '.') !== false) {
            $fieldParts = explode('.', $field);
            $quotedFieldParts = [];
            foreach ($fieldParts as $fieldPart) {
                $quotedFieldParts[] = $db->quoteKey($fieldPart);
            }
            $quotedField = implode('.', $quotedFieldParts);
        } else {
            $quotedField = $db->quoteKey($field);
        }

        $sql .= ' SET ' . $quotedField . '=' . $quotedField . '-' . intval($step);
        $sql .= $sqlData[0];
        $this->_lastSql = array($sql, $sqlData[1]);

        $db->query($sql, $sqlData[1]);

        return $this;
    }

    /**
     * 更新数据
     *
     * @param array $keyValues 要更新的数据键值对
     * @return Table
     */
    public function update(array $keyValues = []): Table
    {
        $db = Be::getDb($this->_dbName);

        $sqlData = $this->prepareSql();

        $sql = 'UPDATE ' . $db->quoteKey($this->_tableName);

        if ($this->_alias) {
            $sql .= ' AS ' . $this->_alias;
        }

        if ($this->_join) {
            foreach ($this->_join as $join) {
                $sql .= ' ' . $join[0] . ' ' . $db->quoteKey($join[1]);
                if ($join[3]) {
                    $sql .= ' AS ' . $db->quoteKey($join[3]);
                }
                $sql .= ' ON ' . $join[2];
            }
        }

        $quotedFields = [];
        foreach (array_keys($keyValues) as $field) {
            if (strpos($field, '.') !== false) {
                $fieldParts = explode('.', $field);
                $quotedFieldParts = [];
                foreach ($fieldParts as $fieldPart) {
                    $quotedFieldParts[] = $db->quoteKey($fieldPart);
                }
                $quotedFields[] = implode('.', $quotedFieldParts);
            } else {
                $quotedFields[] = $db->quoteKey($field);
            }
        }

        $sql .= ' SET ' . implode('=?,', $quotedFields) . '=?';
        $sql .= $sqlData[0];
        $this->_lastSql = array($sql, $sqlData[1]);

        $db->query($sql, array_merge(array_values($keyValues), $sqlData[1]));

        return $this;
    }

    /**
     * 删除数据
     * @param string|null $tableName 表名，有连表时需指定
     * @return Table
     */
    public function delete(string $tableName = null): Table
    {
        $db = Be::getDb($this->_dbName);

        $sqlData = $this->prepareSql();
        $sql = 'DELETE';
        if ($tableName) {
            $sql .= ' ' . $tableName;
        }
        $sql .= ' FROM ' . $db->quoteKey($this->_tableName);

        if ($this->_alias) {
            $sql .= ' AS ' . $this->_alias;
        }

        if ($this->_join) {
            foreach ($this->_join as $join) {
                $sql .= ' ' . $join[0] . ' ' . $db->quoteKey($join[1]);
                if ($join[3]) {
                    $sql .= ' AS ' . $db->quoteKey($join[3]);
                }
                $sql .= ' ON ' . $join[2];
            }
        }

        $sql .= $sqlData[0];
        $this->_lastSql = array($sql, $sqlData[1]);

        $db->query($sql, $sqlData[1]);

        return $this;
    }

    /**
     * 清空表
     * @return Table
     */
    public function truncate(): Table
    {
        $db = Be::getDb($this->_dbName);

        $sql = 'TRUNCATE TABLE ' . $db->quoteKey($this->_tableName);
        $this->_lastSql = array($sql, []);

        $db->query($sql);

        return $this;
    }

    /**
     * 删除表
     * @return Table
     */
    public function drop(): Table
    {
        $db = Be::getDb($this->_dbName);

        $sql = 'DROP TABLE ' . $db->quoteKey($this->_tableName);
        $this->_lastSql = array($sql, []);

        $db->query($sql);

        return $this;
    }

    /**
     * 初始化
     *
     * @return Table
     */
    public function init(): Table
    {
        $this->_join = [];
        $this->_where = [];
        $this->_groupBy = '';
        $this->_having = '';
        $this->_offset = 0;
        $this->_limit = 0;
        $this->_orderBy = '';

        return $this;
    }

    /**
     * 准备查询的 sql
     *
     * @return array
     * @throws TableException
     */
    public function prepareSql(): array
    {
        $db = Be::getDb($this->_dbName);

        $sql = '';
        $values = [];

        // 处理 where 条件
        if (count($this->_where) > 0) {
            $sql .= ' WHERE';
            foreach ($this->_where as $where) {
                if (is_array($where)) {
                    if (is_array($where[1])) {
                        $sql .= ' ' . $where[0];
                        $values = array_merge($values, $where[1]);
                    } else {
                        $sql .= ' ' . $db->quoteKey($where[0]);
                        $op = strtoupper($where[1]);
                        $sql .= ' ' . $op;
                        switch ($op) {
                            case 'IN':
                            case 'NOT IN':
                                if (is_array($where[2]) && count($where[2]) > 0) {
                                    $sql .= ' (' . implode(',', array_fill(0, count($where[2]), '?')) . ')';
                                    $values = array_merge($values, $where[2]);
                                } else {
                                    throw new TableException('Table query(in/not in) condition exception!');
                                }
                                break;
                            case 'BETWEEN':
                            case 'NOT BETWEEN':
                                $sql .= ' ' . $op;
                                if (is_array($where[2]) && count($where[2]) === 2) {
                                    $sql .= ' ? AND ?';
                                    $values = array_merge($values, $where[2]);
                                } else {
                                    throw new TableException('Table query(between/not between) condition exception!');
                                }
                                break;
                            default:
                                $sql .= ' ?';
                                $values[] = $where[2];
                        }
                    }
                } else {
                    $sql .= ' ' . $where;
                }
            }
        }

        if ($this->_groupBy) $sql .= ' GROUP BY ' . $this->_groupBy;
        if ($this->_having) $sql .= ' HAVING ' . $this->_having;
        if ($this->_orderBy) $sql .= ' ORDER BY ' . $this->_orderBy;

        if ($this->_limit > 0) {
            if ($this->_offset > 0) {
                $sql .= ' LIMIT ' . $this->_offset . ',' . $this->_limit;
            } else {
                $sql .= ' LIMIT ' . $this->_limit;
            }
        } else {
            if ($this->_offset > 0) {
                $sql .= ' OFFSET ' . $this->_offset;
            }
        }

        return [$sql, $values];
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
     * @return string|array|null
     */
    public function getPrimaryKey()
    {
        return $this->_primaryKey;
    }

    /**
     * 获取字段列表
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->_fields;
    }

    /**
     * 获取最后一次执行的完整 SQL
     *
     * @return string
     */
    public function getLastSql(): string
    {
        $db = Be::getDb($this->_dbName);

        if ($this->_lastSql === null) return '';
        $lastSql = $this->_lastSql[0];
        $values = $this->_lastSql[1];
        $n = count($values);
        $i = 0;
        while (($pos = strpos($lastSql, '?')) !== false && $i < $n) {
            $lastSql = substr($lastSql, 0, $pos) . $db->quoteValue($values[$i]) . substr($lastSql, $pos + 1);
            $i++;
        }
        return $lastSql;
    }


}