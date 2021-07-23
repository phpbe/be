<?php

namespace Be\Session;

/**
 * SESSION 驱动
 */
interface Driver
{

    /**
     * 获取 session id
     *
     * @return string
     */
    public function getId();

    /**
     * 获取 session name
     *
     * @return string
     */
    public function getName();

    /**
     * 获取 session 超时时间
     *
     * @return int
     */
    public function getExpire();

    /**
     * 启动 SESSION
     *
     */
    public function start();

    /**
     * 获取session 值
     *
     * @param string $name 名称
     * @param string $default 默认值
     * @return mixed
     */
    public function get($name = null, $default = null);

    /**
     * 向session中赋值
     *
     * @param string $name 名称
     * @param string $value 值
     */
    public function set($name, $value);


    /**
     * 是否已设置指定名称的 session
     *
     * @param string $name 名称
     * @return bool
     */
    public function has($name);

    /**
     *
     * 删除除指定名称的 session
     * @param string $name 名称
     *
     * @return mixed
     */
    public function delete($name);

}

