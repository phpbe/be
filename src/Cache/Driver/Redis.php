<?php

namespace Be\Cache\Driver;

use Be\Be;
use Be\Cache\Driver;
use Be\Cache\Proxy;

/**
 * 缓存驱动
 */
class Redis extends Driver
{

    /**
     * @var \Redis
     */
    private $redis = null;

    /**
     * 构造函数
     *
     * @param object $config 配置参数
     */
    public function __construct($config)
    {
        $this->redis = Be::getRedis($config->redis);
    }

    /**
     * 关闭
     *
     * @return bool
     */
    public function close(): bool
    {
        $this->redis = null;
        return true;
    }

    /**
     * 读取
     *
     * @param string $key 键名
     * @return mixed|false
     */
    public function get(string $key)
    {
        $value = $this->redis->get('be:cache:' . $key);
        if (is_bool($value) || is_numeric($value)) return $value;
        return unserialize($value);
    }

    /**
     * 批量读取
     *
     * @param array $keys 键名 数组
     * @return array
     */
    public function getMany(array $keys): array
    {
        $prefixedKeys = [];
        foreach ($keys as $key) {
            $prefixedKeys[] = 'be:cache:' . $key;
        }

        $return = [];
        $values = $this->redis->mget($prefixedKeys);
        foreach ($values as $index => $value) {
            if (!is_bool($value) && !is_numeric($value)) {
                $value = unserialize($value);
            }
            $return[] = $value;
        }

        return $return;
    }

    /**
     * 写入
     *
     * @param string $key 键名
     * @param mixed $value 值
     * @param int $expire 有效时间（秒）
     * @return bool
     */
    public function set(string $key, $value, int $expire = 0): bool
    {
        if (!is_bool($value) && !is_numeric($value)) {
            $value = serialize($value);
        }

        if ($expire > 0) {
            return $this->redis->setex('be:cache:' . $key, $expire, $value);
        } else {
            return $this->redis->set('be:cache:' . $key, $value);
        }
    }

    /**
     * 批量写入
     *
     * @param array $values 键值对
     * @param int $expire 有效时间（秒）
     * @return bool
     */
    public function setMany(array $keyValues, int $expire = 0): bool
    {
        $formattedValues = array();
        foreach ($keyValues as $key => $value) {
            if (!is_bool($value) && !is_numeric($value)) {
                $formattedValues['be:cache:' . $key] = serialize($value);
            } else {
                $formattedValues['be:cache:' . $key] = $value;
            }
        }

        if ($expire > 0) {
            $this->redis->multi(); // 开启事务
            $this->redis->mset($formattedValues);
            foreach ($formattedValues as $key => $val) {
                $this->redis->expire($key, $expire);
            }
            $this->redis->exec();
            return true;
        } else {
            return $this->redis->mset($formattedValues);
        }
    }

    /**
     * 设置超时时间
     *
     * @param string $key    键名
     * @param int $expire 有效时间（秒）
     * @return bool
     */
    public function setExpire(string $key,  int $expire = 0): bool
    {
        return $this->redis->expire('be:cache:' . $key, $expire);
    }

    /**
     * 指定键名的缓存是否存在
     *
     * @param string $key 缓存键名
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->redis->exists('be:cache:' . $key) ? true : false;
    }

    /**
     * 删除指定键名的缓存
     *
     * @param string $key 缓存键名
     * @return bool
     */
    public function delete(string $key): bool
    {
        return $this->redis->del('be:cache:' . $key);
    }

    /**
     * 自增缓存（针对数值缓存）
     *
     * @param string $key 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    public function increase(string $key, int $step = 1)
    {
        return $this->redis->incrby('be:cache:' . $key, $step);
    }

    /**
     * 自减缓存（针对数值缓存）
     *
     * @param string $key 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    public function decrease(string $key, int $step = 1)
    {
        return $this->redis->decrby('be:cache:' . $key, $step);
    }

    /**
     * 清除缓存
     *
     * @return bool
     */
    public function flush(): bool
    {
        return true;
    }


}
