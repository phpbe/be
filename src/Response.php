<?php

namespace Be;

/**
 * Response 静态快速访问类
 *
 * Class Response
 *
 * @package Be
 * @method static status(int $code = 302, string $message = '') 请求状态
 * @method static redirect(string $url, int $code = 302) 请求重定向
 * @method static set(string $name, $value) 设置暂存数据
 * @method static mixed get(string $name, $default = null) 获取暂存数据
 * @method static header(string $key, string $val) 输出头信息
 * @method static write(string $string) 输出内容
 * @method static json($data = null) 以 JSON 输出暂存数据
 * @method static end(string $string = '') 结束输出
 * @method static cookie(string $key, string $value = '', int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httpOnly = false) 设置 Cookie
 * @method static success(string $message, array $redirect = null) 成功
 * @method static error(string $message, array $redirect = null) 失败
 * @method static exception(\Throwable $e) 系统异常
 * @method static createHistory(string $historyKey = null) 记录历史节点
 * @method static successAndBack(string $message, string $historyKey = null, array $redirect = []) 成功并返回
 * @method static errorAndBack(string $message, string $historyKey = null, array $redirect = null) 错误并返回
 * @method static display(string $template = null, string $theme = null) 显示模板
 * @method static string fetch(string $template, string $theme = null) 获取模板内容
 * @method static null | \Swoole\Http\Response getResponse() 获取原生 Response 对像，仅适用 swoole 模式下
 */
abstract class Response
{
    public static function __callStatic($method, $args)
    {
        return Be::getResponse()->$method(...$args);
    }
}
