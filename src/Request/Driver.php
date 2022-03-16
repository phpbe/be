<?php

namespace Be\Request;

use Be\Be;

/**
 * Class Driver
 * @package Be\Request
 */
abstract class Driver
{
    protected $admin = false;
    protected $appName = null;
    protected $controllerName = null;
    protected $actionName = null;
    protected $route = null;

    protected $json = null;

    protected $url = null;
    protected $rootUrl = null;

    /**
     * 获取 header 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    abstract function header(string $name = null, $default = null, $format = 'string');

    /**
     * 获取 server 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    abstract function server(string $name = null, $default = null, $format = 'string');

    /**
     * 获取 get 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    abstract function get(string $name = null, $default = null, $format = 'string');

    /**
     * 获取 post 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    abstract function post(string $name = null, $default = null, $format = 'string');

    /**
     * 获取 request 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    abstract function request(string $name = null, $default = null, $format = 'string');

    /**
     * 获取 ajax 请求发送的 JSON 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    abstract function json(string $name = null, $default = null, $format = null);

    /**
     * 获取请求的原始数据
     *
     * @return string
     */
    abstract function getInput(): string;

    /**
     * 获取 cookie 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    abstract function cookie(string $name = null, $default = null, $format = 'string');

    /**
     * 获取上传的文件
     * @param string|null $name 参数量
     * @return array|null
     */
    abstract function files(string $name = null);


    protected function _request($input, $name, $default, $format)
    {
        if ($name === null) {
            if ($format instanceof \Closure) {
                $input = $this->formatByClosure($input, $format);
            } else {
                if ($format) {
                    $fnFormat = 'format' . ucfirst($format);
                    $input = $this->$fnFormat($input);
                }
            }

            return $input;
        }

        $value = null;
        if (strpos($name, '.') === false) {
            if (!isset($input[$name])) return $default;
            $value = $input[$name];
        } else {
            $tmpValue = $input;
            $names = explode('.', $name);
            foreach ($names as $x) {
                if (!isset($tmpValue[$x])) return $default;
                $tmpValue = $tmpValue[$x];
            }
            $value = $tmpValue;
        }

        if ($format instanceof \Closure) {
            return $this->formatByClosure($value, $format);
        } else {
            if ($format) {
                $fnFormat = 'format' . ucfirst($format);
                return $this->$fnFormat($value);
            } else {
                return $value;
            }
        }
    }

    protected function formatInt($value)
    {
        return is_array($value) ? array_map([$this, 'formatInt'], $value) : intval($value);
    }

    protected function formatFloat($value)
    {
        return is_array($value) ? array_map([$this, 'formatFloat'], $value) : floatval($value);
    }

    protected function formatBool($value)
    {
        return is_array($value) ? array_map([$this, 'formatBool'], $value) : boolval($value);
    }

    protected function formatString($value)
    {
        return is_array($value) ? array_map([$this, 'formatString'], $value) : htmlspecialchars($value);
    }

    // 过滤  脚本,样式，框架
    protected function formatHtml($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'formatHtml'], $value);
        } else {
            $value = preg_replace("@<script(.*?)</script>@is", '', $value);
            $value = preg_replace("@<style(.*?)</style>@is", '', $value);
            $value = preg_replace("@<iframe(.*?)</iframe>@is", '', $value);

            return $value;
        }
    }

    /**
     * 格式化 IP
     * @param $value
     * @return array|string
     */
    protected function formatIp($value)
    {
        if (is_array($value)) {
            $returns = [];
            foreach ($value as $v) {
                $returns[] = $this->formatIp($v);
            }
            return $returns;
        } else {
            if (filter_var($value, FILTER_VALIDATE_IP)) {
                return $value;
            } else {
                return 'invalid';
            }
        }
    }

    protected function formatByClosure($value, \Closure $closure)
    {
        if (is_array($value)) {
            $returns = [];
            foreach ($value as $v) {
                $returns[] = $this->formatByClosure($v, $closure);
            }
            return $returns;
        } else {
            return $closure($value);
        }
    }

    /**
     * 是否GET请求
     *
     * @return bool
     */
    abstract function isGet(): bool;

    /**
     * 是否POST请求
     *
     * @return bool
     */
    abstract function isPost(): bool;

    /**
     * 是否AJAX请求
     *
     * @return bool
     */
    abstract function isAjax(): bool;

    /**
     * 是否通过手机访问
     *
     * @return bool
     */
    abstract function isMobile(): bool;

    /**
     * 获取当前请求的方法类型
     *
     * @return string 方法类型 GET / POST / ...
     */
    abstract function getMethod(): string;

    /**
     * 获取当前请求的通讯协议
     *
     * @return string 通讯协议
     */
    abstract function getScheme(): string;

    /**
     * 获取当前请求的域名，即服务器名 server name
     *
     * @return string 域名
     */
    abstract function getDomain(): string;

    /**
     * 获取当前请求的主机名，包含端品号
     *
     * @return string 主机名
     */
    abstract function getHost(): string;

    /**
     * 获取当前请求的主机，包含端品号
     *
     * @return int 端口号
     */
    abstract function getPort(): int;


    /**
     * 获取请求者的 IP 地址
     *
     * @return string
     */
    abstract function getIp(bool $detectProxy = true): string;

    /**
     * 获取当前请求的完整网址
     *
     * @return string 网址
     */
    abstract function getUrl(): string;

    /**
     * 获取当前请求的根网址
     *
     * @return string 请求的根网址
     */
    abstract function getRootUrl(): string;

    /**
     * 获取upload上传目录的网址
     *
     * @return string 上传目录的网址
     */
    public function getUploadUrl(): string
    {
        return $this->getRootUrl() . '/' . Be::getRuntime()->getUploadDir();
    }

    /**
     * 获取来源网址
     *
     * @return string 来源网址
     */
    abstract function getReferer(): string;

    /**
     * 获取当前执行的是否后台功能
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->admin;
    }

    /**
     * 获取当前执行的 APP 名
     *
     * @return null | string
     */
    public function getAppName(): string
    {
        return $this->appName;
    }

    /**
     * 获取当前执行的 控制器 名
     *
     * @return null | string
     */
    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    /**
     * 获取当前执行的 动作 名
     *
     * @return null | string
     */
    public function getActionName(): string
    {
        return $this->actionName;
    }

    /**
     * 获取当前执行的 路径（应用名.控制器名.动作名）
     *
     * @return null | string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * 设置当前路径
     *
     * @param string $appName 应用名
     * @param string $controllerName 控制器名
     * @param string $actionName 动作名
     */
    public function setRoute(string $appName, string $controllerName, string $actionName)
    {
        $this->appName = $appName;
        $this->controllerName = $controllerName;
        $this->actionName = $actionName;
        $this->route = $appName . '.' . $controllerName . '.' . $actionName;
    }

    /**
     * 设置是否后台
     *
     * @param bool $admin 是否后台
     */
    public function setAdmin(bool $admin)
    {
        $this->admin = $admin;
    }

    /**
     * 获取原生 Request 对像，仅适用 swoole 模式下
     *
     * @return null | \Swoole\Http\Request
     */
    public function getRequest()
    {
        return null;
    }

}

