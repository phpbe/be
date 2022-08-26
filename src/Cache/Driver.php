<?php
namespace Be\Cache;

/**
 * 缓存驱动
 */
abstract class Driver
{

    /**
     * 构造函数
     *
     * @param object $config 配置参数
     */
    abstract function __construct($config);

    /**
     * 手动关闭
     * @return bool
     */
    abstract function close(): bool;

    /**
     * 读取
     *
     * @param string $key     键名
     * @return mixed
     */
    abstract function get(string $key);

    /**
     * 批量读取
     *
     * @param array $keys    键名 数组
     * @return array
     */
    abstract function getMany(array $keys): array;

    /**
     * 写入
     *
     * @param string $key    键名
     * @param mixed  $value  值
     * @param int    $expire 有效时间（秒）
     * @return bool
     */
    abstract function set(string $key, $value, int $expire = 0): bool;

    /**
     * 批量写入
     *
     * @param array $keyValues 键值对
     * @param int   $expire 有效时间（秒）
     * @return bool
     */
    abstract function setMany(array $keyValues, int $expire = 0): bool;

    /**
     * 设置超时时间
     *
     * @param string $key    键名
     * @param int $expire 有效时间（秒）
     * @return bool
     */
    abstract function setExpire(string $key, int $expire = 0): bool;

    /**
     * 指定键名的缓存是否存在
     *
     * @param string $key 缓存键名
     * @return bool
     */
    abstract function has(string $key): bool;

    /**
     * 删除指定键名的缓存
     *
     * @param string $key 缓存键名
     * @return bool
     */
    abstract function delete(string $key): bool;

    /**
     * 自增缓存（针对数值缓存）
     *
     * @param string $key  缓存变量名
     * @param int    $step 步长
     * @return false|int
     */
    abstract function increase(string $key, int $step = 1);

    /**
     * 自减缓存（针对数值缓存）
     *
     * @param string $key  缓存变量名
     * @param int    $step 步长
     * @return false|int
     */
    abstract function decrease(string $key, int $step = 1);

    /**
     * 清除缓存
     *
     * @return bool
     */
    abstract function flush(): bool;

    /**
     * 缓存代理
     *
     * @param mixed $instance 代理对象
     * @param int $expire 超时时间
     * @return mixed
     */
    public function proxy($instance, int $expire = 0): Proxy
    {
        return new Proxy($this, $instance, $expire);
    }


}
