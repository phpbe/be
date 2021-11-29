<?php

namespace Be\User;

use Be\Be;

class User
{
    public $id = 0;

    /**
     * User constructor.
     * @param null | object $user
     */
    public function __construct($user = null)
    {
        if ($user && is_object($user)) {
            $vars = get_object_vars($user);
            foreach ($vars as $key => $val) {
                $this->$key = $val;
            }
        }
    }

    /**
     * 是否游客（未登录）
     *
     * @return bool
     */
    public function isGuest() {
        return !$this->id;
    }

}

