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
    public static function encode(string $route, array $params = [])
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
        if ($router) {
            $route2uriKey = 'be:route2uri:' . $route;
            if ($configRouter->cache) {
                if ($params) {
                    $route2uriKey .= ':' . md5(json_encode($params));
                }

                if (isset(self::$cache[$route2uriKey])) {
                    return $rootUrl . self::$cache[$route2uriKey] . $configSystem->urlSuffix;
                }
            } else {
                if (!$params) {
                    if (isset(self::$cache[$route2uriKey])) {
                        return $rootUrl . self::$cache[$route2uriKey] . $configSystem->urlSuffix;
                    }
                } else {
                    $route2uriKey .= ':' . md5(json_encode($params));
                }
            }

            if (method_exists($router, $actionName)) {
                $uri = $router->$actionName($params);
            }
        } else {
            $urlParams = '';
            if (count($params)) {
                foreach ($params as $key => $val) {
                    $urlParams .= '/' . $key . '-' . $val;
                }
            }

            return $rootUrl . str_replace('.', '/', $route) . $urlParams . $configSystem->urlSuffix;
        }

        // 存入 REDIS
        $redis = Be::getRedis($configRouter->redis);
        $redisUri = $redis->get($route2uriKey);
        if ($redisUri) {
            // 更新 REDIS 中的网址
            if ($uri != $redisUri) {
                $redis->set($route2uriKey, $uri);
                $redis->set('be:uri2route:' . $uri, json_encode([$route, $params]));
                $redis->del('be:uri2route:' . $redisUri);
            }
        } else {
            // 写入 Redis 网址
            $redis->set($route2uriKey, $uri);
            $redis->set('be:uri2route:' . $uri, json_encode([$route, $params]));
        }

        if ($configRouter->cache) {
            self::$cache[$route2uriKey] = $uri;
        } else {
            if (!$params) {
                self::$cache[$route2uriKey] = $uri;
            }
        }

        return $rootUrl . $uri . $configSystem->urlSuffix;
    }

    /**
     * 跟据网址获取路由信息
     *
     * @param string $uri 网址
     * @return array 路由信息
     * @throws \Be\Runtime\RuntimeException
     */
    public static function decode(string $uri)
    {
        $configRouter = Be::getConfig('App.System.Router');

        $uri2routeKey = 'be:uri2route:' . $uri;

        if ($configRouter->cache) {
            if (isset(self::$cache[$uri2routeKey])) {
                return self::$cache[$uri2routeKey];
            }
        }

        $redis = Be::getRedis($configRouter->redis);
        $routeJson = $redis->get($uri2routeKey);
        if ($routeJson) {
            $route = json_decode($routeJson, true);
            if ($configRouter->cache) {
                if ($route) {
                    self::$cache[$uri2routeKey] = $route;
                    return $route;
                } else {
                    self::$cache[$uri2routeKey] = [];
                }
            }
        }

        return [];
    }

}


