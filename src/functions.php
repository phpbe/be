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
    $request = \Be\Be::getRequest();
    if ($route === null) {
        if ($params !== null) {
            $route = $request->getRoute();
        } else {
            return $request->getRootUrl();
        }
    }

    $configSystem = \Be\Be::getConfig('App.System.System');
    if ($configSystem->urlRewrite === '1') {
        $url = $request->getRootUrl() . '/' . str_replace('.', '/', $route);
        if ($params !== null && $params) {
            $urlParams = '';
            foreach ($params as $key => $val) {
                $urlParams .= '/' . $key . '-' . $val;
            }
            $url .= $urlParams;
        }
        $url .=  $configSystem->urlSuffix;
        return $url;
    } elseif ($configSystem->urlRewrite === '2') {
        return \Be\Router\Helper::encode($route, $params);
    } else {
        $url = $request->getRootUrl() . '/?route=' . $route;
        if ($params !== null && $params) {
            $url .=  '&' . http_build_query($params);
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
    $request = \Be\Be::getRequest();
    $adminAlias = \Be\Be::getRuntime()->getAdminAlias();
    $configSystem = \Be\Be::getConfig('App.System.System');
    if ($route === null) {
        if ($params !== null) {
            $route = $request->getRoute();
        } else {
            if ($configSystem->urlRewrite) {
                return $request->getRootUrl() . '/' . $adminAlias;
            } else {
                return $request->getRootUrl() . '/?' . $adminAlias . '=1';
            }
        }
    }

    if ($configSystem->urlRewrite) {
        $url = $request->getRootUrl() . '/' . $adminAlias . '/' . str_replace('.', '/', $route);
        if ($params !== null && $params) {
            $urlParams = '';
            foreach ($params as $key => $val) {
                $urlParams .= '/' . $key . '-' . $val;
            }
            $url .= $urlParams;
        }
        if ($configSystem->urlRewrite === '1') {
            $url .=  $configSystem->urlSuffix;
        }
        return $url;
    } else {
        $url = $request->getRootUrl() . '/?' . $adminAlias . '=1&route=' . $route;
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
