<?php

namespace Be;

use Be\Runtime\RuntimeException;

/**
 *  BE系统资源工厂
 * @package Be\Mf
 *
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
        if (self::getRuntime()->getMode() == 'Swoole') {
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
        if (self::getRuntime()->getMode() == 'Swoole') {
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
        if (self::getRuntime()->getMode() == 'Swoole') {
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
        if (self::getRuntime()->getMode() == 'Swoole') {
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
        if (self::getRuntime()->getMode() == 'Swoole') {
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
        if (isset(self::$cache['config'][$name])) return self::$cache['config'][$name];
        self::$cache['config'][$name] = self::newConfig($name);
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
        $appName = $parts[0];
        $configName = $parts[1];

        $class = 'Be\\Data\\' . $appName . '\\Config\\' . $configName;
        if (class_exists($class)) {
            return new $class();
        }

        $class = 'Be\\App\\' . $appName . '\\Config\\' . $configName;
        if (class_exists($class)) {
            $instance = new $class();
            // ConfigHelper::update($name, $instance);
            return $instance;
        }

        throw new RuntimeException('Config ' . $name . ' doesn\t exist!');
    }

    /**
     * 获取日志记录器
     *
     * @return \Be\Log\Driver
     */
    public static function getLog()
    {
        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (!isset(self::$cache[$cid]['log'])) {
                self::$cache[$cid]['log'] = new \Be\Log\Driver();
            }
            return self::$cache[$cid]['log'];
        } else {
            if (!isset(self::$cache['log'])) {
                self::$cache['log'] = new \Be\Log\Driver();
            }
            return self::$cache['log'];
        }
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
        $class = 'Be\\' . implode('\\', $parts) . '\\Property';
        if (!class_exists($class)) throw new RuntimeException('Property ' . $name . ' doesn\'t exist!');
        $instance = new $class();

        self::$cache['property'][$name] = $instance;
        return self::$cache['property'][$name];
    }

    /**
     * 获取SESSION
     *
     * @return \Be\Session\Driver
     */
    public static function getSession()
    {
        $config = self::getConfig('System.Session');
        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['session'])) {
                return self::$cache[$cid]['session'];
            }

            $driver = '\\Be\\Session\\Driver\\Swoole\\' . $config->driver;
            if (!class_exists($driver)) {
                throw new RuntimeException('Session driver' . $config->driver . ' doesn\'t exist!');
            }
            self::$cache[$cid]['session'] = new $driver($config);
            return self::$cache[$cid]['session'];
        } else {
            if (isset(self::$cache['session'])) {
                return self::$cache['session'];
            }

            $driver = '\\Be\\Session\\Driver\\Common\\' . $config->driver;
            if (!class_exists($driver)) {
                throw new RuntimeException('Session driver' . $config->driver . ' doesn\'t exist!');
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
    public static function getCache()
    {
        $config = self::getConfig('System.Cache');
        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['cache'])) {
                return self::$cache[$cid]['cache'];
            }

            $driver = '\\Be\\Cache\\Driver\\' . $config->driver;
            if (!class_exists($driver)) {
                throw new RuntimeException('Cache driver' . $config->driver . ' doesn\'t exist!');
            }
            self::$cache[$cid]['cache'] = new $driver($config);
            return self::$cache[$cid]['cache'];
        } else {
            if (isset(self::$cache['cache'])) {
                return self::$cache['cache'];
            }

            $driver = '\\Be\\Cache\\Driver\\' . $config->driver;
            if (!class_exists($driver)) {
                throw new RuntimeException('Cache driver' . $config->driver . ' doesn\'t exist!');
            }
            self::$cache['cache'] = new $driver($config);
            return self::$cache['cache'];
        }
    }

    /**
     * 初始货Redis连接池
     */
    public static function initRedisPools()
    {
        if (self::$cache['redis']['pools'] === null) {
            self::$cache['redis']['pools'] = [];
            $config = Be::getConfig('System.Redis');
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
        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['redis'][$name])) return self::$cache[$cid]['redis'][$name];

            $config = Be::getConfig('System.Redis');
            if (!isset($config->$name)) {
                throw new RuntimeException('Redis config item (' . $name . ') doesn\'t exist!');
            }

            $driver = null;
            if (isset(self::$cache['redis']['pools'][$name])) {
                $pool = self::$cache['redis']['pools'][$name];
                $redis = $pool->get();
                $driver = new \Be\Redis\Driver($name, $redis);
            } else {
                $driver = new \Be\Redis\Driver($name);
            }

            self::$cache[$cid]['redis'][$name] = $driver;
            return self::$cache[$cid]['redis'][$name];
        } else {
            if (isset(self::$cache['redis'][$name])) return self::$cache['redis'][$name];
            self::$cache['redis'][$name] = self::newRedis($name);
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
        $config =  Be::getConfig('System.Redis');
        if (!isset($config->$name)) {
            throw new RuntimeException('Redis config item (' . $name . ') doesn\'t exist!');
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
        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['es'])) return self::$cache[$cid]['es'];
            self::$cache[$cid]['es'] = self::newEs();
            return self::$cache[$cid]['es'];
        } else {
            if (isset(self::$cache['es'])) {
                return self::$cache['es'];
            }
            self::$cache['es'] = self::newEs();
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
        $config = self::getConfig('System.Es');
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

            $config = self::getConfig('System.Db');
            foreach ($config as $k => $v) {
                if ($v['driver'] != 'mysql') continue;

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
        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['db'][$name])) return self::$cache[$cid]['db'][$name];

            $config = self::getConfig('System.Db');
            if (!isset($config->$name)) {
                throw new RuntimeException('Db config item (' . $name . ') doesn\'t exist!');
            }
            $configData = $config->$name;

            $driver = null;
            switch ($configData['driver']) {
                case 'mysql':
                    if (isset(self::$cache['db']['pools'][$name])) {
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
        $config = Be::getConfig('System.Db');
        if (!isset($config->$name)) {
            throw new RuntimeException('Db config item (' . $name . ') doesn\'t exist!');
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
        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['tuple'][$db][$name])) return self::$cache[$cid]['tuple'][$db][$name];
            self::$cache[$cid]['tuple'][$db][$name] = self::newTuple($name, $db);
            return self::$cache[$cid]['tuple'][$db][$name];
        } else {
            if (isset(self::$cache['tuple'][$db][$name])) {
                return self::$cache['tuple'][$db][$name];
            }
            self::$cache['tuple'][$db][$name] = self::newTuple($name, $db);
            return self::$cache['tuple'][$db][$name];
        }
    }

    /**
     * 新创建一个数据库行记灵对象
     *
     * @param string $name 数据库行记灵对象名
     * @param string $db 库名
     * @return \Be\Db\Tuple | mixed
     */
    public static function newTuple(string $name, string $db = 'master')
    {
        $runtime = self::getRuntime();
        $path = $runtime->getCachePath() . '/Tuple/' . $db . '/' . $name . '.php';
        $configSystem = Be::getConfig('System.System');
        if ($configSystem->developer || !file_exists($path)) {
            \Be\Db\DbHelper::updateTuple($name, $db);
            include_once $path;
        }

        $class = 'Be\\Cache\\Tuple\\' . $db . '\\' . $name;
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
        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['table'][$db][$name])) return self::$cache[$cid]['table'][$db][$name];
            self::$cache[$cid]['table'][$db][$name] = self::newTable($name, $db);
            return self::$cache[$cid]['table'][$db][$name];
        } else {
            if (isset(self::$cache['table'][$db][$name])) {
                return self::$cache['table'][$db][$name];
            }
            self::$cache['table'][$db][$name] = self::newTable($name, $db);
            return self::$cache['table'][$db][$name];
        }
    }

    /**
     * 新创建一个数据库表对象
     *
     * @param string $name 表名
     * @param string $db 库名
     * @return \Be\Db\Table
     */
    public static function newTable(string $name, string $db = 'master')
    {
        $runtime = self::getRuntime();
        $path = $runtime->getCachePath() . '/Table/' . $db . '/' . $name . '.php';
        $configSystem = Be::getConfig('System.System');
        if ($configSystem->developer || !file_exists($path)) {
            \Be\Db\DbHelper::updateTable($name, $db);
            include_once $path;
        }

        $class = 'Be\\Cache\\Table\\' . $db . '\\' . $name;
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

        $runtime = self::getRuntime();
        $path = $runtime->getCachePath() . '/TableProperty/' . $db . '/' . $name . '.php';
        $configSystem = Be::getConfig('System.System');
        if ($configSystem->developer || !file_exists($path)) {
            \Be\Db\DbHelper::updateTableProperty($name, $db);
            include_once $path;
        }

        $class = 'Be\\Cache\\TableProperty\\' . $db . '\\' . $name;
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
        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['mongoDb'][$name])) return self::$cache[$cid]['mongoDb'][$name];
            self::$cache[$cid]['mongoDb'][$name] = self::newMongoDb($name);
            return self::$cache[$cid]['mongoDb'][$name];
        } else {
            if (isset(self::$cache['mongoDb'][$name])) {
                return self::$cache['mongoDb'][$name];
            }
            self::$cache['mongoDb'][$name] = self::newMongoDb($name);
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
        $config = Be::getConfig('System.MongoDb');
        if (!isset($config->$name)) {
            throw new RuntimeException('MongoDb config item (' . $name . ') doesn\'t exist!');
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
        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['service'][$name])) return self::$cache[$cid]['service'][$name];
            self::$cache[$cid]['service'][$name] = self::newService($name);
            return self::$cache[$cid]['service'][$name];
        } else {
            if (isset(self::$cache['service'][$name])) {
                return self::$cache['service'][$name];
            }
            self::$cache['service'][$name] = self::newService($name);
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
        $app = array_shift($parts);
        $class = 'Be\\App\\' . $app . '\\Service\\' . implode('\\', $parts);
        return new $class();
    }

    /**
     * 获取指定的一个后台服务
     *
     * @param string $name 服务名
     * @return mixed
     */
    public static function getAdminService(string $name)
    {
        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['adminService'][$name])) return self::$cache[$cid]['adminService'][$name];
            self::$cache[$cid]['adminService'][$name] = self::newAdminService($name);
            return self::$cache[$cid]['adminService'][$name];
        } else {
            if (isset(self::$cache['adminService'][$name])) {
                return self::$cache['adminService'][$name];
            }
            self::$cache['adminService'][$name] = self::newAdminService($name);
            return self::$cache['adminService'][$name];
        }
    }

    /**
     * 新创建一个后台服务
     *
     * @param string $name 服务名
     * @return mixed
     */
    public static function newAdminService(string $name)
    {
        $parts = explode('.', $name);
        $app = array_shift($parts);
        $class = 'Be\\App\\' . $app . '\\AdminService\\' . implode('\\', $parts);
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
        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['lib'][$name])) return self::$cache[$cid]['lib'][$name];
            self::$cache[$cid]['lib'][$name] = self::newLib($name);
            return self::$cache[$cid]['lib'][$name];
        } else {
            if (isset(self::$cache['lib'][$name])) {
                return self::$cache['lib'][$name];
            }
            self::$cache['lib'][$name] = self::newLib($name);
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
            $class = 'Be\\Lib\\' . $name . '\\' . $name;
        } else {
            $class = $name;
        }
        if (!class_exists($class)) throw new RuntimeException('Lib ' . $class . ' doesn\'t exist!');

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
        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['plugin'][$name])) return self::$cache[$cid]['plugin'][$name];
            self::$cache[$cid]['plugin'][$name] = self::newPlugin($name);
            return self::$cache[$cid]['plugin'][$name];
        } else {
            if (isset(self::$cache['plugin'][$name])) {
                return self::$cache['plugin'][$name];
            }
            self::$cache['plugin'][$name] = self::newPlugin($name);
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
        $class = 'Be\\Plugin\\' . $name . '\\' . $name;
        if (!class_exists($class)) {
            throw new RuntimeException('Plugin ' . $name . ' doesn\'t exist!');
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
        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['adminPlugin'][$name])) return self::$cache[$cid]['adminPlugin'][$name];
            self::$cache[$cid]['adminPlugin'][$name] = self::newAdminPlugin($name);
            return self::$cache[$cid]['adminPlugin'][$name];
        } else {
            if (isset(self::$cache['adminPlugin'][$name])) {
                return self::$cache['adminPlugin'][$name];
            }
            self::$cache['adminPlugin'][$name] = self::newAdminPlugin($name);
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
        $class = 'Be\\AdminPlugin\\' . $name . '\\' . $name;
        if (!class_exists($class)) {
            throw new RuntimeException('AdminPlugin ' . $name . ' doesn\'t exist!');
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
            $property = self::getProperty($type . '.' . $name);
            if (isset($property->theme)) {
                $theme = $property->theme;
            } else {
                $configSystem = self::getConfig('System.System');
                $theme = $configSystem->theme;
            }
        }

        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['template'][$theme][$template])) return self::$cache[$cid]['template'][$theme][$template];
        } else {
            if (isset(self::$cache['template'][$theme][$template])) return self::$cache['template'][$theme][$template];
        }

        $runtime = self::getRuntime();
        $path = $runtime->getCachePath() . '/Template/' . $theme . '/' . $type . '/' . $name . '/' . implode('/', $parts) . '.php';

        $configSystem = self::getConfig('System.System');
        if ($configSystem->developer || !file_exists($path)) {
            \Be\Template\TemplateHelper::update($template, $theme);
        }

        $class = 'Be\\Cache\\Template\\' . $theme . '\\' . $type . '\\' . $name . '\\' . implode('\\', $parts);

        if (self::getRuntime()->getMode() == 'Swoole') {
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
            $property = self::getProperty($type . '.' . $name);
            if (isset($property->theme)) {
                $theme = $property->theme;
            } else {
                $configAdmin = self::getConfig('System.Admin');
                $theme = $configAdmin->theme;
            }
        }

        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['adminTemplate'][$theme][$template])) return self::$cache[$cid]['adminTemplate'][$theme][$template];
        } else {
            if (isset(self::$cache['adminTemplate'][$theme][$template])) return self::$cache['adminTemplate'][$theme][$template];
        }

        $runtime = self::getRuntime();
        $path = $runtime->getCachePath() . '/AdminTemplate/' . $theme . '/' . $type . '/' . $name . '/' . implode('/', $parts) . '.php';

        $configSystem = self::getConfig('System.System');
        if ($configSystem->developer || !file_exists($path)) {
            \Be\AdminTemplate\AdminTemplateHelper::update($template, $theme);
        }

        $class = 'Be\\Cache\\AdminTemplate\\' . $theme . '\\' . $type . '\\' . $name . '\\' . implode('\\', $parts);

        if (self::getRuntime()->getMode() == 'Swoole') {
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
    public static function getMenu()
    {
        if (isset(self::$cache['Menu'])) return self::$cache['Menu'];

        $path = self::getRuntime()->getCachePath() . '/Menu.php';

        $configSystem = self::getConfig('System.System');
        if ($configSystem->developer || !file_exists($path)) {
            $service = Be::getAdminService('System.Menu');
            $service->update();
            include_once $path;
        }

        $class = 'Be\\Cache\\Menu';
        self::$cache['Menu'] = new $class();
        return self::$cache['Menu'];
    }

    /**
     * 获取指定的一个菜单
     *
     * @return \Be\AdminMenu\Driver
     */
    public static function getAdminMenu()
    {
        if (isset(self::$cache['adminMenu'])) return self::$cache['adminMenu'];

        $path = self::getRuntime()->getCachePath() . '/AdminMenu.php';

        $configSystem = self::getConfig('System.System');
        if ($configSystem->developer || !file_exists($path)) {
            $service = Be::getAdminService('System.AdminMenu');
            $service->update();
            include_once $path;
        }

        $class = 'Be\\Cache\\AdminMenu';
        self::$cache['adminMenu'] = new $class();
        return self::$cache['adminMenu'];
    }

    /**
     * 获取指定的一个角色信息
     *
     * @param int $roleId 角色ID
     * @return \Be\AdminUser\AdminRole
     */
    public static function getAdminRole(int $roleId)
    {
        if (isset(self::$cache[$roleId])) return self::$cache[$roleId];

        $path = self::getRuntime()->getCachePath() . '/AdminRole/AdminRole' . $roleId . '.php';

        $configSystem = self::getConfig('System.System');
        if ($configSystem->developer || !file_exists($path)) {
            $service = Be::getAdminService('System.AdminRole');
            $service->updateAdminRole($roleId);
            include_once $path;
        }

        $class = 'Be\\Cache\\AdminRole\\AdminRole' . $roleId;
        self::$cache[$roleId] = new $class();
        return self::$cache[$roleId];
    }

    /**
     * 获取当前用户
     *
     * @return \Be\AdminUser\AdminUser | mixed
     */
    public static function getAdminUser()
    {
        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['adminUser'])) {
                return self::$cache[$cid]['adminUser'];
            }

            $user = Be::getSession()->get('_adminUser');
            self::$cache[$cid]['adminUser'] = new \Be\AdminUser\AdminUser($user);
            return self::$cache[$cid]['adminUser'];
        } else {
            if (isset(self::$cache['adminUser'])) {
                return self::$cache['adminUser'];
            }

            $user = Be::getSession()->get('_adminUser');
            self::$cache['adminUser'] = new \Be\AdminUser\AdminUser($user);
            return self::$cache['adminUser'];
        }
    }

    /**
     * 获取当前用户
     *
     * @return \Be\User\User | mixed
     */
    public static function getUser()
    {
        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();
            if (isset(self::$cache[$cid]['user'])) {
                return self::$cache[$cid]['user'];
            }

            $user = Be::getSession()->get('_user');
            self::$cache[$cid]['user'] = new \Be\User\User($user);
            return self::$cache[$cid]['user'];
        } else {
            if (isset(self::$cache['user'])) {
                return self::$cache['user'];
            }

            $user = Be::getSession()->get('_user');
            self::$cache['user'] = new \Be\User\User($user);
            return self::$cache['user'];
        }
    }

    /**
     * 设置上下文
     *
     * @param string $name
     * @param $value
     */
    public static function setContext(string $name, $value)
    {
        if (self::getRuntime()->getMode() == 'Swoole') {
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
        if (self::getRuntime()->getMode() == 'Swoole') {
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
     * 回收资源
     */
    public static function gc()
    {
        if (self::getRuntime()->getMode() == 'Swoole') {
            $cid = \Swoole\Coroutine::getCid();

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
