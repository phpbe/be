<?php

namespace Be;

use Be\Runtime\RuntimeException;

/**
 * BE系统资源工厂
 *
 * Class Be
 * @package Be
 */
abstract class Be
{

    private static $cache = [];

    /**
     * 获取swoole模式下的协程ID， common模式下返回0
     *
     * @return int
     */
    public static function getCid(): int
    {
        $runtime = self::getRuntime();
        if ($runtime->isSwooleMode()) {
            return \Swoole\Coroutine::getCid();
        }

        return 0;
    }

    /**
     *  设置运行时
     * @param \Be\Runtime\Driver $runtime
     */
    public static function setRuntime(\Be\Runtime\Driver $runtime)
    {
        self::$cache['runtime'] = $runtime;
    }

    /**
     * 获取运行时对象
     *
     * @return \Be\Runtime\Driver
     */
    public static function getRuntime(): \Be\Runtime\Driver
    {
        return self::$cache['runtime'];
    }

    /**
     * 设置请求对象
     *
     * @param \Be\Request\Driver $request
     */
    public static function setRequest(\Be\Request\Driver $request)
    {
        $runtime = self::getRuntime();
        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            self::$cache[$cid]['request'] = $request;
        } else {
            self::$cache['request'] = $request;
        }
    }

    /**
     * 获取请求对象
     *
     * @return \Be\Request\Driver
     */
    public static function getRequest()
    {
        $runtime = self::getRuntime();
        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['request'])) {
                return self::$cache[$cid]['request'];
            }
        } else {
            if (isset(self::$cache['request'])) {
                return self::$cache['request'];
            }
        }

        return null;
    }

    /**
     * 设置响应对象
     *
     * @param \Be\Response\Driver $request
     */
    public static function setResponse(\Be\Response\Driver $response)
    {
        $runtime = self::getRuntime();
        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            self::$cache[$cid]['response'] = $response;
        } else {
            self::$cache['response'] = $response;
        }
    }

    /**
     * 获取输出对象
     *
     * @return \Be\Response\Driver | null
     */
    public static function getResponse()
    {
        $runtime = self::getRuntime();
        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['response'])) {
                return self::$cache[$cid]['response'];
            }
        } else {
            if (isset(self::$cache['response'])) {
                return self::$cache['response'];
            }
        }

        return null;
    }

    /**
     * 获取指定的配置文件
     *
     * @param string $name 配置文件名
     * @return mixed
     */
    public static function getConfig(string $name)
    {
        if (!isset(self::$cache['config'][$name])) {
            self::$cache['config'][$name] = self::newConfig($name);
        }
        return self::$cache['config'][$name];
    }

    /**
     * 新创建一个指定的配置文件
     *
     * @param string $name 配置文件名
     * @return mixed
     */
    public static function newConfig(string $name)
    {
        $parts = explode('.', $name);

        $type = array_shift($parts);
        $catalog = array_shift($parts);
        $classSuffix = implode('\\', $parts);

        $isPageConfig = ($type === 'Theme' || $type === 'AdminTheme') && $parts[0] === 'Page';

        $instance1 = null;
        $class = '\\Be\\Data\\' . $type . '\\' . $catalog . '\\Config\\' . $classSuffix;
        if (class_exists($class)) {
            $instance1 = new $class();
        } elseif ($isPageConfig && count($parts) > 2)  {

            // 则检测对应页面在主题中是否存在指定配置
            $class = '\\Be\\' . $type . '\\' . $catalog . '\\Config\\' . $classSuffix;
            if (class_exists($class)) {
                $instance1 = new $class();
            }
            
            if ($instance1 === null) {
                // 则检测对应页面在APP中是否存在指定配置
                $class = '\\Be\\App\\' . $parts[1] . '\\Config\\Page\\' . ($type === 'AdminTheme' ? 'Admin\\' : '') . implode('\\', array_slice($parts, 2));
                if (class_exists($class)) {
                    $instance1 = new $class();
                }
            }

            if ($instance1 === null) {
                $class = '\\Be\\Data\\' . $type . '\\' . $catalog . '\\Config\\Page';
                if (class_exists($class)) {
                    $instance1 = new $class();
                }
            }
        }

        if ($isPageConfig) {
            $class = '\\Be\\' . $type . '\\' . $catalog . '\\Config\\Page';
            if (!class_exists($class)) {
                throw new RuntimeException('Config (' . $type . '.' . $catalog . '.Page) does not exist!');
            }
        } else {
            $class = '\\Be\\' . $type . '\\' . $catalog . '\\Config\\' . $classSuffix;
            if (!class_exists($class)) {
                throw new RuntimeException('Config ' . $name . ' does not exist!');
            }
        }

        $instance2 = new $class();
        if ($instance1 === null) {
            return $instance2;
        } else {
            $vars = get_object_vars($instance2);

            if ($isPageConfig) {
                foreach ($vars as $key => $val) {
                    if (in_array($key, ['north', 'middle', 'west', 'center', 'east', 'south'])) {
                        if (!isset($instance1->$key) || $instance1->$key < 0) {
                            // 方位属性如果是继承的公共的，用负数标记
                            $instance1->$key = -$val;
                        }
                    } else {
                        if (!isset($instance1->$key)) {
                            $instance1->$key = $val;
                        }
                    }
                }

                if ($instance1->middle !== 0) {
                    $instance1->west = 0;
                    $instance1->center = 0;
                    $instance1->east = 0;
                    unset($instance1->westSections, $instance1->centerSections, $instance1->eastSections);
                }

                if ($instance1->west !== 0 || $instance1->center !== 0 || $instance1->east !== 0) {
                    $instance1->middle = 0;
                    unset($instance1->middleSections);
                }

            } else {
                foreach ($vars as $key => $val) {
                    if (!isset($instance1->$key)) {
                        $instance1->$key = $val;
                    }
                }
            }

            return $instance1;
        }
    }

    /**
     * 获取日志记录器
     *
     * @return \Be\Log\Driver
     */
    public static function getLog(): \Be\Log\Driver
    {
        $runtime = self::getRuntime();
        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (!isset(self::$cache[$cid]['log'])) {
                self::$cache[$cid]['log'] = self::newLog();
            }
            return self::$cache[$cid]['log'];
        } else {
            if (!isset(self::$cache['log'])) {
                self::$cache['log'] = self::newLog();
            }
            return self::$cache['log'];
        }
    }

    /**
     * 创建 日志记录器
     *
     * @return \Be\Log\Driver
     */
    public static function newLog(): \Be\Log\Driver
    {
        return new \Be\Log\Driver\File();
    }

    /**
     * 获取一个属性
     *
     * @param string $name 名称
     * @return \Be\Property\Driver
     * @throws RuntimeException
     */
    public static function getProperty(string $name)
    {
        if (isset(self::$cache['property'][$name])) return self::$cache['property'][$name];

        $parts = explode('.', $name);
        $class = '\\Be\\' . implode('\\', $parts) . '\\Property';
        if (!class_exists($class)) throw new RuntimeException('Property ' . $name . ' does not exist!');
        $instance = new $class();

        self::$cache['property'][$name] = $instance;
        return self::$cache['property'][$name];
    }

    /**
     * 获取SESSION
     *
     * @return \Be\Session\Driver
     */
    public static function getSession(): \Be\Session\Driver
    {
        $config = self::getConfig('App.System.Session');
        $runtime = self::getRuntime();

        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['session'])) {
                return self::$cache[$cid]['session'];
            }

            $driver = '\\Be\\Session\\Driver\\Swoole\\' . $config->driver;
            if (!class_exists($driver)) {
                throw new RuntimeException('Session driver' . $config->driver . ' does not exist!');
            }
            self::$cache[$cid]['session'] = new $driver($config);
            return self::$cache[$cid]['session'];
        } else {
            if (isset(self::$cache['session'])) {
                return self::$cache['session'];
            }

            $driver = '\\Be\\Session\\Driver\\Common\\' . $config->driver;
            if (!class_exists($driver)) {
                throw new RuntimeException('Session driver' . $config->driver . ' does not exist!');
            }

            self::$cache['session'] = new $driver($config);
            return self::$cache['session'];
        }
    }

    /**
     * 获取Cache
     *
     * @return \Be\Cache\Driver
     */
    public static function getCache(): \Be\Cache\Driver
    {
        $runtime = self::getRuntime();
        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (!isset(self::$cache[$cid]['cache'])) {
                self::$cache[$cid]['cache'] = self::newCache();
            }
            return self::$cache[$cid]['cache'];
        } else {
            if (!isset(self::$cache['cache'])) {
                self::$cache['cache'] = self::newCache();
            }
            return self::$cache['cache'];
        }
    }

    /**
     * 创建 Cache
     *
     * @return \Be\Cache\Driver
     */
    public static function newCache(): \Be\Cache\Driver
    {
        $config = self::getConfig('App.System.Cache');
        $driver = '\\Be\\Cache\\Driver\\' . $config->driver;
        if (!class_exists($driver)) {
            throw new RuntimeException('Cache driver' . $config->driver . ' does not exist!');
        }
        return new $driver($config);
    }

    /**
     * 获取 Storage
     *
     * @return \Be\Storage\Driver
     */
    public static function getStorage(): \Be\Storage\Driver
    {
        if (self::getRuntime()->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (!isset(self::$cache[$cid]['storage'])) {
                self::$cache[$cid]['storage'] = self::newStorage();
            }
            return self::$cache[$cid]['storage'];
        } else {
            if (!isset(self::$cache['storage'])) {
                self::$cache['storage'] = self::newStorage();
            }
            return self::$cache['storage'];
        }
    }

    /**
     * 创建 Storage
     *
     * @return \Be\Storage\Driver
     */
    public static function newStorage(): \Be\Storage\Driver
    {
        $config = self::getConfig('App.System.Storage');
        $driver = '\\Be\\Storage\\Driver\\' . $config->driver;
        if (!class_exists($driver)) {
            throw new RuntimeException('Storage driver' . $config->driver . ' does not exist!');
        }
        return new $driver($config);
    }

    /**
     * 初始货Redis连接池
     */
    public static function initRedisPools()
    {
        if (self::$cache['redis']['pools'] === null) {
            self::$cache['redis']['pools'] = [];
            $config = Be::getConfig('App.System.Redis');
            foreach ($config as $k => $v) {
                $size = isset($v['pool']) ? intval($v['pool']) : 0;
                if ($size <= 0) {
                    continue;
                }

                $redisConfig = new \Swoole\Database\RedisConfig();

                $redisConfig->withHost($v['host']);

                if (isset($v['port'])) {
                    $redisConfig->withPort($v['port']);
                }

                if (isset($v['auth']) && $v['auth']) {
                    $redisConfig->withAuth($v['auth']);
                }

                if (isset($v['db']) && $v['db']) {
                    $redisConfig->withDbIndex($v['db']);
                }

                if (isset($v['timeout']) && $v['timeout']) {
                    $redisConfig->withTimeout($v['timeout']);
                }

                self::$cache['redis']['pools'][$k] = new \Swoole\Database\RedisPool($redisConfig, $size);
            }
        }
    }

    /**
     * 获取Redis对象
     *
     * @param string $name Redis名
     * @return \Be\Redis\Driver|\Redis
     * @throws RuntimeException
     */
    public static function getRedis(string $name = 'master')
    {
        $runtime = self::getRuntime();
        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['redis'][$name])) return self::$cache[$cid]['redis'][$name];

            $config = Be::getConfig('App.System.Redis');
            if (!isset($config->$name)) {
                throw new RuntimeException('Redis config item (' . $name . ') does not exist!');
            }

            $driver = null;
            if ($runtime->isWorkerProcess() && isset(self::$cache['redis']['pools'][$name])) {
                $pool = self::$cache['redis']['pools'][$name];
                $redis = $pool->get();
                $driver = new \Be\Redis\Driver($name, $redis);
            } else {
                $driver = new \Be\Redis\Driver($name);
            }

            self::$cache[$cid]['redis'][$name] = $driver;
            return self::$cache[$cid]['redis'][$name];
        } else {
            if (!isset(self::$cache['redis'][$name])) {
                self::$cache['redis'][$name] = self::newRedis($name);
            }
            return self::$cache['redis'][$name];
        }
    }

    /**
     * 新创建一个Redis对象
     *
     * @param string $name Redis名
     * @return \Be\Redis\Driver|\Redis
     * @throws RuntimeException
     */
    public static function newRedis(string $name = 'master')
    {
        $config = Be::getConfig('App.System.Redis');
        if (!isset($config->$name)) {
            throw new RuntimeException('Redis config item (' . $name . ') does not exist!');
        }

        return new \Be\Redis\Driver($name);
    }

    /**
     * 获取ES对象
     *
     * @return \Elasticsearch\Client
     * @throws RuntimeException
     */
    public static function getEs()
    {
        $runtime = self::getRuntime();
        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (!isset(self::$cache[$cid]['es'])) {
                self::$cache[$cid]['es'] = self::newEs();
            }
            return self::$cache[$cid]['es'];
        } else {
            if (!isset(self::$cache['es'])) {
                self::$cache['es'] = self::newEs();
            }
            return self::$cache['es'];
        }
    }

    /**
     * 新创建一个ES对象
     *
     * @return \Elasticsearch\Client
     * @throws RuntimeException
     */
    public static function newEs()
    {
        $config = self::getConfig('App.System.Es');
        $driver = \Elasticsearch\ClientBuilder::create()->setHosts($config->hosts)->build();
        return $driver;
    }

    /**
     * 初始货Redis连接池
     */
    public static function initDbPools()
    {
        if (self::$cache['db']['pools'] === null) {
            self::$cache['db']['pools'] = [];

            $config = self::getConfig('App.System.Db');
            foreach ($config as $k => $v) {
                if ($v['driver'] !== 'mysql') continue;

                $size = isset($v['pool']) ? intval($v['pool']) : 0;
                if ($size <= 0) {
                    continue;
                }

                $options = array(
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                );

                $port = isset($v['port']) ? $v['port'] : 3306;
                $charset = isset($v['charset']) ? $v['charset'] : 'utf8mb4';

                $pdoConfig = new \Swoole\Database\PDOConfig();
                $pdoConfig->withHost($v['host'])
                    ->withPort($port)
                    ->withDbName($v['name'])
                    ->withUsername($v['username'])
                    ->withPassword($v['password'])
                    ->withCharset($charset)
                    ->withOptions($options);

                self::$cache['db']['pools'][$k] = new \Swoole\Database\PDOPool($pdoConfig, $size);
            }
        }
    }

    /**
     * 获取数据库对象
     *
     * @param string $name 数据库名
     * @return \Be\Db\Driver
     * @throws RuntimeException
     */
    public static function getDb(string $name = 'master')
    {
        $runtime = self::getRuntime();
        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['db'][$name])) return self::$cache[$cid]['db'][$name];

            $config = self::getConfig('App.System.Db');
            if (!isset($config->$name)) {
                throw new RuntimeException('Db config item (' . $name . ') does not exist!');
            }
            $configData = $config->$name;

            $driver = null;
            switch ($configData['driver']) {
                case 'mysql':
                    if ($runtime->isWorkerProcess() && isset(self::$cache['db']['pools'][$name])) {
                        $pool = self::$cache['db']['pools'][$name];
                        $pdo = $pool->get();
                        $driver = new \Be\Db\Driver\Mysql($name, $pdo);
                    } else {
                        $driver = new \Be\Db\Driver\Mysql($name);
                    }
                    break;
                case 'Mssql':
                    $driver = new \Be\Db\Driver\Mssql($name);
                    break;
                case 'Oracle':
                    $driver = new \Be\Db\Driver\Oracle($name);
                    break;
            }

            self::$cache[$cid]['db'][$name] = $driver;
            return self::$cache[$cid]['db'][$name];
        } else {
            if (isset(self::$cache['db'][$name])) return self::$cache['db'][$name];
            self::$cache['db'][$name] = self::newDb($name);
            return self::$cache['db'][$name];
        }
    }

    /**
     * 新创建一个数据库对象
     *
     * @param string $name 数据库名
     * @return \Be\Db\Driver
     * @throws RuntimeException
     */
    public static function newDb(string $name = 'master')
    {
        $config = Be::getConfig('App.System.Db');
        if (!isset($config->$name)) {
            throw new RuntimeException('Db config item (' . $name . ') does not exist!');
        }
        $configData = $config->$name;

        $driver = null;
        switch ($configData['driver']) {
            case 'mysql':
                $driver = new \Be\Db\Driver\Mysql($name);
                break;
            case 'Mssql':
                $driver = new \Be\Db\Driver\Mssql($name);
                break;
            case 'Oracle':
                $driver = new \Be\Db\Driver\Oracle($name);
                break;
        }
        return $driver;
    }

    /**
     * 获取指定的一个数据库行记灵对象
     *
     * @param string $name 数据库行记灵对象名
     * @param string $db 库名
     * @return \Be\Db\Tuple | mixed
     */
    public static function getTuple(string $name, string $db = 'master')
    {
        $path = self::getRuntime()->getRootPath() . '/data/Runtime/Tuple/' . $db . '/' . $name . '.php';
        if (Be::getConfig('App.System.System')->developer || !file_exists($path)) {
            \Be\Db\DbHelper::updateTuple($name, $db);
            include_once $path;
        }

        $class = '\\Be\\Data\\Runtime\\Tuple\\' . $db . '\\' . $name;
        return (new $class());
    }

    /**
     * 获取指定的一个数据库表对象
     *
     * @param string $name 表名
     * @param string $db 库名
     * @return \Be\Db\Table
     */
    public static function getTable(string $name, string $db = 'master')
    {
        $path = self::getRuntime()->getRootPath() . '/data/Runtime/Table/' . $db . '/' . $name . '.php';
        if (Be::getConfig('App.System.System')->developer || !file_exists($path)) {
            \Be\Db\DbHelper::updateTable($name, $db);
            include_once $path;
        }

        $class = '\\Be\\Data\\Runtime\\Table\\' . $db . '\\' . $name;
        return (new $class());
    }

    /**
     * 获取指定的一个数据库表属性
     *
     * @param string $name 表名
     * @param string $db 库名
     * @return \Be\Db\TableProperty
     */
    public static function getTableProperty(string $name, string $db = 'master')
    {
        if (isset(self::$cache['tableProperty'][$db][$name])) return self::$cache['tableProperty'][$db][$name];

        $path = self::getRuntime()->getRootPath() . '/data/Runtime/TableProperty/' . $db . '/' . $name . '.php';
        if (Be::getConfig('App.System.System')->developer || !file_exists($path)) {
            \Be\Db\DbHelper::updateTableProperty($name, $db);
            include_once $path;
        }

        $class = '\\Be\\Data\\Runtime\\TableProperty\\' . $db . '\\' . $name;
        self::$cache['tableProperty'][$db][$name] = new $class();
        return self::$cache['tableProperty'][$db][$name];
    }

    /**
     * 获取 MongoDb 对象
     *
     * @param string $name 名称
     * @return \Be\MongoDb\Driver
     */
    public static function getMongoDb(string $name)
    {
        if (self::getRuntime()->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (!isset(self::$cache[$cid]['mongoDb'][$name])) {
                self::$cache[$cid]['mongoDb'][$name] = self::newMongoDb($name);
            }
            return self::$cache[$cid]['mongoDb'][$name];
        } else {
            if (!isset(self::$cache['mongoDb'][$name])) {
                self::$cache['mongoDb'][$name] = self::newMongoDb($name);
            }
            return self::$cache['mongoDb'][$name];
        }
    }

    /**
     * 新创建一个 MongoDb 对象
     *
     * @param string $name 名称
     * @return \Be\MongoDb\Driver
     * @throws RuntimeException
     */
    public static function newMongoDb(string $name)
    {
        $config = Be::getConfig('App.System.MongoDb');
        if (!isset($config->$name)) {
            throw new RuntimeException('MongoDb config item (' . $name . ') does not exist!');
        }
        return new \Be\MongoDb\Driver($config->$name);
    }

    /**
     * 获取指定的一个服务
     *
     * @param string $name 服务名
     * @return mixed
     */
    public static function getService(string $name)
    {
        if (self::getRuntime()->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (!isset(self::$cache[$cid]['service'][$name])) {
                self::$cache[$cid]['service'][$name] = self::newService($name);
            }
            return self::$cache[$cid]['service'][$name];
        } else {
            if (!isset(self::$cache['service'][$name])) {
                self::$cache['service'][$name] = self::newService($name);
            }
            return self::$cache['service'][$name];
        }
    }

    /**
     * 新创建一个服务
     *
     * @param string $name 服务名
     * @return mixed
     */
    public static function newService(string $name)
    {
        $parts = explode('.', $name);
        $type = array_shift($parts);
        $catalog = array_shift($parts);
        $class = '\\Be\\' . $type . '\\' . $catalog . '\\Service\\' . implode('\\', $parts);
        if (!class_exists($class)) {
            throw new RuntimeException('Service (' . $name . ') does not exist!');
        }

        return new $class();
    }

    /**
     * 获取指定的库
     *
     * @param string $name 库名，可指定命名空间，调用第三方库
     * @return mixed
     * @throws RuntimeException
     */
    public static function getLib(string $name)
    {
        if (self::getRuntime()->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (!isset(self::$cache[$cid]['lib'][$name])) {
                self::$cache[$cid]['lib'][$name] = self::newLib($name);
            }
            return self::$cache[$cid]['lib'][$name];
        } else {
            if (!isset(self::$cache['lib'][$name])) {
                self::$cache['lib'][$name] = self::newLib($name);
            }
            return self::$cache['lib'][$name];
        }
    }

    /**
     * 新创建一个指定的库
     *
     * @param string $name 库名，可指定命名空间，调用第三方库
     * @return mixed
     * @throws RuntimeException
     */
    public static function newLib(string $name)
    {
        $class = null;
        if (strpos($name, '\\') === false) {
            $class = '\\Be\\Lib\\' . $name . '\\' . $name;
        } else {
            $class = $name;
        }
        if (!class_exists($class)) throw new RuntimeException('Lib ' . $class . ' does not exist!');

        return new $class();
    }

    /**
     * 获取指定的一个扩展
     *
     * @param string $name 扩展名
     * @return mixed
     * @throws RuntimeException
     */
    public static function getPlugin(string $name)
    {
        if (self::getRuntime()->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (!isset(self::$cache[$cid]['plugin'][$name])) {
                self::$cache[$cid]['plugin'][$name] = self::newPlugin($name);
            }
            return self::$cache[$cid]['plugin'][$name];
        } else {
            if (!isset(self::$cache['plugin'][$name])) {
                self::$cache['plugin'][$name] = self::newPlugin($name);
            }
            return self::$cache['plugin'][$name];
        }
    }

    /**
     * 新创建一个指定的扩展
     *
     * @param string $name 扩展名
     * @return mixed
     * @throws RuntimeException
     */
    public static function newPlugin(string $name)
    {
        $class = '\\Be\\Plugin\\' . $name . '\\' . $name;
        if (!class_exists($class)) {
            throw new RuntimeException('Plugin ' . $name . ' does not exist!');
        }

        return new $class();
    }

    /**
     * 获取指定的一个后台扩展
     *
     * @param string $name 扩展名
     * @return mixed
     * @throws RuntimeException
     */
    public static function getAdminPlugin(string $name)
    {
        if (self::getRuntime()->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (!isset(self::$cache[$cid]['adminPlugin'][$name])) {
                self::$cache[$cid]['adminPlugin'][$name] = self::newAdminPlugin($name);
            }
            return self::$cache[$cid]['adminPlugin'][$name];
        } else {
            if (!isset(self::$cache['adminPlugin'][$name])) {
                self::$cache['adminPlugin'][$name] = self::newAdminPlugin($name);
            }
            return self::$cache['adminPlugin'][$name];
        }
    }

    /**
     * 新创建一个指定的后台扩展
     *
     * @param string $name 扩展名
     * @return mixed
     * @throws RuntimeException
     */
    public static function newAdminPlugin(string $name)
    {
        $class = '\\Be\\AdminPlugin\\' . $name . '\\' . $name;
        if (!class_exists($class)) {
            throw new RuntimeException('AdminPlugin ' . $name . ' does not exist!');
        }

        return new $class();
    }

    /**
     * 获取指定的一个模板
     *
     * @param string $template 模板名
     * @param string $theme 主题名
     * @return \Be\Template\Driver
     * @throws RuntimeException
     */
    public static function getTemplate(string $template, string $theme = null)
    {
        $parts = explode('.', $template);
        $type = array_shift($parts);
        $name = array_shift($parts);

        if ($theme === null) {
            $theme = self::getConfig('App.System.Theme')->default;
        }

        $runtime = self::getRuntime();
        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['template'][$theme][$template])) {
                return self::$cache[$cid]['template'][$theme][$template];
            }
        } else {
            if (isset(self::$cache['template'][$theme][$template])) {
                return self::$cache['template'][$theme][$template];
            }
        }

        $path = $runtime->getRootPath() . '/data/Runtime/Template/' . $theme . '/' . $type . '/' . $name . '/' . implode('/', $parts) . '.php';
        if (self::getConfig('App.System.System')->developer || !file_exists($path)) {
            \Be\Template\TemplateHelper::update($template, $theme);
        }

        $class = '\\Be\\Data\\Runtime\\Template\\' . $theme . '\\' . $type . '\\' . $name . '\\' . implode('\\', $parts);

        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            self::$cache[$cid]['template'][$theme][$template] = new $class();
            return self::$cache[$cid]['template'][$theme][$template];
        } else {
            self::$cache['template'][$theme][$template] = new $class();
            return self::$cache['template'][$theme][$template];
        }
    }

    /**
     * 获取指定的一个模板
     *
     * @param string $template 模板名
     * @param string $theme 主题名
     * @return \Be\Template\Driver
     * @throws RuntimeException
     */
    public static function getAdminTemplate(string $template, string $theme = null)
    {
        $parts = explode('.', $template);
        $type = array_shift($parts);
        $name = array_shift($parts);

        if ($theme === null) {
            $theme = self::getConfig('App.System.AdminTheme')->default;
        }

        $runtime = self::getRuntime();
        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['adminTemplate'][$theme][$template])) {
                return self::$cache[$cid]['adminTemplate'][$theme][$template];
            }
        } else {
            if (isset(self::$cache['adminTemplate'][$theme][$template])) {
                return self::$cache['adminTemplate'][$theme][$template];
            }
        }

        $path = $runtime->getRootPath() . '/data/Runtime/AdminTemplate/' . $theme . '/' . $type . '/' . $name . '/' . implode('/', $parts) . '.php';
        if (self::getConfig('App.System.System')->developer || !file_exists($path)) {
            \Be\Template\TemplateHelper::update($template, $theme, true);
        }

        $class = '\\Be\\Data\\Runtime\\AdminTemplate\\' . $theme . '\\' . $type . '\\' . $name . '\\' . implode('\\', $parts);

        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            self::$cache[$cid]['adminTemplate'][$theme][$template] = new $class();
            return self::$cache[$cid]['adminTemplate'][$theme][$template];
        } else {
            self::$cache['adminTemplate'][$theme][$template] = new $class();
            return self::$cache['adminTemplate'][$theme][$template];
        }
    }

    /**
     * 获取指定的一个菜单
     *
     * @return \Be\Menu\Driver
     */
    public static function getMenu($name)
    {
        if (isset(self::$cache['Menu'][$name])) return self::$cache['Menu'][$name];

        $path = self::getRuntime()->getRootPath() . '/data/Runtime/Menu/' . $name . '.php';
        if (!file_exists($path)) {
            $service = Be::getService('App.System.Admin.Menu');
            $service->update($name);
        }

        $class = '\\Be\\Data\\Runtime\\Menu\\' . $name;
        self::$cache['Menu'][$name] = new $class();
        return self::$cache['Menu'][$name];
    }

    /**
     * 获取指定的一个菜单
     *
     * @return \Be\AdminMenu\Driver
     */
    public static function getAdminMenu()
    {
        if (isset(self::$cache['adminMenu'])) return self::$cache['adminMenu'];

        $path = self::getRuntime()->getRootPath() . '/data/Runtime/AdminMenu.php';
        if (self::getConfig('App.System.System')->developer || !file_exists($path)) {
            $service = Be::getService('App.System.Admin.AdminMenu');
            $service->update();
        }

        $class = '\\Be\\Data\\Runtime\\AdminMenu';
        self::$cache['adminMenu'] = new $class();
        return self::$cache['adminMenu'];
    }

    /**
     * 获取指定的一个角色信息
     *
     * @param string $roleId 角色ID
     * @return \Be\AdminUser\AdminRole
     */
    public static function getAdminRole(string $roleId)
    {
        if (isset(self::$cache['adminRole'][$roleId])) return self::$cache['adminRole'][$roleId];

        $suffix = str_replace('-', '', $roleId);
        $path = self::getRuntime()->getRootPath() . '/data/Runtime/AdminRole/AdminRole_' . $suffix . '.php';
        if (self::getConfig('App.System.System')->developer || !file_exists($path)) {
            $service = Be::getService('App.System.Admin.AdminRole');
            $service->updateAdminRole($roleId);
        }

        $class = '\\Be\\Data\\Runtime\\AdminRole\\AdminRole_' . $suffix;
        self::$cache['adminRole'][$roleId] = new $class();
        return self::$cache['adminRole'][$roleId];
    }

    /**
     * 获取后台权限信息
     *
     * @return \Be\AdminUser\AdminPermission
     */
    public static function getAdminPermission()
    {
        if (isset(self::$cache['adminPermission'])) return self::$cache['adminPermission'];

        $path = self::getRuntime()->getRootPath() . '/data/Runtime/AdminPermission/AdminPermission.php';
        if (self::getConfig('App.System.System')->developer || !file_exists($path)) {
            $service = Be::getService('App.System.Admin.AdminPermission');
            $service->updateAdminPermission();
        }

        $class = '\\Be\\Data\\Runtime\\AdminPermission\\AdminPermission';
        self::$cache['adminPermission'] = new $class();
        return self::$cache['adminPermission'];
    }


    /**
     * 设置当前后台用户
     *
     * @param \stdClass | null $adminUser
     */
    public static function setAdminUser($adminUser = null)
    {
        Be::getSession()->set('be-adminUser', $adminUser);
        $runtime = self::getRuntime();
        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['adminUser'])) {
                if ($adminUser === null) {
                    unset(self::$cache[$cid]['adminUser']);
                } else {
                    self::$cache[$cid]['adminUser'] = new \Be\AdminUser\AdminUser($adminUser);
                }
            }
        } else {
            if (isset(self::$cache['adminUser'])) {
                if ($adminUser === null) {
                    unset(self::$cache['adminUser']);
                } else {
                    self::$cache['adminUser'] = new \Be\AdminUser\AdminUser($adminUser);
                }
            }
        }
    }

    /**
     * 获取当前后台用户
     *
     * @return \Be\AdminUser\AdminUser | mixed
     */
    public static function getAdminUser()
    {
        $runtime = self::getRuntime();
        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['adminUser'])) {
                return self::$cache[$cid]['adminUser'];
            }

            $user = Be::getSession()->get('be-adminUser');
            self::$cache[$cid]['adminUser'] = new \Be\AdminUser\AdminUser($user);
            return self::$cache[$cid]['adminUser'];
        } else {
            if (isset(self::$cache['adminUser'])) {
                return self::$cache['adminUser'];
            }

            $user = Be::getSession()->get('be-adminUser');
            self::$cache['adminUser'] = new \Be\AdminUser\AdminUser($user);
            return self::$cache['adminUser'];
        }
    }

    /**
     * 设置当前用户
     *
     * @param \stdClass | null $user
     */
    public static function setUser($user = null)
    {
        Be::getSession()->set('be-user', $user);
        $runtime = self::getRuntime();
        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['user'])) {
                if ($user === null) {
                    unset(self::$cache[$cid]['user']);
                } else {
                    self::$cache[$cid]['user'] = new \Be\User\User($user);
                }
            }
        } else {
            if (isset(self::$cache['user'])) {
                if ($user === null) {
                    unset(self::$cache['user']);
                } else {
                    self::$cache['user'] = new \Be\User\User($user);
                }
            }
        }
    }

    /**
     * 获取当前用户
     *
     * @return \Be\User\User | mixed
     */
    public static function getUser()
    {
        $runtime = self::getRuntime();
        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['user'])) {
                return self::$cache[$cid]['user'];
            }

            $user = Be::getSession()->get('be-user');
            self::$cache[$cid]['user'] = new \Be\User\User($user);
            return self::$cache[$cid]['user'];
        } else {
            if (isset(self::$cache['user'])) {
                return self::$cache['user'];
            }

            $user = Be::getSession()->get('be-user');
            self::$cache['user'] = new \Be\User\User($user);
            return self::$cache['user'];
        }
    }

    /**
     * 设置全局数据
     *
     * @param string $name
     * @param $value
     */
    public static function setGlobal(string $name, $value)
    {
        self::$cache['global'][$name] = $value;
    }

    /**
     * 获取全局数据
     *
     * @param string $name
     * @return mixed|null
     */
    public static function getGlobal(string $name)
    {
        if (isset(self::$cache['global'][$name])) {
            return self::$cache['global'][$name];
        }
        return null;
    }

    /**
     * 全局数据是否存在
     *
     * @param string $name
     * @return bool
     */
    public static function hasGlobal(string $name): bool
    {
        return isset(self::$cache['global'][$name]);
    }

    /**
     * 设置上下文
     *
     * @param string $name
     * @param $value
     */
    public static function setContext(string $name, $value)
    {
        if (self::getRuntime()->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            self::$cache[$cid]['context'][$name] = $value;
        } else {
            self::$cache['context'][$name] = $value;
        }
    }

    /**
     * 获取上下文
     *
     * @param string $name
     * @return mixed|null
     */
    public static function getContext(string $name)
    {
        if (self::getRuntime()->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['context'][$name])) {
                return self::$cache[$cid]['context'][$name];
            }
            return null;
        } else {
            if (isset(self::$cache['context'][$name])) {
                return self::$cache['context'][$name];
            }
            return null;
        }
    }

    /**
     * 上下文是否存在
     *
     * @param string $name
     * @return bool
     */
    public static function hasContext(string $name): bool
    {
        if (self::getRuntime()->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();
            return isset(self::$cache[$cid]['context'][$name]);
        } else {
            return isset(self::$cache['context'][$name]);
        }
    }

    /**
     * 回收资源
     */
    public static function gc()
    {
        $runtime = self::getRuntime();
        if ($runtime->isSwooleMode()) {
            $cid = \Swoole\Coroutine::getCid();

            // worker 进程回收
            if ($runtime->isWorkerProcess()) {
                // 关闭 SESSION
                if (isset(self::$cache[$cid]['session'])) {
                    /**
                     * @var \Be\Session\Driver
                     */
                    $driver = self::$cache[$cid]['session'];
                    $driver->close();
                }

                // 回收 db 连接池
                if (isset(self::$cache[$cid]['db'])) {
                    foreach (self::$cache[$cid]['db'] as $name => $driver) {
                        if (isset(self::$cache['db']['pools'][$name])) {
                            $pool = self::$cache['db']['pools'][$name];

                            /**
                             * @var \Be\Db\Driver $driver
                             */
                            $pdo = $driver->getConnection()->getPdo();
                            $driver->release();

                            if ($pdo->inTransaction()) {
                                $pdo->rollBack();
                            }

                            $pool->put($pdo);
                        }
                    }
                }

                // 回收 redis 连接池
                if (isset(self::$cache[$cid]['redis'])) {
                    foreach (self::$cache[$cid]['redis'] as $name => $driver) {
                        if (isset(self::$cache['redis']['pools'][$name])) {
                            $pool = self::$cache['redis']['pools'][$name];

                            /**
                             * @var \Be\Redis\Driver $driver
                             */
                            $redis = $driver->getRedis();
                            $driver->release();
                            $pool->put($redis);
                        }
                    }
                }
            }

            // 释放用户协程创建的对象
            unset(self::$cache[$cid]);

        } else {

            // 关闭 SESSION
            if (isset(self::$cache['session'])) {
                /**
                 * @var \Be\Session\Driver
                 */
                $driver = self::$cache['session'];
                $driver->close();
            }

        }
    }
}
