<?php
namespace Be\Session\Driver\Common;

use Be\Be;
use Be\Session\Driver;

/**
 * Session
 */
class File implements Driver
{

    protected $name = null; // session name
    protected $expire = 1440; // session 超时时间

    public function __construct($config)
    {
        $this->name = $config->name;
        $this->expire = $config->expire;
    }

    // 获取 session id
    public function getId()
    {
        return session_id();
    }

    // 获取 session name
    public function getName()
    {
        return $this->name;
    }

    /**
     * 获取 session 超时时间
     *
     * @return int
     */
    public function getExpire()
    {
        return $this->expire;
    }

    // 获取数据库实例
    public function start()
    {
        @ini_set('session.gc_maxlifetime', $this->expire);
        @ini_set('session.cookie_lifetime', $this->expire);

        // 程序意外中断时，关闭 session
        register_shutdown_function('session_write_close');

        session_name($this->name);
        session_start();
    }

    public function close()
    {
        session_write_close();
    }

    /**
     * 获取session 值
     *
     * @param string $name 名称
     * @param string $default 默认值
     * @return mixed
     */
    public function get($name = null, $default = null)
    {
        if ($name === null) return $_SESSION;
        if (isset($_SESSION[$name])) return $_SESSION[$name];
        return $default;
    }

    /**
     * 向session中赋值
     *
     * @param string $name 名称
     * @param string $value 值
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * 是否已设置指定名称的 session
     *
     * @param string $name 名称
     * @return bool
     */
    public function has($name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     *
     * 删除除指定名称的 session
     * @param string $name 名称
     *
     * @return mixed
     */
    public function delete($name)
    {
        $value = null;
        if (isset($_SESSION[$name])) {
            $value = $_SESSION[$name];
            unset($_SESSION[$name]);
        }
        return $value;
    }

    /**
     * 销毁 session
     *
     * @return bool
     */
    public function destroy()
    {
        Be::getResponse()->cookie(session_name(), '', -1);
        session_unset();
        session_destroy();
        return true;
    }
	
}
