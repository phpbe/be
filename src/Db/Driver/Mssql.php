<?php

namespace Be\Db\Driver;

use Be\Db\Driver;
use Be\Db\DbException;

/**
 * 数据库类 MSSQL(SQL Server)
 */
class Mssql extends Driver
{


    public function __construct(string $name, \PDO $pdo = null)
    {
        $this->name = $name;
        $this->connection = new \Be\Db\Connection\Mssql($name, $pdo);
    }

    /**
     * 返回一个跌代器数组
     *
     * @param string $sql 查询语句
     * @param array $bind 参数
     * @return \Generator
     */
    public function getYieldValues(string $sql, array $bind = null): \Generator
    {
        $connection = new \Be\Db\Connection\Mssql($this->name);
        $statement = $connection->execute($sql, $bind);
        while ($tuple = $statement->fetch(\PDO::FETCH_NUM)) {
            yield $tuple[0];
        }
        $statement->closeCursor();
        $connection->release();
    }

    /**
     * 返回一个跌代器二维数组
     *
     * @param string $sql 查询语句
     * @param array $bind 参数
     * @return \Generator
     */
    public function getYieldArrays(string $sql, array $bind = null): \Generator
    {
        $connection = new \Be\Db\Connection\Mssql($this->name);
        $statement = $connection->execute($sql, $bind);
        while ($result = $statement->fetch(\PDO::FETCH_ASSOC)) {
            yield $result;
        }
        $statement->closeCursor();
        $connection->release();
    }

    /**
     * 返回一个跌代器对象数组
     *
     * @param string $sql 查询语句
     * @param array $bind 参数
     * @return \Generator
     */
    public function getYieldObjects(string $sql, array $bind = null): \Generator
    {
        $connection = new \Be\Db\Connection\Mssql($this->name);
        $statement = $connection->execute($sql, $bind);
        while ($result = $statement->fetchObject()) {
            yield $result;
        }
        $statement->closeCursor();
        $connection->release();
    }

    /**
     * 替换一个对象或数组到数据库
     *
     * @param string $table 表名
     * @param array | object $object 要替换的对象或数组，对象属性或数组键名需要和该表字段一致
     * @return int 影响的行数，如果数据无变化，则返回0，失败时抛出异常
     * @throws DbException
     */
    public function replace(string $table, $object): int
    {
        throw new DbException('Mssql 数据库不支持 Replace Into！');
    }

    /**
     * 批量替换多个对象或数组到数据库
     *
     * @param string $table 表名
     * @param array $objects 要替换数据库的对象数组或二维数组，对象属性或数组键名需要和该表字段一致
     * @return int 影响的行数，如果数据无变化，则返回0，失败时抛出异常
     * @throws DbException
     */
    public function replaceMany(string $table, array $objects): int
    {
        throw new DbException('Mssql 数据库不支持 Replace Into！');
    }

    /**
     * 快速替换一个对象或数组到数据库
     *
     * @param string $table 表名
     * @param array | object $object 要替换的对象或数组，对象属性或数组锓名需要和该表字段一致
     * @return int 影响的行数，如果数据无变化，则返回0，失败时抛出异常
     * @throws DbException
     */
    public function quickReplace(string $table, $object): int
    {
        throw new DbException('Mssql 数据库不支持 Replace Into！');
    }

    /**
     * 快速批量替换多个对象或数组到数据库
     *
     * @param string $table 表名
     * @param array $objects 要替换的对象数组或二维数组，对象属性或数组键名需要和该表字段一致
     * @return int 影响的行数，如果数据无变化，则返回0，失败时抛出异常
     * @throws DbException
     */
    public function quickReplaceMany(string $table, array $objects): int
    {
        throw new DbException('Mssql 数据库不支持 Replace Into！');
    }

    /**
     * 获取 insert 插入后产生的 id
     *
     * @return string|false
     */
    public function getLastInsertId()
    {
        return $this->getValue('SELECT ISNULL(SCOPE_IDENTITY(), 0)');
    }

    /**
     * 生成 UUID
     *
     * @return string
     */
    public function uuid(): string
    {
        return $this->getValue('SELECT NEWID()');
    }

    /**
     * 获取当前数据库所有表信息
     *
     * @return array
     */
    public function getTables(): array
    {
        // SELECT * FROM sysobjects WHERE xType='u';
        // SELECT * FROM sys.objects WHERE type='U';
        // SELECT * FROM [INFORMATION_SCHEMA].[TABLES] WHERE [TABLE_TYPE] = 'BASE TABLE';
        $sql = 'SELECT 
                    a.name, 
                    g.value AS comment
                FROM sys.tables a
                LEFT JOIN sys.extended_properties g ON a.object_id = g.major_id AND g.minor_id = 0
                WHERE a.type=\'U\'';
        return $this->getObjects($sql);
    }

    /**
     * 获取当前数据库所有表名
     *
     * @return array
     */
    public function getTableNames(): array
    {
        // SELECT [TABLE_NAME] FROM [INFORMATION_SCHEMA].[TABLES] WHERE [TABLE_TYPE] = 'BASE TABLE'
        return $this->getValues('SELECT [name] FROM sys.tables WHERE [type] = \'U\'');
    }

    /**
     * 获取当前连接的所有库信息
     *
     * @return array
     */
    public function getDatabases(): array
    {
        return $this->getObjects('SELECT * FROM master..sysdatabasesWHERE [name]!=\'master\'');
    }

    /**
     * 获取当前连接的所有库名
     *
     * @return array
     */
    public function getDatabaseNames(): array
    {
        return $this->getValues('SELECT [name] FROM master..sysdatabasesWHERE [name]!=\'master\'');
    }

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
    public function getTableFields(string $table): array
    {
        $cacheKey = 'TableFields:' . $table;
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $sql = 'SELECT  
                    a.name,  
                    ISNULL(d.[value], \'\') AS [comment],  
                    b.name AS [type],  
                    a.length AS [length],  
                    ISNULL(COLUMNPROPERTY(a.id, a.name, \'Scale\'), 0) AS [scale],  
                    a.isnullable AS [null_able],
                    c.text AS [default]
                FROM syscolumns a  
                LEFT JOIN systypes b ON a.xtype = b.xusertype  
                LEFT JOIN syscomments c ON a.cdefault = c.id  
                LEFT JOIN sys.extended_properties d ON a.id = d.major_id AND a.colid = d.minor_id AND d.name = \'MS_Description\'  
                WHERE a.id=object_id(\'' . $table . '\')';
        $fields = $this->getObjects($sql);

        $data = [];
        foreach ($fields as $field) {
            $data[$field->name] = [
                'name' => $field->name,
                'type' => $field->type,
                'length' => $field->length,
                'precision' => 0,
                'scale' => $field->scale,
                'comment' => $field->comment,
                'default' => $field->default,
                'nullAble' => $field->null_able ? true : false,
            ];
        }

        $this->cache[$cacheKey] = $data;
        return $data;
    }

    /**
     * 获取指定表的主银
     *
     * @param string $table 表名
     * @return string | array | null
     */
    public function getTablePrimaryKey(string $table)
    {
        $cacheKey = 'TablePrimaryKey:' . $table;
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $sql = 'SELECT COL_NAME(a.parent_obj, c.colid) 
                FROM sysobjects a
                LEFT JOIN sysindexes b ON a.name = b.name
                LEFT JOIN sysindexkeys c ON b.id = c.id AND b.indid = c.indid
                WHERE a.xtype=\'PK\' AND a.parent_obj=OBJECT_ID(\'' . $table . '\')';
        $primaryKeys = $this->getValues($sql);

        $primaryKey = null;
        $count = count($primaryKeys);
        if ($count > 1) {
            $primaryKey = $primaryKeys;
        } elseif ($count === 1) {
            $primaryKey = $primaryKeys[0];
        }

        $this->cache[$cacheKey] = $primaryKey;
        return $primaryKey;
    }

    /**
     * 删除表
     *
     * @param string $table 表名
     */
    public function dropTable(string $table)
    {
        $statement = $this->connection->execute('DROP TABLE ' . $this->quoteKey($table));
        $statement->closeCursor();
    }


}
