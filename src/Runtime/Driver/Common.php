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
        $request = new \Be\Request\Driver\Common();
        $response = new \Be\Response\Driver\Common();
        Be::setRequest($request);
        Be::setResponse($response);

        // 启动 session
        $session = Be::getSession();
        $session->start();

        try {
            // 检查网站配置， 是否暂停服务
            $configSystem = Be::getConfig('System.System');

            // 默认时区
            date_default_timezone_set($configSystem->timezone);

            $admin = null;
            $app = null;
            $controller = null;
            $action = null;

            // 从网址中提取出 路径
            if ($configSystem->urlRewrite) {

                //print_r($_SERVER);

                /*
                 * REQUEST_URI 可能值为：[/path]/{action}[/{k-v}].html?[k=v]
                 * 需要解析的有效部分为： {action}[/{k-v}]
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
                if ($_SERVER['QUERY_STRING'] != ''){
                    $uri = substr($uri, 0, strrpos($uri, '?'));
                }

                // 移除 .html
                $lenSefSuffix = strlen($configSystem->urlSuffix);
                if (substr($uri, -$lenSefSuffix, $lenSefSuffix) == $configSystem->urlSuffix) {
                    $uri = substr($uri, 0, strrpos($uri, $configSystem->urlSuffix));
                }

                // 移除结尾的 /
                if (substr($uri, -1, 1) == '/') $uri = substr($uri, 0, -1);

                // 是否后台功能
                if (substr($uri, 0, strlen($this->adminAlias) + 1) == $this->adminAlias . '/') {
                    $admin = true;
                    $uri = substr($uri, strlen($this->adminAlias) + 1);
                }

                // /{action}[/{k-v}]
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
                     * /{action}/{参数名1}-{参数值1}/{参数名2}-{参数值2}/{参数名3}-{参数值3}
                     * 其中{参数名}-{参数值} 值对不限数量
                     */
                    for ($i = 4; $i < $len; $i++) {
                        $pos = strpos($uris[$i], '-');
                        if ($pos !== false) {
                            $key = substr($uris[$i], 0, $pos);
                            $val = substr($uris[$i], $pos + 1);

                            $_GET[$key] = $_REQUEST[$key] = $val;
                        }
                    }
                }
            }

            if ($admin === null) $admin = $request->request('admin', false);
            if ($admin) $request->setAdmin($admin);

            // 默认访问控制台页面
            if (!$app) {
                if ($admin) {
                    $route = $request->request('route', Be::getConfig('System.Admin')->home);
                } else {
                    $route = $request->request('route', $configSystem->home);
                }
                $routes = explode('.', $route);
                if (count($routes) == 3) {
                    $app = $routes[0];
                    $controller = $routes[1];
                    $action = $routes[2];
                } else {
                    $response->set('code', 404);
                    $response->error('路由参数（' . $route . '）无法识别！');
                    Be::gc();
                    return;
                }
            }

            $request->setRoute($app, $controller, $action);

            if ($admin) {
                // 校验权限
                $adminRole0 = Be::getAdminRole(0);
                if (!$adminRole0->hasPermission($app, $controller, $action)) {
                    $my = Be::getAdminUser();
                    if ($my->id == 0) {
                        Be::getAdminService('System.AdminUser')->rememberMe();
                        $my = Be::getAdminUser();
                    }

                    // 访问的不是公共内容，且未登录，跳转到登录页面
                    if ($my->id == 0) {
                        $return = $request->get('return', base64_encode($request->getUrl()));
                        $redirectUrl = beAdminUrl('System.AdminUser.login', ['return' => $return]);
                        $response->error('登录超时，请重新登录！', $redirectUrl);
                        Be::gc();
                        return;
                    } else {
                        if (!$my->hasPermission($app, $controller, $action)) {
                            $response->error('您没有权限操作该功能！');
                            Be::gc();
                            return;
                        }

                        // 已登录用户，IP锁定功能校验
                        $configAdminUser = Be::getConfig('System.AdminUser');
                        if ($configAdminUser->ipLock) {
                            if ($my->this_login_ip != $request->getIp()) {
                                Be::getAdminService('System.AdminUser')->logout();
                                $redirectUrl = beAdminUrl('System.AdminUser.login');
                                $response->error('检测到您的账号在其它地点（' . $my->this_login_ip . ' ' . $my->this_login_time . '）登录！', $redirectUrl);
                                Be::gc();
                                return;
                            }
                        }
                    }
                }

                $class = 'Be\\App\\' . $app . '\\AdminController\\' . $controller;
                if (!class_exists($class)) {
                    $response->set('code', 404);
                    $response->error('控制器 ' . $app . '/' . $controller . ' 不存在！');
                } else {
                    $instance = new $class();
                    if (method_exists($instance, $action)) {
                        $instance->$action();
                    } else {
                        $response->set('code', 404);
                        $response->error('方法 ' . $action . ' 在控制器 ' . $class . ' 中不存在！');
                    }
                }
            } else {
                $class = 'Be\\App\\' . $app . '\\Controller\\' . $controller;
                if (!class_exists($class)) {
                    $response->set('code', 404);
                    $response->error('Controller ' . $app . '/' . $controller . ' doesn\'t exist!');
                } else {
                    $instance = new $class();
                    if (method_exists($instance, $action)) {
                        $instance->$action();
                    } else {
                        $response->set('code', 404);
                        $response->error('Undefined action ' . $action . ' of class ' . $class . '!');
                    }
                }
            }

        } catch (\Throwable $t) {
            $logId = Be::getLog()->emergency($t);
            $response->set('logId', $logId);
            $response->exception($t);
        }

        Be::gc();
    }
}
