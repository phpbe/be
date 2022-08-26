<?php

namespace Be\Cache;

/**
 * 缓存代理
 */
class Proxy
{

    private ?Driver $cache = null;
    private $instance = null;
    private int $expire = 0;

    /**
     * 构造函数
     *
     * @param Driver $cache 缓存类
     * @param mixed $instance 代理对象
     * @param int $expire 超时时间
     */
    public function __construct(Driver $cache, $instance, int $expire)
    {
        $this->cache = $cache;
        $this->instance = $instance;
        $this->expire = $expire;
    }

    public function __call($method, ...$args)
    {
        $key = 'proxy:' . get_class($this->instance). ':' . $method . ':' . md5(serialize($args)) . ':' . $this->expire;
        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $value = $this->instance->$method(...$args);
        $this->cache->set($key, $value, $this->expire);

        return $value;
    }

}
