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
        self::$cache['request'] = $request;
    }

    /**
     * 获取请求对象
     *
     * @return \Be\Request\Driver
     */
    public static function getRequest()
    {
        if (isset(self::$cache['request'])) {
            return self::$cache['request'];
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
        self::$cache['response'] = $response;
    }

    /**
     * 获取输出对象
     *
     * @return \Be\Response\Driver | null
     */
    public static function getResponse()
    {
        if (isset(self::$cache['response'])) {
            return self::$cache['response'];
        }

        return null;
    }

    /**
     * 获取指定的配置文件，单例
     *
     * @param string $name 配置文件名
     * @return object
     */
    public static function getConfig(string $name): object
    {
        if (!isset(self::$cache['config'][$name])) {
            self::$cache['config'][$name] = self::newConfig($name);
        }
        return self::$cache['config'][$name];
    }

    /**
     * 获取指定的配置文件，创新新实例
     *
     * @param string $name 配置文件名
     * @return object
     */
    public static function newConfig(string $name): object
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
        }

        if ($isPageConfig) {
            $class = '\\Be\\Data\\' . $type . '\\' . $catalog . '\\Config\\Page';
            if (!class_exists($class)) {
                $class = '\\Be\\' . $type . '\\' . $catalog . '\\Config\\Page';
                if (!class_exists($class)) {
                    throw new RuntimeException('Config (' . $type . '.' . $catalog . '.Page) does not exist!');
                }
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
     * 获取 日志记录器 单例
     *
     * @return \Be\Log\Driver
     */
    public static function getLog(): \Be\Log\Driver
    {
        if (!isset(self::$cache['log'])) {
            self::$cache['log'] = self::newLog();
        }
        return self::$cache['log'];
    }

    /**
     * 创建 日志记录器 新的实例
     *
     * @return \Be\Log\Driver
     */
    public static function newLog(): \Be\Log\Driver
    {
        return new \Be\Log\Driver\File();
    }

    /**
     * 获取一个属性 单例
     *
     * @param string $name 名称
     * @return \Be\Property\Driver
     * @throws RuntimeException
     */
    public static function getProperty(string $name): \Be\Property\Driver
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

    /**
     * 获取 缓存对象 单例
     *
     * @return \Be\Cache\Driver
     */
    public static function getCache(): \Be\Cache\Driver
    {
        if (!isset(self::$cache['cache'])) {
            self::$cache['cache'] = self::newCache();
        }
        return self::$cache['cache'];
    }

    /**
     * 创建 缓存对象 实例
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
     * 获取 存储 单例
     *
     * @return \Be\Storage\Driver
     */
    public static function getStorage(): \Be\Storage\Driver
    {
        if (!isset(self::$cache['storage'])) {
            self::$cache['storage'] = self::newStorage();
        }
        return self::$cache['storage'];
    }

    /**
     * 创建 存储 实例
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
     * 获取Redis对象 单例
     *
     * @param string $name Redis名
     * @return \Be\Redis\Driver|\Redis
     * @throws RuntimeException
     */
    public static function getRedis(string $name = 'master'): \Be\Redis\Driver
    {
        if (!isset(self::$cache['redis'][$name])) {
            self::$cache['redis'][$name] = self::newRedis($name);
        }
        return self::$cache['redis'][$name];
    }

    /**
     * 创建一个 Redis对象 实例
     *
     * @param string $name Redis名
     * @return \Be\Redis\Driver|\Redis
     * @throws RuntimeException
     */
    public static function newRedis(string $name = 'master'): \Be\Redis\Driver
    {
        $config = Be::getConfig('App.System.Redis');
        if (!isset($config->$name)) {
            throw new RuntimeException('Redis config item (' . $name . ') does not exist!');
        }

        return new \Be\Redis\Driver($name);
    }

    /**
     * 获取ES对象 单例
     *
     * @return \Elasticsearch\Client
     * @throws RuntimeException
     */
    public static function getEs(): \Elasticsearch\Client
    {
        if (!isset(self::$cache['es'])) {
            self::$cache['es'] = self::newEs();
        }
        return self::$cache['es'];
    }

    /**
     * 创建一个 ES对象 实例
     *
     * @return \Elasticsearch\Client
     * @throws RuntimeException
     */
    public static function newEs(): \Elasticsearch\Client
    {
        $config = self::getConfig('App.System.Es');
        $driver = \Elasticsearch\ClientBuilder::create()->setHosts($config->hosts)->build();
        return $driver;
    }

    /**
     * 获取 数据库对象 单例
     *
     * @param string $name 数据库名
     * @return \Be\Db\Driver
     * @throws RuntimeException
     */
    public static function getDb(string $name = 'master'): \Be\Db\Driver
    {
        if (isset(self::$cache['db'][$name])) return self::$cache['db'][$name];
        self::$cache['db'][$name] = self::newDb($name);
        return self::$cache['db'][$name];
    }

    /**
     * 创建一个 数据库对象 实例
     *
     * @param string $name 数据库名
     * @return \Be\Db\Driver
     * @throws RuntimeException
     */
    public static function newDb(string $name = 'master'): \Be\Db\Driver
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
     * @return \Be\Db\Tuple
     */
    public static function getTuple(string $name, string $db = 'master'): \Be\Db\Tuple
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
    public static function getTable(string $name, string $db = 'master'): \Be\Db\Table
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
    public static function getTableProperty(string $name, string $db = 'master'): \Be\Db\TableProperty
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
     * 获取 MongoDb 对象 单例
     *
     * @param string $name 名称
     * @return \Be\MongoDb\Driver
     */
    public static function getMongoDb(string $name): \Be\MongoDb\Driver
    {
        if (!isset(self::$cache['mongoDb'][$name])) {
            self::$cache['mongoDb'][$name] = self::newMongoDb($name);
        }
        return self::$cache['mongoDb'][$name];
    }

    /**
     * 创建一个 MongoDb 对象 实例
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
     * 获取指定的一个服务 单例
     *
     * @param string $name 服务名
     * @return mixed
     */
    public static function getService(string $name)
    {
        if (!isset(self::$cache['service'][$name])) {
            self::$cache['service'][$name] = self::newService($name);
        }
        return self::$cache['service'][$name];
    }

    /**
     * 新创建一个服务 实例
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
        if (!isset(self::$cache['lib'][$name])) {
            self::$cache['lib'][$name] = self::newLib($name);
        }
        return self::$cache['lib'][$name];
    }

    /**
     * 创建 一个指定的库 实例
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
     * 获取指定的一个扩展 单例
     *
     * @param string $name 扩展名
     * @return \Be\Plugin\Driver
     * @throws RuntimeException
     */
    public static function getPlugin(string $name): \Be\Plugin\Driver
    {
        if (!isset(self::$cache['plugin'][$name])) {
            self::$cache['plugin'][$name] = self::newPlugin($name);
        }
        return self::$cache['plugin'][$name];
    }

    /**
     * 创建一个新的指定的扩展 实例
     *
     * @param string $name 扩展名
     * @return \Be\Plugin\Driver
     * @throws RuntimeException
     */
    public static function newPlugin(string $name): \Be\Plugin\Driver
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
     * @return \Be\AdminPlugin\Driver
     * @throws RuntimeException
     */
    public static function getAdminPlugin(string $name): \Be\AdminPlugin\Driver
    {
        if (!isset(self::$cache['adminPlugin'][$name])) {
            self::$cache['adminPlugin'][$name] = self::newAdminPlugin($name);
        }
        return self::$cache['adminPlugin'][$name];
    }

    /**
     * 新创建一个指定的后台扩展
     *
     * @param string $name 扩展名
     * @return \Be\AdminPlugin\Driver
     * @throws RuntimeException
     */
    public static function newAdminPlugin(string $name): \Be\AdminPlugin\Driver
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
    public static function getTemplate(string $template, string $theme = null): \Be\Template\Driver
    {
        $parts = explode('.', $template);
        $type = array_shift($parts);
        $name = array_shift($parts);

        if ($theme === null) {
            $theme = self::getConfig('App.System.Theme')->default;
        }

        if (isset(self::$cache['template'][$theme][$template])) {
            return self::$cache['template'][$theme][$template];
        }

        $runtime = self::getRuntime();
        $path = $runtime->getRootPath() . '/data/Runtime/Template/' . $theme . '/' . $type . '/' . $name . '/' . implode('/', $parts) . '.php';
        if (!file_exists($path)) {
            \Be\Template\TemplateHelper::update($template, $theme);
        } else {
            if (self::getConfig('App.System.System')->developer) {
                if (\Be\Template\TemplateHelper::hasChange($template, $theme)) {
                    \Be\Template\TemplateHelper::update($template, $theme);
                }
            }
        }

        $class = '\\Be\\Data\\Runtime\\Template\\' . $theme . '\\' . $type . '\\' . $name . '\\' . implode('\\', $parts);

        self::$cache['template'][$theme][$template] = new $class();
        return self::$cache['template'][$theme][$template];
    }

    /**
     * 获取指定的一个后台模板
     *
     * @param string $template 模板名
     * @param string $theme 主题名
     * @return \Be\Template\Driver
     * @throws RuntimeException
     */
    public static function getAdminTemplate(string $template, string $theme = null): \Be\Template\Driver
    {
        $parts = explode('.', $template);
        $type = array_shift($parts);
        $name = array_shift($parts);

        if ($theme === null) {
            $theme = self::getConfig('App.System.AdminTheme')->default;
        }

        $runtime = self::getRuntime();
        if (isset(self::$cache['adminTemplate'][$theme][$template])) {
            return self::$cache['adminTemplate'][$theme][$template];
        }

        $path = $runtime->getRootPath() . '/data/Runtime/AdminTemplate/' . $theme . '/' . $type . '/' . $name . '/' . implode('/', $parts) . '.php';
        if (!file_exists($path)) {
            \Be\Template\TemplateHelper::update($template, $theme, true);
        } else {
            if (self::getConfig('App.System.System')->developer) {
                if (\Be\Template\TemplateHelper::hasChange($template, $theme, true)) {
                    \Be\Template\TemplateHelper::update($template, $theme, true);
                }
            }
        }

        $class = '\\Be\\Data\\Runtime\\AdminTemplate\\' . $theme . '\\' . $type . '\\' . $name . '\\' . implode('\\', $parts);
        self::$cache['adminTemplate'][$theme][$template] = new $class();
        return self::$cache['adminTemplate'][$theme][$template];

    }

    /**
     * 获取指定的一个菜单
     *
     * @return \Be\Menu\Driver
     */
    public static function getMenu($name): \Be\Menu\Driver
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
     * 获取指定的一个语言包
     *
     * @param string $package 语言包包
     * @param string $languageName 语言名
     * @return \Be\Language\Driver
     */
    public static function getLanguage(string $package, string $languageName = null): \Be\Language\Driver
    {
        $runtime = self::getRuntime();

        if ($languageName === null) {
            if (!$runtime->isSwooleMode() || $runtime->isWorkerProcess()) {
                $languageName = self::getRequest()->getLanguageName();
            } else {
                $languageName = self::getConfig('App.System.Language')->default;
            }
        }

        $underlineLanguageName = str_replace('-', '_', $languageName);

        if (isset(self::$cache['Language'][$package][$languageName])) return self::$cache['Language'][$package][$languageName];

        $path = $runtime->getRootPath() . '/data/Runtime/Language/' . str_replace('.', '/', $package) . '/' . $underlineLanguageName . '.php';
        if (!file_exists($path)) {
            \Be\Language\LanguageHelper::update($package, $languageName);
        } else {
            if (self::getConfig('App.System.System')->developer) {
                if (\Be\Language\LanguageHelper::hasChange($package, $languageName)) {
                    \Be\Language\LanguageHelper::update($package, $languageName);
                }
            }
        }

        $class = '\\Be\\Data\\Runtime\\Language\\' . str_replace('.', '\\', $package) . '\\' . $underlineLanguageName;
        self::$cache['Language'][$package][$languageName] = new $class();
        return self::$cache['Language'][$package][$languageName];
    }

    /**
     * 获取指定的一个后台菜单
     *
     * @return \Be\AdminMenu\Driver
     */
    public static function getAdminMenu(): \Be\AdminMenu\Driver
    {
        if (isset(self::$cache['adminMenu'])) return self::$cache['adminMenu'];

        $path = self::getRuntime()->getRootPath() . '/data/Runtime/AdminMenu.php';
        if (!file_exists($path)) {
            $service = Be::getService('App.System.Admin.AdminMenu');
            $service->update();
        } else {
            if (self::getConfig('App.System.System')->developer) {
                $service = Be::getService('App.System.Admin.AdminMenu');
                if ($service->hasChange()) {
                    $service->update();
                }
            }
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
    public static function getAdminRole(string $roleId): \Be\AdminUser\AdminRole
    {
        if (isset(self::$cache['adminRole'][$roleId])) return self::$cache['adminRole'][$roleId];

        $suffix = str_replace('-', '', $roleId);
        $path = self::getRuntime()->getRootPath() . '/data/Runtime/AdminRole/AdminRole_' . $suffix . '.php';
        if (!file_exists($path)) {
            $service = Be::getService('App.System.Admin.AdminRole');
            $service->updateAdminRole($roleId);
        } else {
            if (self::getConfig('App.System.System')->developer) {
                $service = Be::getService('App.System.Admin.AdminRole');
                if ($service->hasChange($roleId)) {
                    $service->update();
                }
            }
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
    public static function getAdminPermission(): \Be\AdminUser\AdminPermission
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
     * @param object|null $adminUser
     */
    public static function setAdminUser($adminUser = null)
    {
        Be::getSession()->set('Be-AdminUser', $adminUser);
        if (isset(self::$cache['adminUser'])) {
            if ($adminUser === null) {
                unset(self::$cache['adminUser']);
            } else {
                self::$cache['adminUser'] = new \Be\AdminUser\AdminUser($adminUser);
            }
        }
    }

    /**
     * 获取当前后台用户
     *
     * @return \Be\AdminUser\AdminUser|object
     */
    public static function getAdminUser(): \Be\AdminUser\AdminUser
    {
        if (isset(self::$cache['adminUser'])) {
            return self::$cache['adminUser'];
        }

        $user = Be::getSession()->get('Be-AdminUser');
        self::$cache['adminUser'] = new \Be\AdminUser\AdminUser($user);
        return self::$cache['adminUser'];
    }

    /**
     * 设置当前用户
     *
     * @param object|null $user
     */
    public static function setUser($user = null)
    {
        Be::getSession()->set('Be-User', $user);
        if (isset(self::$cache['user'])) {
            if ($user === null) {
                unset(self::$cache['user']);
            } else {
                self::$cache['user'] = new \Be\User\User($user);
            }
        }
    }

    /**
     * 获取当前用户
     *
     * @return \Be\User\User|object
     */
    public static function getUser(): \Be\User\User
    {
        if (isset(self::$cache['user'])) {
            return self::$cache['user'];
        }

        $user = Be::getSession()->get('Be-User');
        self::$cache['user'] = new \Be\User\User($user);
        return self::$cache['user'];
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
        self::$cache['context'][$name] = $value;
    }

    /**
     * 获取上下文
     *
     * @param string $name
     * @return mixed|null
     */
    public static function getContext(string $name)
    {
        if (isset(self::$cache['context'][$name])) {
            return self::$cache['context'][$name];
        }
        return null;
    }

    /**
     * 上下文是否存在
     *
     * @param string $name
     * @return bool
     */
    public static function hasContext(string $name): bool
    {
        return isset(self::$cache['context'][$name]);
    }

    /**
     * 回收资源
     */
    public static function gc()
    {
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
