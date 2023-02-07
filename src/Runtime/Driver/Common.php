<?php

namespace Be\Runtime\Driver;

use Be\Be;
use Be\Runtime\Driver;

/**
 *  运行时
 */
class Common extends Driver
{
    protected $mode = 'Common'; // 运行模式 Swoole / Common

    public function execute()
    {
        // 检查网站配置， 是否暂停服务
        $configSystem = Be::getConfig('App.System.System');

        // 默认时区
        date_default_timezone_set($configSystem->timezone);

        if ($configSystem->developer === 0) {
            error_reporting(0);
        }

        $request = new \Be\Request\Driver\Common();
        $response = new \Be\Response\Driver\Common();
        Be::setRequest($request);
        Be::setResponse($response);

        // 启动 session
        $session = Be::getSession();
        $session->start();

        try {
            $admin = $request->get($this->adminAlias, false);
            if ($admin !== false) {
                $admin = true;
            }

            $app = null;
            $controller = null;
            $action = null;

            // 从网址中提取出 路径
            if ($configSystem->urlRewrite !== 'disable') {
                /*
                 * REQUEST_URI 可能值为：[/path]/{app}/{controller}/{action}[/{k-v}].html?[k=v]
                 * 需要解析的有效部分为： {app}/{controller}/{action}[/{k-v}]
                 */
                $uri = $_SERVER['REQUEST_URI'];    // 返回值为:

                // 移除 [/path]
                $scriptName = $_SERVER['SCRIPT_NAME'];
                $indexName = '/index.php';
                $pos = strrpos($scriptName, $indexName);
                if ($pos !== false) {
                    $path = substr($scriptName, 0, $pos);
                    if ($path) {
                        if (strpos($uri, $path) === 0) {
                            $uri = substr($uri, strlen($path));
                        }
                    }
                }

                // 移除 ?[k=v]
                $pos = strpos($uri, '?');
                if ($pos !== false) {
                    $uri = substr($uri, 0, $pos);
                }

                // 移除网址后缀 如：.html
                if ($configSystem->urlRewrite === 'simple') {
                    $lenSefSuffix = strlen($configSystem->urlSuffix);
                    if ($lenSefSuffix > 0 && substr($uri, -$lenSefSuffix, $lenSefSuffix) === $configSystem->urlSuffix) {
                        $uri = substr($uri, 0, strrpos($uri, $configSystem->urlSuffix));
                    }
                }

                // 移除结尾的 /
                //if (substr($uri, -1, 1) === '/') $uri = substr($uri, 0, -1);

                // 是否后台功能
                if (substr($uri, 0, strlen($this->adminAlias) + 1) === '/' . $this->adminAlias) {
                    $admin = true;
                    $uri = substr($uri, strlen($this->adminAlias) + 1);
                }

                $routeParsed = false;
                if (!$admin && $configSystem->urlRewrite === 'router') {

                    if ($uri === '/') {
                        $route = $request->get('route', '');
                        if ($route) {
                            $routes = explode('.', $route);
                            if (count($routes) === 3) {
                                $app = $routes[0];
                                $controller = $routes[1];
                                $action = $routes[2];
                                $routeParsed = true;
                            }
                        }
                    }

                    if (!$routeParsed) {
                        $decodedRoute = \Be\Router\Helper::decode($uri);
                        if ($decodedRoute) {
                            $routes = explode('.', $decodedRoute[0]);
                            $app = $routes[0] ?? '';
                            $controller = $routes[1] ?? '';
                            $action = $routes[2] ?? '';

                            if (isset($decodedRoute[1])) {
                                foreach ($decodedRoute[1] as $key => $val) {
                                    $_GET[$key] = $val;
                                }
                            }

                            $routeParsed = true;
                        }
                    }
                }

                if (!$routeParsed) {
                    // /{app}/{controller}/{action}[/{k-v}]
                    $uris = explode('/', $uri);
                    $len = count($uris);
                    if ($len > 3) {
                        $app = $uris[1];
                        $controller = $uris[2];
                        $action = $uris[3];
                    }

                    if ($len > 4) {
                        /**
                         * 把网址按以下规则匹配
                         * /{app}/{controller}/{action}/{参数名1}-{参数值1}/{参数名2}-{参数值2}/{参数名3}-{参数值3}
                         * 其中{参数名}-{参数值} 值对不限数量
                         */
                        for ($i = 4; $i < $len; $i++) {
                            $pos = strpos($uris[$i], '-');
                            if ($pos !== false) {
                                $key = substr($uris[$i], 0, $pos);
                                $val = substr($uris[$i], $pos + 1);

                                $_GET[$key] = $val;
                            }
                        }
                    }
                }
            }

            if ($admin) $request->setAdmin($admin);

            // 默认访问控制台页面
            if (!$app) {
                if ($admin) {
                    $route = $request->get('route', Be::getConfig('App.System.Admin')->home);
                } else {
                    $route = $request->get('route', $configSystem->home);
                }
                $routes = explode('.', $route);
                if (count($routes) === 3) {
                    $app = $routes[0];
                    $controller = $routes[1];
                    $action = $routes[2];
                } else {
                    $response->set('code', 404);
                    $response->error(beLang('App.System', 'RUNTIME.ROUTE_ERROR', $route));
                    Be::gc();
                    return;
                }
            }

            $request->setRoute($app, $controller, $action);

            $class = 'Be\\App\\' . $app . '\\Controller\\' . ($admin ? 'Admin\\' : '') . $controller;
            if (!class_exists($class)) {
                $response->set('code', 404);
                $response->error(beLang('App.System', 'RUNTIME.CONTROLLER_DOES_NOT_EXIST', $app, $controller));
            } else {
                $instance = new $class();
                if (method_exists($instance, $action)) {
                    $instance->$action();
                } else {
                    $response->set('code', 404);
                    $response->error(beLang('App.System', 'RUNTIME.UNDEFINED_ACTION', $action, $class));
                }
            }

        } catch (\Throwable $t) {
            if ($t instanceof \Be\Exception) {
                /**
                 * @var \Be\Exception $t
                 */
                $code = $t->getCode();
                if ($code !== 0) {
                    $logId = Be::getLog()->fatal($t);
                    $response->set('logId', $logId);
                    $response->set('code', $t->getCode());
                }
                $response->error($t->getMessage(), $t->getRedirect());
            } else {
                $logId = Be::getLog()->fatal($t);
                $response->set('logId', $logId);
                $response->exception($t);
            }
        }

        Be::gc();
    }
}
