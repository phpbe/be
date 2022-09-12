<?php

namespace Be\Runtime\Driver;

use Be\Be;
use Be\Runtime\Driver;
use Be\Util\File\Mime;

/**
 *  运行时
 */
class Swoole extends Driver
{

    protected $mode = 'Swoole'; // 运行模式 Swoole / Common


    /**
     * @var \Swoole\Http\Server
     */
    private $swooleHttpServer = null;

    public function execute()
    {
        if ($this->swooleHttpServer !== null) {
            return;
        }

        \Co::set(['hook_flags' => SWOOLE_HOOK_ALL]);

        $configSystem = Be::getConfig('App.System.System');
        date_default_timezone_set($configSystem->timezone);

        $configServer = Be::getConfig('App.System.Server');
        $this->swooleHttpServer = new \Swoole\Http\Server($configServer->host, $configServer->port);

        $setting = [
            'enable_coroutine' => true,
            'task_worker_num' => 2,
            'task_enable_coroutine' => true,
        ];

        if ($configServer->reactor_num > 0) {
            $setting['reactor_num'] = $configServer->reactor_num;
        }

        if ($configServer->worker_num > 0) {
            $setting['worker_num'] = $configServer->worker_num;
        }

        if ($configServer->max_request > 0) {
            $setting['max_request'] = $configServer->max_request;
        }

        if ($configServer->max_conn > 0) {
            $setting['max_conn'] = $configServer->max_conn;
        }

        if ($configServer->task_worker_num > 0) {
            $setting['task_worker_num'] = $configServer->task_worker_num;
        }

        if ($configServer->task_max_request > 0) {
            $setting['task_max_request'] = $configServer->task_max_request;
        }

        $this->swooleHttpServer->set($setting);

        // 初始化数据库，Redis连接池
        Be::initDbPools();
        Be::initRedisPools();

        if ($configServer->clearRuntimeOnStart) {
            $dir = Be::getRuntime()->getRootPath() . '/data/Runtime';
            \Be\Util\File\Dir::rm($dir);
        } else {
            $sessionConfig = Be::getConfig('App.System.Session');
            if ($sessionConfig->driver === 'File') {
                $dir = Be::getRuntime()->getRootPath() . '/data/session';
                \Be\Util\File\Dir::rm($dir);
            }
        }

        $this->swooleHttpServer->on('request', function ($swooleRequest, $swooleResponse) {
            /**
             * @var \Swoole\Http\Response $swooleResponse
             */
            $swooleResponse->header('Server', 'BE', false);
            $uri = $swooleRequest->server['request_uri'];

            $ext = strrchr($uri, '.');
            if ($ext) {
                $ext = strtolower(substr($ext, 1));
                if (isset(\Be\Util\File\Mime::MAPPING[$ext])) {
                    $rootPath = Be::getRuntime()->getRootPath() . '/www';

                    if (strpos($uri, '../') !== false) {
                        $swooleResponse->status(404);
                        $swooleResponse->end();
                        return true;
                    }

                    if (file_exists($rootPath . $uri)) {
                        $swooleResponse->header('Content-Type', \Be\Util\File\Mime::MAPPING[$ext], false);

                        //缓存
                        $lastModified = gmdate('D, d M Y H:i:s', filemtime($rootPath . $uri)) . ' GMT';
                        if (isset($swooleRequest->header['if-modified-since']) && $swooleRequest->header['if-modified-since'] === $lastModified) {
                            $swooleResponse->status(304);
                            $swooleResponse->end();
                            return true;
                        }

                        $swooleResponse->header('Last-Modified', $lastModified, false);

                        //发送Expires头标，设置当前缓存的文档过期时间，GMT格式
                        $swooleResponse->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT', false);

                        //发送Cache_Control头标，设置xx秒以后文档过时,可以代替Expires，如果同时出现，max-age优先。
                        $swooleResponse->header('Cache-Control', 'max-age=31536000', false);
                        $swooleResponse->header('Pragma', 'max-age=31536000', false);

                        $swooleResponse->sendfile($rootPath . $uri);
                        return true;
                    }

                    if (strpos($uri, '/favicon.ico') !== false) {
                        $swooleResponse->end();
                        return true;
                    }
                }
            }


            $swooleRequest->request = null;
            if ($swooleRequest->get !== null) {
                if ($swooleRequest->post !== null) {
                    $swooleRequest->request = array_merge($swooleRequest->get, $swooleRequest->post);
                } else {
                    $swooleRequest->request = $swooleRequest->get;
                }
            } else {
                if ($swooleRequest->post !== null) {
                    $swooleRequest->request = $swooleRequest->post;
                }
            }

            $request = new \Be\Request\Driver\Swoole($swooleRequest);
            $response = new \Be\Response\Driver\Swoole($swooleResponse);

            Be::setRequest($request);
            Be::setResponse($response);

            // 启动 session
            Be::getSession()->start();

            try {
                // 检查网站配置， 是否暂停服务
                $configSystem = Be::getConfig('App.System.System');

                $admin = $request->get($this->adminAlias, false);
                if ($admin !== false) {
                    $admin = true;
                }
                $app = null;
                $controller = null;
                $action = null;

                // 从网址中提取出 路径
                if ($configSystem->urlRewrite) {

                    // 移除网址后缀 如：.html
                    if ($configSystem->urlRewrite === '1') {
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
                    if (!$admin && $configSystem->urlRewrite === '2') {
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
                                        $swooleRequest->get[$key] = $val;
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

                                    $swooleRequest->get[$key] = $val;
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
                        $response->error('Route (' . $route . ') is unable to identify!');
                        Be::gc();
                        return true;
                    }
                }

                $request->setRoute($app, $controller, $action);

                $class = 'Be\\App\\' . $app . '\\Controller\\' . ($admin ? 'Admin\\' : '') . $controller;
                if (!class_exists($class)) {
                    $response->set('code', 404);
                    $response->error('Controller ' . $app . '/' . $controller . ' does not exist!');
                } else {
                    $instance = new $class();
                    if (method_exists($instance, $action)) {
                        $instance->$action();
                    } else {
                        $response->set('code', 404);
                        $response->error('Undefined action ' . $action . ' of class ' . $class . '!');
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
            return true;
        });

        // 注册任务处理方法
        $this->swooleHttpServer->on('task', [\Be\Runtime\Task::class, 'onTask']);
        $this->swooleHttpServer->on('finish', function ($swooleHttpServer, $taskId, $data) {});

        // 定时任务进程
        $process = new \Swoole\Process([\Be\Runtime\Task::class, 'process'], false, 0, true);
        $this->swooleHttpServer->addProcess($process);

        $this->swooleHttpServer->start();
    }


    public function stop()
    {
        $this->swooleHttpServer->stop();
    }

    public function reload()
    {
        $this->swooleHttpServer->reload();
    }

    public function task($data)
    {
        $this->swooleHttpServer->task($data);
    }

    public function getSwooleHttpServer()
    {
        return $this->swooleHttpServer;
    }

    /**
     * 当前是否Worker进程
     *
     * @return bool
     */
    public function isWorkerProcess(): bool
    {
        return $this->swooleHttpServer->worker_id >= 0 && !$this->swooleHttpServer->taskworker;
    }

    /**
     * 当前是否Task进程
     *
     * @return bool
     */
    public function isTaskProcess(): bool
    {
        return $this->swooleHttpServer->worker_id >= 0 && $this->swooleHttpServer->taskworker;
    }

    /**
     * 当前是否用户自定义进程
     *
     * @return bool
     */
    public function isUserProcess(): bool
    {
        return $this->swooleHttpServer->worker_id  === -1;
    }

}
