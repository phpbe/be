<?php

namespace Be\AdminPlugin;

use Be\Be;

/**
 * 扩展基类
 */
abstract class Driver
{

    protected ?array $setting = null;

    protected array $events = [];

    /**
     * 监听事件
     * @param string $event 事件名
     * @param callable $callback 回调
     * @return Driver
     */
    public function on(string $event, $callback): Driver
    {
        if (isset($this->events[$event])) {
            if (is_array($this->events[$event])) {
                $this->events[$event][] = $callback;
            } else {
                $this->events[$event] = [$this->events[$event], $callback];
            }
        } else {
            $this->events[$event] = $callback;
        }

        return $this;
    }

    /**
     * 触发事件
     * @param string $event 事件名
     * @param mixed ...$args 事件参数
     * @return Driver
     */
    public function trigger(string $event, ...$args): Driver
    {
        if (isset($this->events[$event])) {
            if (is_array($this->events[$event])) {
                foreach ($this->events[$event] as $callback) {
                    if (is_callable($callback)) {
                        $callback(...$args);
                    }
                }
            } else {
                $callback = $this->events[$event];
                if (is_callable($callback)) {
                    $callback(...$args);
                }
            }
        }

        return $this;
    }

    /**
     * 配置项
     *
     * @param array $setting
     * @return Driver
     */
    public function setting(array $setting = []): Driver
    {
        $this->setting = $setting;
        return $this;
    }

    /**
     * 执行指定任务
     *
     * @param string $task
     */
    public function execute(string $task = null)
    {
        if ($task === null) {
            $task = Be::getRequest()->get('task', 'display');
        }

        if (method_exists($this, $task)) {
            $this->$task();
        }
    }

    /**
     * 默认输出方法
     */
    public function display()
    {

    }

}
