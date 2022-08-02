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
    abstract function close();

    /**
     * 获取 指定的缓存 值
     *
     * @param string $key     键名
     * @return mixed
     */
    abstract function get($key);

    /**
     * 获取 多个指定的缓存 值
     *
     * @param array $keys    键名 数组
     * @return array
     */
    abstract function getMany($keys);

    /**
     * 设置缓存
     *
     * @param string $key    键名
     * @param mixed  $value  值
     * @param int    $expire 有效时间（秒）
     * @return bool
     */
    abstract function set($key, $value, $expire = 0): bool;

    /**
     * 设置缓存
     *
     * @param array $values 键值对
     * @param int   $expire 有效时间（秒）
     * @return bool
     */
    abstract function setMany($values, $expire = 0): bool;

    /**
     * 设置超时时间
     *
     * @param string $key    键名
     * @param int $expire 有效时间（秒）
     * @return bool
     */
    abstract function setExpire($key, $expire = 0): bool;

    /**
     * 指定键名的缓存是否存在
     *
     * @param string $key 缓存键名
     * @return bool
     */
    abstract function has($key): bool;

    /**
     * 删除指定键名的缓存
     *
     * @param string $key 缓存键名
     * @return bool
     */
    abstract function delete($key): bool;

    /**
     * 自增缓存（针对数值缓存）
     *
     * @param string $key  缓存变量名
     * @param int    $step 步长
     * @return false|int
     */
    abstract function increment($key, $step = 1);

    /**
     * 自减缓存（针对数值缓存）
     *
     * @param string $key  缓存变量名
     * @param int    $step 步长
     * @return false|int
     */
    abstract function decrement($key, $step = 1);

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
