<?php

namespace Be;

/**
 * Request 静态快速访问类
 *
 * Class Request
 *
 * @package Be

 */
class Request
{

    protected static $admin = false;
    protected static $appName = null;
    protected static $controllerName = null;
    protected static $actionName = null;
    protected static $route = null;
    protected static $themeName = null;
    protected static $languageName = null;

    protected static $json = null;

    protected static $url = null;
    protected static $rootUrl = null;


    /**
     * 获取 header 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    public static function header(string $name = null, $default = null, $format = 'string')
    {
        if ($name === null) {
            $headers = [];
            foreach ($_SERVER as $key => $val) {
                if (substr($key, 0, 5) === 'HTTP_') {
                    $key = substr($key, 5);
                    $key = strtolower($key);
                    $key = str_replace('_', '-', $key);
                    $headers[$key] = $val;
                }
            }
            return self::_request($headers, $name, $default, $format);
        } else {
            $name = str_replace('-', '_', $name);
            $name = strtoupper($name);
            $name = 'HTTP_' . $name;
            return self::_request($_SERVER, $name, $default, $format);
        }
    }

    /**
     * 获取 server 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    public static function server(string $name = null, $default = null, $format = 'string')
    {
        return self::_request($_SERVER, $name, $default, $format);
    }

    /**
     * 获取 get 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    public static function get(string $name = null, $default = null, $format = 'string')
    {
        return self::_request($_GET, $name, $default, $format);
    }

    /**
     * 获取 post 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    public static function post(string $name = null, $default = null, $format = 'string')
    {
        return self::_request($_POST, $name, $default, $format);
    }

    /**
     * 获取 request 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    public static function request(string $name = null, $default = null, $format = 'string')
    {
        return self::_request($_REQUEST, $name, $default, $format);
    }

    /**
     * 获取请求体中的 JSON 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    public static function json(string $name = null, $default = null, $format = null)
    {
        if (self::$json === null) {
            if (isset($_SERVER['CONTENT_TYPE']) && strpos(strtolower($_SERVER['CONTENT_TYPE']), 'application/json') !== false) {
                $json = file_get_contents('php://input');
                $json = json_decode($json, true);
                if ($json) {
                    self::$json = $json;
                }
            }
        }

        return self::_request(self::$json, $name, $default, $format);
    }

    /**
     * 获取请求体中的原始数据
     *
     * @return string
     */
    public static function getInput(): string
    {
        return file_get_contents('php://input');
    }

    /**
     * 获取 cookie 数据
     * @param string $name 参数量
     * @param mixed $default 默认值
     * @param string|\Closure $format 格式化
     * @return array|mixed|string
     */
    public static function cookie(string $name = null, $default = null, $format = 'string')
    {
        return self::_request($_COOKIE, $name, $default, $format);
    }

    /**
     * 获取上传的文件
     * @param string $name 参数量
     * @return array|null
     */
    public static function files(string $name = null)
    {
        if ($name === null) {
            return $_FILES;
        }

        if (!isset($_FILES[$name])) return null;

        return $_FILES[$name];
    }

    /**
     * 是否GET请求
     *
     * @return bool
     */
    public static function isGet(): bool
    {
        return 'GET' === $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 是否POST请求
     *
     * @return bool
     */
    public static function isPost(): bool
    {
        return 'POST' === $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 是否AJAX请求
     *
     * @return bool
     */
    public static function isAjax(): bool
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'XMLHTTPREQUEST' === strtoupper($_SERVER['HTTP_X_REQUESTED_WITH'])) ||
            (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/json') !== false);
    }

    /**
     * 是否通过手机访问
     *
     * @return bool
     */
    public static function isMobile(): bool
    {
        if (isset($_GET['be-is-mobile'])) {
            return (bool)$_GET['be-is-mobile'];
        }

        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取当前请求的方法类型
     *
     * @return string 方法类型 GET / POST / ...
     */
    public static function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 获取当前请求的通讯协议
     *
     * @return string 通讯协议
     */
    public static function getScheme(): string
    {
        if (isset($_SERVER['HTTP_SCHEME']) && ($_SERVER['HTTP_SCHEME'] === 'http' || $_SERVER['HTTP_SCHEME'] === 'https')) {
            return $_SERVER['HTTP_SCHEME'];
        } else {
            return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        }
    }

    /**
     * 获取当前请求的域名，即服务器名 server name
     *
     * @return string 域名
     */
    public static function getDomain(): string
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * 获取当前请求的主机名，包含端口号
     *
     * @return string 主机名
     */
    public static function getHost(): string
    {
        return isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT']));
    }

    /**
     * 获取当前请求的端口号
     *
     * @return int 端口号
     */
    public static function getPort(): int
    {
        return $_SERVER['SERVER_PORT'] ?? 80;
    }

    /**
     * 获取访问者的 IP 地址
     *
     * @return string
     */
    public static function getIp(bool $detectProxy = true): string
    {
        if ($detectProxy) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $xForwardedFor = $_SERVER['HTTP_X_FORWARDED_FOR'];

                $ip = null;
                $pos = strpos($xForwardedFor, ',');
                if ($pos === false) {
                    $ip = $xForwardedFor;
                } else {
                    $ip = substr($xForwardedFor, 0, $pos);
                }

                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * 获取当前访问的完整网址
     *
     * @return string 网址
     */
    public static function getUrl(): string
    {
        if (self::url === null) {
            $configSystem = Be::getConfig('App.System.System');
            if ($configSystem->rootUrl !== '') {
                $url = $configSystem->rootUrl;
            } else {
                if (isset($_SERVER['HTTP_SCHEME']) && ($_SERVER['HTTP_SCHEME'] === 'http' || $_SERVER['HTTP_SCHEME'] === 'https')) {
                    $url = $_SERVER['HTTP_SCHEME'] . '://';
                } else {
                    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
                }

                $url .= isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT']));
            }

            $url .= $_SERVER['REQUEST_URI'];
            self::url = $url;
        }

        return self::url;
    }

    /**
     * 获取当前访问的根网址
     *
     * @return string 访问的根网址
     */
    public static function getRootUrl(): string
    {
        if (self::rootUrl === null) {
            $configSystem = Be::getConfig('App.System.System');
            if ($configSystem->rootUrl !== '') {
                $rootUrl = $configSystem->rootUrl;
            } else {
                if (isset($_SERVER['HTTP_SCHEME']) && ($_SERVER['HTTP_SCHEME'] === 'http' || $_SERVER['HTTP_SCHEME'] === 'https')) {
                    $rootUrl = $_SERVER['HTTP_SCHEME'] . '://';
                } else {
                    $rootUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
                }

                $rootUrl .= isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT']));

                $scriptName = $_SERVER['SCRIPT_NAME'];
                $indexName = '/index.php';
                $pos = strrpos($scriptName, $indexName);
                if ($pos !== false) {
                    $path = substr($scriptName, 0, $pos);
                    if ($path) {
                        $rootUrl .= $path;
                    }
                }
            }

            self::rootUrl = $rootUrl;
        }

        return self::rootUrl;
    }

    /**
     * 获取来源网址
     *
     * @return string 来源网址
     */
    public static function getReferer(): string
    {
        return $_SERVER['HTTP_REFERER'] ?? '';
    }
    
    
    

    protected static function _request($input, $name, $default, $format)
    {
        if ($name === null) {
            if ($format instanceof \Closure) {
                $input = self::formatByClosure($input, $format);
            } else {
                if ($format) {
                    $fnFormat = 'format' . ucfirst($format);
                    $input = self::$fnFormat($input);
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
            return self::formatByClosure($value, $format);
        } else {
            if ($format) {
                $fnFormat = 'format' . ucfirst($format);
                return self::$fnFormat($value);
            } else {
                return $value;
            }
        }
    }

    protected static function formatInt($value)
    {
        return is_array($value) ? array_map([self, 'formatInt'], $value) : intval($value);
    }

    protected static function formatFloat($value)
    {
        return is_array($value) ? array_map([self, 'formatFloat'], $value) : floatval($value);
    }

    protected static function formatBool($value)
    {
        return is_array($value) ? array_map([self, 'formatBool'], $value) : boolval($value);
    }

    protected static function formatString($value)
    {
        return is_array($value) ? array_map([self, 'formatString'], $value) : htmlspecialchars($value);
    }

    // 过滤  脚本,样式，框架
    protected static function formatHtml($value)
    {
        if (is_array($value)) {
            return array_map([self, 'formatHtml'], $value);
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
    protected static function formatIp($value)
    {
        if (is_array($value)) {
            $returns = [];
            foreach ($value as $v) {
                $returns[] = self::formatIp($v);
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

    protected static function formatByClosure($value, \Closure $closure)
    {
        if (is_array($value)) {
            $returns = [];
            foreach ($value as $v) {
                $returns[] = self::formatByClosure($v, $closure);
            }
            return $returns;
        } else {
            return $closure($value);
        }
    }

    
    /**
     * 获取当前执行的是否后台功能
     *
     * @return bool
     */
    public static function isAdmin(): bool
    {
        return self::admin;
    }

    /**
     * 获取当前执行的 APP 名
     *
     * @return null | string
     */
    public static function getAppName(): string
    {
        return self::appName;
    }

    /**
     * 获取当前执行的 控制器 名
     *
     * @return null | string
     */
    public static function getControllerName(): string
    {
        return self::controllerName;
    }

    /**
     * 获取当前执行的 动作 名
     *
     * @return null | string
     */
    public static function getActionName(): string
    {
        return self::actionName;
    }

    /**
     * 获取当前执行的 路径（应用名.控制器名.动作名）
     *
     * @return null | string
     */
    public static function getRoute()
    {
        return self::route;
    }

    /**
     * 设置当前路径
     *
     * @param string $appName 应用名
     * @param string $controllerName 控制器名
     * @param string $actionName 动作名
     */
    public static function setRoute(string $appName, string $controllerName, string $actionName)
    {
        self::appName = $appName;
        self::controllerName = $controllerName;
        self::actionName = $actionName;
        self::route = $appName . '.' . $controllerName . '.' . $actionName;
    }

    /**
     * 设置是否后台
     *
     * @param bool $admin 是否后台
     */
    public static function setAdmin(bool $admin)
    {
        self::admin = $admin;
    }

    /**
     * 获取当前请求生效的主题
     *
     * @return string
     */
    public static function getThemeName(): string
    {
        if (self::themeName === null) {
            $themeName = self::get('be-theme', false);
            if ($themeName) {
                self::themeName = $themeName;
            } else {
                if (self::admin) {
                    self::themeName = Be::getConfig('App.System.AdminTheme')->default;
                } else {
                    self::themeName = Be::getConfig('App.System.Theme')->default;
                }
            }
        }

        return self::themeName;
    }

    /**
     * 获取语言名称
     *
     * @return string
     */
    public static function getLanguageName(): string
    {
        if (self::languageName === null) {
            $languageName = self::get('be-language', false);
            if ($languageName) {
                self::languageName = $languageName;
            } else {
                $languageName = self::cookie('be-language', false);
                if ($languageName) {
                    self::languageName = $languageName;
                } else {
                    $configLanguage = Be::getConfig('App.System.Language');
                    if ($configLanguage->autoDetect === 1) {
                        $acceptLanguage = strtolower(self::header('accept-language'));
                        foreach ($configLanguage->language as $x) {
                            if (strpos($acceptLanguage, strtolower($x)) !== false) {
                                $languageName = $x;
                                break;
                            }
                        }
                    }

                    if (!$languageName) {
                        self::languageName = $configLanguage->default;
                    }
                }
            }
        }

        return self::languageName;
    }

}
