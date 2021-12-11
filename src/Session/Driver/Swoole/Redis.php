<?php

namespace Be\Session\Driver\Swoole;

use Be\Be;

/**
 * Session
 */
class Redis extends Driver
{

    /**
     * @var \redis
     */
    private $redis = null;

    public function read()
    {
        if ($this->data === null) {
            $this->redis = Be::getRedis(Be::getConfig('App.System.Session')->redis);
            $data = $this->redis->get('be:session:' . $this->id);
            if ($data) {
                $data = unserialize($data);
            } else {
                $data = [];
            }
            $this->data = $data;
        }
    }

    public function write()
    {
        if ($this->data !== null) {
            $this->redis->setex('be:session:' . $this->id, $this->expire, serialize($this->data));
        }
    }

    public function close()
    {
        $this->write();
        $this->redis = null;
        $this->data = null;
    }

    /**
     * 销毁 session
     *
     * @return bool
     */
    public function wipe()
    {
        $this->data = null;
        if ($this->redis === null) {
            $this->redis = Be::getRedis(Be::getConfig('App.System.Session')->redis);
        }
        return $this->redis->del('be:session:' . $this->id);
    }

}
