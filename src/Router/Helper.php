<?php

namespace Be\Router;

use Be\Be;
use Be\Util\Annotation;
use http\Exception\RuntimeException;

class Helper
{

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
            $path = Be::getRuntime()->getRootPath() . '/data/Runtime/Router/' . $app . '/' . $router . '.php';
            if (Be::getConfig('App.System.System')->developer || !file_exists($path)) {
                self::updateRouter($app, $router);
            }

            $class = '\\Be\\Data\\Runtime\\Router\\' . $app . '\\' . $router;
            if (!class_exists($class)) {
                throw new RuntimeException(beLang('App.System', 'RUNTIME.ROUTE_ERROR', $app . '.' . $router));
            }

            self::$cache[$key] = new $class();
        }
        return self::$cache[$key];
    }

    /**
     * 跟据注解自动生成路由器
     *
     * @param string $app 应用名
     * @param string $router 路由器名称
     */
    public static function updateRouter(string $app, string $router)
    {
        $code = '<?php' . "\n";
        $code .= 'namespace Be\\Data\\Runtime\\Router\\' . $app . ';' . "\n";
        $code .= "\n";
        $code .= 'class ' . $router . "\n";
        $code .= '{' . "\n";

        $controllerClassName = '\\Be\\App\\' . $app . '\\Controller\\' . $router;
        if (!class_exists($controllerClassName)) {
            throw new RuntimeException(beLang('App.System', 'RUNTIME.ROUTE_ERROR', $app . '.' . $router));
        }

        $reflection = new \ReflectionClass($controllerClassName);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as &$method) {
            $methodName = $method->getName();
            $methodComment = $method->getDocComment();
            $methodComments = Annotation::parse($methodComment);
            foreach ($methodComments as $key => $val) {
                if ($key === 'BeRoute') {
                    if (is_array($val[0]) && isset($val[0]['value'])) {
                        $value = $val[0]['value'];
                        if (strpos($value, '<') !== false) { // 正则路由
                            // 示例：/order/<(\d+):orderId>
                            // 示例：/order/<(\d+):orderId>/edit
                            // 示例：/product/<(\w+):productSn>
                            $reg = "/\<\(([^)]+)\):(\w+)\>/";
                            $results = [];
                            preg_match_all($reg, $value, $results);
                            if (isset($results[0]) && is_array($results[0])) {
                                $len = count($results[0]);
                                if ($len > 0) {
                                    for ($i = 0; $i < $len; $i++) {
                                        $value = str_replace($results[0][$i], '\'.($params[\'' . $results[2][$i] . '\']??\'\').\'', $value);
                                    }
                                    $code .= '  public $' . $methodName . ' = \'regular\';' . "\n";
                                    $code .= '  public function ' . $methodName . '(array $params = [])' . "\n";
                                    $code .= '  {' . "\n";
                                    $code .= '    return \'' . $value . '\';' . "\n";
                                    $code .= '  }' . "\n\n";
                                }
                            }
                        } elseif (strpos($value, '::') !== false || strpos($value, '->') !== false) { // Hashmap 路由
                            $code .= '  public $' . $methodName . ' = \'hashmap\';' . "\n";
                            $code .= '  public function ' . $methodName . '(array $params = [])' . "\n";
                            $code .= '  {' . "\n";
                            $code .= '    return ' . $value . ';' . "\n";
                            $code .= '  }' . "\n\n";
                        } else { // 静态路由
                            $code .= '  public $' . $methodName . ' = \'static\';' . "\n";
                            $code .= '  public function ' . $methodName . '(array $params = null)' . "\n";
                            $code .= '  {' . "\n";
                            $code .= '    return \'' . $value . '\';' . "\n";
                            $code .= '  }' . "\n\n";
                        }
                    }
                }
            }
        }

        $code .= '}' . "\n";

        $path = Be::getRuntime()->getRootPath() . '/data/Runtime/Router/' . $app . '/' . $router . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            @chmod($dir, 0777);
        }

        file_put_contents($path, $code, LOCK_EX);
        @chmod($path, 0777);
    }

    /**
     * 获取指定的路由器
     *
     * @return mixed
     */
    public static function getMapping()
    {
        $key = 'router:mapping';
        if (!isset(self::$cache[$key])) {
            $path = Be::getRuntime()->getRootPath() . '/data/Runtime/Router/Mapping.php';
            if (Be::getConfig('App.System.System')->developer || !file_exists($path)) {
                self::updateMapping();
            }

            $class = '\\Be\\Data\\Runtime\\Router\\Mapping';
            self::$cache[$key] = new $class();
        }
        return self::$cache[$key];
    }

    /**
     * 跟据注解自动生成路由映射
     */
    public static function updateMapping()
    {
        $staticMapping = [];
        $regularMapping = [];
        $hashmap = false;

        $apps = Be::getService('App.System.Admin.App')->getApps();
        foreach ($apps as $app) {

            $appProperty = Be::getProperty('App.' . $app->name);
            $appName = $app->name;
            $controllerDir = $appProperty->getPath() . '/Controller';
            if (!file_exists($controllerDir) && !is_dir($controllerDir)) continue;

            $controllers = scandir($controllerDir);
            foreach ($controllers as $controller) {
                if ($controller === '.' || $controller === '..' || is_dir($controllerDir . '/' . $controller)) continue;

                $controllerNName = substr($controller, 0, -4);
                $controllerClassName = '\\Be\\App\\' . $appName . '\\Controller\\' . $controllerNName;
                if (!class_exists($controllerClassName)) continue;

                $reflection = new \ReflectionClass($controllerClassName);
                $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($methods as &$method) {
                    $methodName = $method->getName();
                    $methodComment = $method->getDocComment();
                    $methodComments = Annotation::parse($methodComment);
                    foreach ($methodComments as $key => $val) {
                        if ($key === 'BeRoute') {
                            if (is_array($val[0]) && isset($val[0]['value'])) {
                                $value = $val[0]['value'];
                                if (strpos($value, '<') !== false) { // 正则路由

                                    // 示例：/order/<(\d+):orderId>
                                    // 示例：/order/<(\d+):orderId>/edit
                                    // 示例：/product/<(\w+):productSn>
                                    $reg = "/\<\(([^)]+)\):(\w+)\>/";
                                    $results = [];
                                    preg_match_all($reg, $value, $results);
                                    if (isset($results[0]) && is_array($results[0])) {
                                        $len = count($results[0]);
                                        if ($len > 0) {
                                            for ($i = 0; $i < $len; $i++) {
                                                $value = str_replace($results[0][$i], '(' . $results[1][$i] . ')', $value);
                                            }
                                            $value = str_replace('/', '\/', $value);
                                            $regularMapping[$value] = [$app->name . '.' . $controllerNName . '.' . $methodName, $results[2]];
                                        }
                                    }

                                } elseif (strpos($value, '::') !== false || strpos($value, '->') !== false) { // Hashmap 路由
                                    $hashmap = true;
                                } else { // 静态路由
                                    $staticMapping[$value] = $app->name . '.' . $controllerNName . '.' . $methodName;
                                }
                            }
                        }
                    }
                }
            }
        }

        $code = '<?php' . "\n";
        $code .= 'namespace Be\\Data\\Runtime\\Router;' . "\n";
        $code .= "\n";
        $code .= 'class Mapping' . "\n";
        $code .= '{' . "\n";

        if (count($staticMapping) > 0) {
            $code .= '  public $static = true;' . "\n";
            $code .= '  public $staticMapping = [' . "\n";
            foreach ($staticMapping as $key => $val) {
                $code .= '    \'' . $key . '\' => \'' . $val . '\',' . "\n";
            }
            $code .= '  ];' . "\n\n";
        } else {
            $code .= '  public $static = false;' . "\n";
            $code .= '  public $staticMapping = null;' . "\n\n";
        }

        if (count($regularMapping) > 0) {
            $code .= '  public $regular = true;' . "\n";
            $code .= '  public $regularMapping = [' . "\n";
            foreach ($regularMapping as $key => $val) {
                $code .= '    \'' . $key . '\' => [\'' . $val[0] . '\', [\''.implode('\', \'', $val[1]).'\']],' . "\n";
            }
            $code .= '  ];' . "\n\n";
        } else {
            $code .= '  public $regular = false;' . "\n";
            $code .= '  public $regularMapping = null;' . "\n\n";
        }

        if ($hashmap) {
            $code .= '  public $hashmap = true;' . "\n";
        } else {
            $code .= '  public $hashmap = false;' . "\n";
        }

        $code .= '}' . "\n";

        $path = Be::getRuntime()->getRootPath() . '/data/Runtime/Router/Mapping.php';
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            @chmod($dir, 0777);
        }

        file_put_contents($path, $code, LOCK_EX);
        @chmod($path, 0777);
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
        $runtime = Be::getRuntime();
        $configSystem = Be::getConfig('App.System.System');
        if ($configSystem->rootUrl === '') {
            if (!$runtime->isSwooleMode() || $runtime->isWorkerProcess()) {
                $rootUrl = Be::getRequest()->getRootUrl();
            } else {
                $rootUrl = '';
            }
        } else {
            $rootUrl = $configSystem->rootUrl;
        }

        $parts = explode('.', $route);
        $appName = $parts[0];
        $controllerName = $parts[1];
        $actionName = $parts[2];
        $router = self::getRouter($appName, $controllerName);
        if ($router && isset($router->$actionName)) {

            // 调用路由器生成网址
            $uri = $router->$actionName($params);

            if ($router->$actionName === 'hashmap') {

                // 路由到网址的映射键名
                $route2uriKey = 'be:route2uri:' . $route . ':' . md5(serialize($params));

                $configRouter = Be::getConfig('App.System.Router');
                if ($configRouter->cache) {
                    // 开启缓存时，并且生成的网址与缓存一致时，直接返回网址
                    if (isset(self::$cache[$route2uriKey]) && self::$cache[$route2uriKey] === $uri) {
                        return $rootUrl . $uri;
                    }
                }

                // 网址到路由的映射键名
                $uri2routeKey = 'be:uri2route:' . $uri;

                // 将路由和网址的双向映射关系写入缓存
                $cache = Be::getCache();
                $cacheUri = $cache->get($route2uriKey);
                if ($cacheUri) {
                    // 路由到网址的映射与Redis中存储的不一致， 更新 REDIS 中的网址
                    if ($uri !== $cacheUri) {
                        $cache->set($route2uriKey, $uri);
                        $cache->set($uri2routeKey, serialize([$route, $params]));

                        // 删除缓存中的旧网址到路由的映射
                        $cache->delete('be:uri2route:' . $cacheUri);
                    }
                } else {
                    // 写入 Redis 网址
                    $cache->set($route2uriKey, $uri);
                    $cache->set($uri2routeKey, serialize([$route, $params]));
                }

                /*
                 * 如果开启了内存缓存, 写入数组缓存
                 */
                if ($configRouter->cache) {
                    self::$cache[$route2uriKey] = $uri; // 路由到网址缓存
                    self::$cache[$uri2routeKey] = [$route, $params]; // 网址到路由缓存
                }
            } elseif ($router->$actionName === 'static') {
                // 静态路由有参数时，将参数以 GET 方式拼接到网址中
                if ($params !== null && $params) {

                    $paramsStr = '';
                    $paramsAvailable = true;
                    foreach ($params as $k => $v) {
                        if (strpos($k, '-') !== false && strpos($v, '-') !== false) {
                            $paramsAvailable = false;
                            break;
                        }
                        $paramsStr .= '/' . $k . '-' . $v;
                    }

                    if ($paramsAvailable) {
                        return $rootUrl . $uri . $paramsStr;
                    } else {
                        return $rootUrl . $uri . '?' . http_build_query($params);
                    }
                }
            }

            return $rootUrl . $uri;

        } else {

            // 未设置路由规则时，返回简单伪静态页格式
            $urlParams = '';
            if ($params !== null && $params) {
                foreach ($params as $key => $val) {
                    $urlParams .= '/' . $key . '-' . $val;
                }
            }

            return $rootUrl . '/' . str_replace('.', '/', $route) . $urlParams;
        }
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
        $mapping = self::getMapping();

        // 静态路由
        if ($mapping->static) {
            if (isset($mapping->staticMapping[$uri])) {
                return [$mapping->staticMapping[$uri]];
            }
        }

        // Hashmap 路由
        if ($mapping->hashmap) {
            $uri2routeKey = 'be:uri2route:' . $uri;
            $configRouter = Be::getConfig('App.System.Router');
            if ($configRouter->cache) {
                if (isset(self::$cache[$uri2routeKey])) {
                    return self::$cache[$uri2routeKey];
                }
            }

            $cache = Be::getCache();
            $routeJson = $cache->get($uri2routeKey);
            if ($routeJson) {
                $route = unserialize($routeJson);
                if ($route) {
                    if ($configRouter->cache) {
                        self::$cache[$uri2routeKey] = $route;
                    }
                    return $route;
                }
            }
        }

        // 尝试逐个移除结尾的参数
        if ($mapping->static) {

            $uris = explode('/', $uri);
            $len = count($uris);

            if ($len > 2) {
                $params = [];
                $i = $len;
                do {
                    $param = array_pop($uris);

                    $pos = strpos($param, '-');
                    if ($pos === false)  break;

                    $key = substr($param, 0, $pos);
                    $val = substr($param, $pos + 1);

                    $params[$key] = $val;

                    $routeStr = implode('/', $uris);

                    if (isset($mapping->staticMapping[$routeStr])) {
                        return [$mapping->staticMapping[$routeStr], $params];
                    }

                    $i--;
                } while($i > 1);
            }
        }

        // 正则路由
        if ($mapping->regular) {
            foreach ($mapping->regularMapping as $key => $val) {
                if (preg_match('/' . $key . '/', $uri, $results)) {
                    $len = count($val[1]);
                    $route = $val[0];
                    $params = [];
                    for ($i = 0; $i < $len; $i++) {
                        $params[$val[1][$i]] = $results[$i + 1];
                    }
                    return [$route, $params];
                }
            }
        }

        return false;
    }

}


