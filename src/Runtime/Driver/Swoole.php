<?php

namespace Be\Runtime\Driver;

use Be\Be;
use Be\Runtime\Driver;

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

    const MIME = [
        'html' => 'text/html',
        'htm' => 'text/html',
        'xhtml' => 'application/xhtml+xml',
        'xml' => 'text/xml',
        'txt' => 'text/plain',
        'log' => 'text/plain',

        'js' => 'application/javascript',
        'json' => 'application/json',
        'css' => 'text/css',

        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png',
        'bmp' => 'image/bmp',
        'ico' => 'image/icon',
        'svg' => 'image/svg+xml',

        'mp3' => 'audio/mpeg',
        'wav' => 'audio/wav',

        'mp4' => 'video/avi',
        'avi' => 'video/avi',
        '3gp' => 'application/octet-stream',
        'flv' => 'application/octet-stream',
        'swf' => 'application/x-shockwave-flash',

        'zip' => 'application/zip',
        'rar' => 'application/octet-stream',

        'ttf' => 'application/octet-stream',
        'otf' => 'application/octet-stream',
        'eot' => 'application/octet-stream',
        'fon' => 'application/octet-stream',
        'woff' => 'application/octet-stream',
        'woff2' => 'application/octet-stream',

        'doc' => 'application/msword',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'mdb' => 'application/msaccess',
        'chm' => 'application/octet-stream',

        'pdf' => 'application/pdf',
    ];


    public function execute()
    {

        if ($this->swooleHttpServer !== null) {
            return;
        }

        $state = new \Swoole\Table(1024);
        $state->column('value', \Swoole\Table::TYPE_INT, 64);
        $state->create();
        $state->set('task', ['value' => 1]);

        \Co::set(['hook_flags' => SWOOLE_HOOK_ALL]);

        $configSystem = Be::getConfig('App.System.System');
        date_default_timezone_set($configSystem->timezone);

        $configServer = Be::getConfig('App.System.Server');
        $this->swooleHttpServer = new \Swoole\Http\Server($configServer->host, $configServer->port);
        $this->swooleHttpServer->state = $state;

        $setting = [
            'enable_coroutine' => true,
            'task_worker_num' => 4,
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

        if ($configServer->clearCacheOnStart) {
            $dir = Be::getRuntime()->getCachePath();
            \Be\Util\File\Dir::rm($dir);
        } else {
            $sessionConfig = Be::getConfig('App.System.Session');
            if ($sessionConfig->driver === 'File') {
                $dir = Be::getRuntime()->getCachePath() . '/session';
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
                if (isset(self::MIME[$ext])) {
                    $rootPath = Be::getRuntime()->getRootPath();
                    if (file_exists($rootPath . $uri)) {
                        $swooleResponse->header('Content-Type', self::MIME[$ext], false);
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

                    if ($uri === '/favicon.ico') {
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

                $admin = $request->request($this->adminAlias, false);
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
                        $decodedRoute = \Be\Router\Helper::decode($uri);
                        if ($decodedRoute) {
                            $routes = explode('.', $decodedRoute[0]);
                            $app = $routes[0] ?? '';
                            $controller = $routes[1] ?? '';
                            $action = $routes[2] ?? '';

                            if (isset($decodedRoute[1])) {
                                foreach ($decodedRoute[1] as $key => $val) {
                                    $_GET[$key] = $_REQUEST[$key] = $val;
                                }
                            }

                            $routeParsed = true;
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

                                    $swooleRequest->get[$key] = $swooleRequest->request[$key] = $val;
                                }
                            }
                        }
                    }
                }

                if ($admin) $request->setAdmin($admin);

                // 默认访问控制台页面
                if (!$app) {
                    if ($admin) {
                        $route = $request->request('route', Be::getConfig('App.System.Admin')->home);
                    } else {
                        $route = $request->request('route', $configSystem->home);
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
                        $logId = Be::getLog()->emergency($t);
                        $response->set('logId', $logId);
                        $response->set('code', $t->getCode());
                    }
                    $response->error($t->getMessage(), $t->getRedirect());
                } else {
                    $logId = Be::getLog()->emergency($t);
                    $response->set('logId', $logId);
                    $response->exception($t);
                }
            }

            Be::gc();
            return true;
        });

        // 注册任务处理方法
        $this->swooleHttpServer->on('task', [\Be\Runtime\Task::class, 'onTask']);
        $this->swooleHttpServer->on('finish', function ($swooleHttpServer, $taskId, $data) {
        });

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

}
