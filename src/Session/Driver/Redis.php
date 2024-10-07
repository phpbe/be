<?php
namespace Be\Session\Driver\Common;

use Be\Be;
use Be\Session\Driver;

/**
 * Redis session
 */
class Redis extends \SessionHandler implements Driver
{

    protected $name = null; // session name
    protected $expire = 1440; // session 超时时间

    /**
     * @var \redis
     */
    private $redis = null;

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
        session_set_save_handler($this);
        session_name($this->name);
        session_start();
    }

    /**
     * 获取session 值
     *
     * @param string $name 名称
     * @param string | array | \stdClass $default 默认值
     * @return string | array | \stdClass
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
     * @param string | array | \stdClass $value 值
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
     * @return string | array | \stdClass
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
	 * 初始化 session
	 *
	 * @param string $savePath 保存路径
	 * @param string $sessionId session id
	 * @return bool
	 */
	public function open($savePath, $sessionId) {
        $this->redis = Be::getRedis(Be::getConfig('App.System.Session')->redis);
		return true;
	}

	/**
	 * 关闭 session
	 *
	 * @return bool
	 */
	public function close() {
		return true;
	}

	/**
	 * 讯取 session 数据
	 *
	 * @param string $sessionId session id
	 * @return string
	 */
	public function read($sessionId) {
        $sessionData = $this->redis->get('be:session:'.$sessionId);
        if (!$sessionData) $sessionData = '';
        return $sessionData;
	}

	/**
	 * 写入 session 数据
	 *
	 * @param string $sessionId session id
	 * @param string $sessionData 写入 session 的数据
	 * @return bool
	 */
	public function write($sessionId, $sessionData) {
		return $this->redis->setex('be:session:'.$sessionId, $this->expire, $sessionData);
	}
	/**
	 * 销毁 session
	 *
	 * @param int $sessionId 要销毁的 session 的 session id
	 * @return bool
	 */
	public function destroy($sessionId) {
		$this->redis->del('be:session:'.$sessionId);
		return true;
	}

	/**
	 * 垃圾回收
	 *
	 * @param int $maxLifetime 最大生存时间
	 * @return bool
	 */
	public function gc($maxLifetime) {
		return true;
	}

    /**
     * 销毁 session
     * @return bool
     */
    public function wipe()
    {
        return session_destroy();
    }

}
