<?php
namespace Be\Db;

/**
 * Class TableProperty
 * @package \Be\Db
 */
class TableProperty
{
    /**
     * 数据库名
     *
     * @var string
     */
    protected string $_dbName = 'master';
    /**
     * 表名
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
     * 字段明细列表
     *
     * @var array
     */
    protected array $_fields = [];

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
     * 获取字段明细列表
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->_fields;
    }

    /**
     * 获取指定字段
     *
     * @param string $fieldName 字段名
     * @return array
     */
    public function getField(string $fieldName)
    {
        return isset($this->_fields[$fieldName]) ? $this->_fields[$fieldName] : null;
    }

    /**
     * 是否存在指定字段
     *
     * @param string $fieldName 字段名
     * @return bool
     */
    public function hasField(string $fieldName): bool
    {
        return isset($this->_fields[$fieldName]);
    }

}
