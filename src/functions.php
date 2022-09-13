<?php

/**
 * 处理网址
 * 启用 SEF 时生成伪静态页， 为空时返回网站网址
 *
 * @param null | string $route 路径（应用名.控制器名.动作名）
 * @param null | array $params
 * @return string 生成的网址
 */
function beUrl($route = null, array $params = null)
{
    $runtime = \Be\Be::getRuntime();
    $configSystem = \Be\Be::getConfig('App.System.System');
    if ($configSystem->rootUrl === '') {
        if (!$runtime->isSwooleMode() || $runtime->isWorkerProcess()) {
            $rootUrl = \Be\Be::getRequest()->getRootUrl();
        } else {
            $rootUrl = '';
        }
    } else {
        $rootUrl = $configSystem->rootUrl;
    }

    if ($route === null) {
        if ($params !== null) {
            if (!$runtime->isSwooleMode() || $runtime->isWorkerProcess()) {
                $route = \Be\Be::getRequest()->getRoute();
            } else {
                $route = $configSystem->home;
            }
        } else {
            return $rootUrl;
        }
    }

    if ($configSystem->urlRewrite === '1') {
        $url = $rootUrl . '/' . str_replace('.', '/', $route);
        if ($params !== null && $params) {
            $urlParams = '';
            foreach ($params as $key => $val) {
                $urlParams .= '/' . $key . '-' . $val;
            }
            $url .= $urlParams;
        }
        $url .= $configSystem->urlSuffix;
        return $url;
    } elseif ($configSystem->urlRewrite === '2') {
        return \Be\Router\Helper::encode($route, $params);
    } else {
        $url = $rootUrl . '/?route=' . $route;
        if ($params !== null && $params) {
            $url .= '&' . http_build_query($params);
        }
        return $url;
    }
}

/**
 * 处理网址
 * 启用 SEF 时生成伪静态页， 为空时返回网站网址
 *
 * @param null | string $route 路径（应用名.控制器名.动作名）
 * @param null | array $params
 * @return string 生成的网址
 */
function beAdminUrl($route = null, array $params = null)
{
    $runtime = \Be\Be::getRuntime();
    $configSystem = \Be\Be::getConfig('App.System.System');
    if ($configSystem->rootUrl === '') {
        if (!$runtime->isSwooleMode() || $runtime->isWorkerProcess()) {
            $rootUrl = \Be\Be::getRequest()->getRootUrl();
        } else {
            $rootUrl = '';
        }
    } else {
        $rootUrl = $configSystem->rootUrl;
    }

    $adminAlias = \Be\Be::getRuntime()->getAdminAlias();
    if ($route === null) {
        if ($params !== null) {
            if (!$runtime->isSwooleMode() || $runtime->isWorkerProcess()) {
                $route = \Be\Be::getRequest()->getRoute();
            } else {
                $route = Be\Be::getConfig('App.System.Admin')->home;
            }
        } else {
            if ($configSystem->urlRewrite) {
                return $rootUrl . '/' . $adminAlias;
            } else {
                return $rootUrl . '/?' . $adminAlias . '=1';
            }
        }
    }

    if ($configSystem->urlRewrite) {
        $url = $rootUrl . '/' . $adminAlias . '/' . str_replace('.', '/', $route);
        if ($params !== null && $params) {
            $urlParams = '';
            foreach ($params as $key => $val) {
                $urlParams .= '/' . $key . '-' . $val;
            }
            $url .= $urlParams;
        }
        if ($configSystem->urlRewrite === '1') {
            $url .= $configSystem->urlSuffix;
        }
        return $url;
    } else {
        $url = $rootUrl . '/?' . $adminAlias . '&route=' . $route;
        if ($params !== null && $params) {
            $url .= '&' . http_build_query($params);
        }
        return $url;
    }
}

/**
 * 系统后台操作日志
 *
 * @param string $content 日志内容
 * @param mixed $details 日志明细
 * @throws \Exception
 */
function beAdminOpLog($content, $details = '')
{
    \Be\Be::getService('App.System.Admin.AdminOpLog')->addLog($content, $details);
}

/**
 * 语言
 *
 * @param string $package 语言包名
 * @param string $text 文字
 * @return string
 */
function beLang(string $package, string $text, string ...$args): string
{
    return \Be\Be::getLanguage($package)->translate($text, ...$args);
}