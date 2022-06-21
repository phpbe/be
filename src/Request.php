<?php

namespace Be;

/**
 * Request 静态快速访问类
 *
 * Class Request
 *
 * @package Be
 * @method static array|mixed|string get(string $name = null, $default = null, $format = 'string') 获取 $_GET 数据
 * @method static array|mixed|string post(string $name = null, $default = null, $format = 'string') 获取 $_POST 数据
 * @method static array|mixed|string request(string $name = null, $default = null, $format = 'string') 获取 $_REQUEST 数据
 * @method static array|mixed|string json(string $name = null, $default = null, $format = null) 获取 ajax 请求发送的 JSON 数据
 * @method static array|mixed|string server(string $name = null, $default = null, $format = 'string') 获取 $_SERVER 数据
 * @method static array|mixed|string cookie(string $name = null, $default = null, $format = 'string') 获取 $_COOKIE 数据
 * @method static array|null files(string $name = null) 获取上传的文件
 * @method static bool isGet() 是否GET请求
 * @method static bool isPost() 是否POST请求
 * @method static bool isAjax() 是否AJAX请求
 * @method static bool isMobile() 是否通过手机访问
 * @method static string getMethod() 获取请求方法
 * @method static string getUrl() 获取请求网址
 * @method static string getIp(bool $detectProxy = true) 获取请求者的 IP 地址
 * @method static string getRootUrl() 获取当前请求的完整网址
 * @method static string getReferer() 获取来源网址
 * @method static bool isAdmin() 获取当前执行的是否后台功能
 * @method static null | string getAppName() 获取当前执行的 APP 名
 * @method static null | string getControllerName() 获取当前执行的 控制器 名
 * @method static null | string getActionName() 获取当前执行的 动作 名
 * @method static null | string getRoute() 获取当前执行的 路径（应用名.控制器名.动作名）
 * @method static setRoute(string $appName, string $controllerName, string $actionName) 设置当前路径
 * @method static setAdmin(bool $admin) 设置是否后台
 * @method static null | \Swoole\Http\Request getRequest() 获取原生 Request 对像，仅适用 swoole 模式下
 */
abstract class Request
{
    public static function __callStatic($method, $args)
    {
        return Be::getRequest()->$method(...$args);
    }
}
