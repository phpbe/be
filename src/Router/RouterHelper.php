<?php

namespace Be\Router;

use Be\Be;

class RouterHelper
{

    // 缓存
    private static $cache = [];

    /**
     * 获取指定的路由器
     *
     * @param string $app 应用名
     * @param string $router 路由器名称
     * @return mixed | false
     */
    public static function getRouter(string $app, string $router)
    {
        $key = 'router:' . $app . '.' . $router;
        if (!isset(self::$cache[$key])) {
            $class = '\\Be\\App\\' . $app . '\\Router\\' . $router;
            if (class_exists($class)) {
                self::$cache[$key] = new $class();
            } else {
                self::$cache[$key] = false;
            }
        }
        return self::$cache[$key];
    }

    /**
     * 跟据路由生成网址
     *
     * @param string $route 路由
     * @param array $params
     * @return mixed|string
     * @throws \Be\Runtime\RuntimeException
     */
    public static function encode(string $route, array $params = null)
    {
        $rootUrl = Be::getRequest()->getRootUrl() . '/';
        $configSystem = Be::getConfig('App.System.System');
        $configRouter = Be::getConfig('App.System.Router');

        $route2uriKey = null;
        $uri = null;

        $parts = explode('.', $route);
        $appName = $parts[0];
        $controllerName = $parts[1];
        $actionName = $parts[2];
        $router = self::getRouter($appName, $controllerName);
        if ($router && method_exists($router, $actionName)) {

            // 调用路由器生成网址
            $uri = ($params === null) ? $router->$actionName() : $router->$actionName($params);

            // 路由到网址的映射键名
            $route2uriKey = 'be:route2uri:' . $route . ($params === null ? '' : (':' . md5(json_encode($params))));

            if ($configRouter->cache) {
                // 开启缓存时，查看
                if (isset(self::$cache[$route2uriKey]) && self::$cache[$route2uriKey] == $uri) {
                    return $rootUrl . self::$cache[$route2uriKey] . $configSystem->urlSuffix;
                }
            } else {
                if ($params === null) {
                    if (isset(self::$cache[$route2uriKey]) && self::$cache[$route2uriKey] == $uri) {
                        return $rootUrl . self::$cache[$route2uriKey] . $configSystem->urlSuffix;
                    }
                }
            }

        } else {

            // 未设置路由规则时，返回简单伪静态页格式
            $urlParams = '';
            if ($params !== null) {
                foreach ($params as $key => $val) {
                    $urlParams .= '/' . $key . '-' . $val;
                }
            }

            return $rootUrl . str_replace('.', '/', $route) . $urlParams . $configSystem->urlSuffix;
        }

        // 网址到路由的映射键名
        $uri2routeKey = 'be:uri2route:' . $uri;

        // 将路由和网址的双向映射关系写入 redis
        $redis = Be::getRedis($configRouter->redis);
        $redisUri = $redis->get($route2uriKey);
        if ($redisUri) {
            // 路由到网址的映射与Redis中存储的不一致， 更新 REDIS 中的网址
            if ($uri != $redisUri) {
                $redis->set($route2uriKey, $uri);
                if ($params === null) {
                    $redis->set($uri2routeKey, json_encode([$route]));
                } else {
                    $redis->set($uri2routeKey, json_encode([$route, $params]));
                }

                // 删除 redis 中的旧网址到路由的映射
                $redis->del('be:uri2route:' . $redisUri);
            }
        } else {
            // 写入 Redis 网址
            $redis->set($route2uriKey, $uri);
            if ($params === null) {
                $redis->set($uri2routeKey, json_encode([$route]));
            } else {
                $redis->set($uri2routeKey, json_encode([$route, $params]));
            }
        }

        /*
         * 以下两种情况写入数组缓存
         * 1 如果开启了内存缓存
         * 2 未开启内存缓存但无参数时
         */
        if ($configRouter->cache || $params === null) {
            // 路由到网址缓存
            self::$cache[$route2uriKey] = $uri;

            // 网址到路由缓存
            self::$cache[$uri2routeKey] = ($params === null) ? [$route] : [$route, $params];
        }

        return $rootUrl . $uri . $configSystem->urlSuffix;
    }

    /**
     * 跟据网址获取路由信息
     *
     * @param string $uri 网址
     * @return false | array 路由信息
     * @throws \Be\Runtime\RuntimeException
     */
    public static function decode(string $uri)
    {
        $configRouter = Be::getConfig('App.System.Router');

        $uri2routeKey = 'be:uri2route:' . $uri;

        if (isset(self::$cache[$uri2routeKey])) {
            return self::$cache[$uri2routeKey];
        }

        $redis = Be::getRedis($configRouter->redis);
        $routeJson = $redis->get($uri2routeKey);
        if ($routeJson) {
            $route = json_decode($routeJson, true);
            if ($route) {
                if ($configRouter->cache) {
                    self::$cache[$uri2routeKey] = $route;
                } else {
                    if (!isset($route[1])) {
                        self::$cache[$uri2routeKey] = $route;
                    }
                }
                return $route;
            }
        }

        return false;
    }

}


