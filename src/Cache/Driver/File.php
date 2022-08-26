<?php

namespace Be\Cache\Driver;

use Be\Be;
use Be\Cache\Driver;

/**
 * 缓存驱动
 */
class File extends Driver
{

    private $path = null;

    /**
     * 构造函数
     *
     * @param object $config 配置参数
     */
    public function __construct($config)
    {
        $this->path = Be::getRuntime()->getRootPath() . '/data/cache';
    }

    /**
     * 关闭
     *
     * @return bool
     */
    public function close(): bool
    {
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
        $hash = sha1($key);
        $path = $this->path . '/' . substr($hash, 0, 2) . '/' . substr($hash, 2, 2) . '/' . $hash;

        if (!is_file($path)) return false;

        $content = file_get_contents($path);

        if (false !== $content) {
            $expire = substr($content, 0, 10);
            if (time() > intval($expire)) {
                unlink($path);
                return false;
            }

            $value = substr($content, 10);
            if (!is_bool($value) && !is_numeric($value)) $value = unserialize($value);
            return $value;
        } else {
            return false;
        }
    }

    /**
     * 批量读取
     *
     * @param array $keys 键名 数组
     * @return array
     */
    public function getMany(array $keys): array
    {
        $values = [];
        foreach ($keys as $key) {
            $values[] = $this->get($key);
        }
        return $values;
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
        $hash = sha1($key);
        $dir = $this->path . '/' . substr($hash, 0, 2) . '/' . substr($hash, 2, 2);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }
        $path = $dir . '/' . $hash;

        if (!is_bool($value) && !is_numeric($value)) {
            $value = serialize($value);
        }

        if ($expire === 0) {
            $expire = 9999999999;
        } else {
            $expire = time() + $expire;
            if ($expire > 9999999999) $expire = 9999999999;
        }
        $data = $expire . $value;
        if (!file_put_contents($path, $data)) return false;
        chmod($path, 0777);
        return true;
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
        foreach ($keyValues as $key => $value) {
            $this->set($key, $value, $expire);
        }
        return true;
    }

    /**
     * 设置超时时间
     *
     * @param string $key    键名
     * @param int $expire 有效时间（秒）
     * @return bool
     */
    public function setExpire(string $key, int $expire = 0): bool
    {
        $hash = sha1($key);
        $path = $this->path . '/' . substr($hash, 0, 2) . '/' . substr($hash, 2, 2) . '/' . $hash;

        if (!is_file($path)) {
            return false;
        };

        $content = file_get_contents($path);

        if (false !== $content) {

            if ($expire === 0) {
                $expire = 9999999999;
            } else {
                $expire = time() + $expire;
                if ($expire > 9999999999) $expire = 9999999999;
            }

            $data = $expire . substr($content, 10);
            if (!file_put_contents($path, $data)) return false;
            chmod($path, 0777);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 指定键名的缓存是否存在
     *
     * @param string $key 缓存键名
     * @return bool
     */
    public function has(string $key): bool
    {
        $hash = sha1($key);
        $path = $this->path . '/' . substr($hash, 0, 2) . '/' . substr($hash, 2, 2) . '/' . $hash ;

        return is_file($path) ? true : false;
    }

    /**
     * 删除指定键名的缓存
     *
     * @param string $key 缓存键名
     * @return bool
     */
    public function delete(string $key): bool
    {
        $hash = sha1($key);
        $path = $this->path . '/' . substr($hash, 0, 2) . '/' . substr($hash, 2, 2) . '/' . $hash;
        if (!is_file($path)) return true;
        return unlink($path);
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
        $hash = sha1($key);
        $dir = $this->path . '/' . substr($hash, 0, 2) . '/' . substr($hash, 2, 2);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }
        $path = $dir . '/' . $hash;

        if (!is_file($path)) {
            $value = $step;
            $data = '9999999999' . $value;
            if (!file_put_contents($path, $data)) return false;
            chmod($path, 0777);
            return $value;
        }

        $content = file_get_contents($path);

        if (false !== $content) {
            $expire = substr($content, 0, 10);
            if (time() > intval($expire)) return false;

            $content = substr($content, 10);
            $value = intval($content) + $step;
            $data = $expire . $value;
            if (!file_put_contents($path, $data)) return false;
            chmod($path, 0777);
            return $value;
        } else {
            return false;
        }
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
        $hash = sha1($key);
        $dir = $this->path . '/' . substr($hash, 0, 2) . '/' . substr($hash, 2, 2);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }
        $path = $dir . '/' . $hash;

        if (!is_file($path)) {
            $value = -$step;
            $data = '9999999999' . $value;
            if (!file_put_contents($path, $data)) return false;
            chmod($path, 0777);
            return $value;
        }

        $content = file_get_contents($path);

        if (false !== $content) {
            $expire = substr($content, 0, 10);
            if (time() > intval($expire)) return false;

            $content = substr($content, 10);
            $value = intval($content) - $step;
            $data = $expire . $value;
            if (!file_put_contents($path, $data)) return false;
            chmod($path, 0777);
            return $value;
        } else {
            return false;
        }
    }

    /**
     * 清除缓存
     *
     * @return bool
     */
    public function flush(): bool
    {
        $handle = opendir($this->path);
        while (($file = readdir($handle)) !== false) {
            if ($file !== '.' && $file !== '..') {
                \Be\Util\File\Dir::rm($this->path . '/' . $file);
            }
        }
        closedir($handle);
        return true;
    }


}
