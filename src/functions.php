<?php


/**
 * 处理网址
 * 启用 SEF 时生成伪静态页， 为空时返回网站网址
 *
 * @param null | string $route 路径（应用名.控制器名.动作名）
 * @param null | array $params
 * @return string 生成的网址
 */
function beUrl($route = null, $params = [])
{
    $request = \Be\Be::getRequest();
    if ($route === null) {
        if (count($params) > 0) {
            $route = $request->getRoute();
        } else {
            return $request->getRootUrl();
        }
    }

    $configSystem = \Be\Be::getConfig('App.System.System');
    if ($configSystem->urlRewrite) {
        $urlParams = '';
        if (count($params)) {
            foreach ($params as $key => $val) {
                $urlParams .= '/' . $key . '-' . $val;
            }
        }
        return $request->getRootUrl() . '/' . str_replace('.', '/', $route) . $urlParams . $configSystem->urlSuffix;
    } else {
        return $request->getRootUrl() . '/?route=' . $route . (count($params) > 0 ? '&' . http_build_query($params) : '');
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
function beAdminUrl($route = null, $params = [])
{
    $request = \Be\Be::getRequest();
    $adminAlias = \Be\Be::getRuntime()->getAdminAlias();
    if ($route === null) {
        if (count($params) > 0) {
            $route = $request->getRoute();
        } else {
            return $request->getRootUrl() . '/' . $adminAlias;
        }
    }

    $configSystem = \Be\Be::getConfig('App.System.System');
    if ($configSystem->urlRewrite) {
        $urlParams = '';
        if (count($params)) {
            foreach ($params as $key => $val) {
                $urlParams .= '/' . $key . '-' . $val;
            }
        }
        return $request->getRootUrl() . '/' . $adminAlias . '/' . str_replace('.', '/', $route) . $urlParams . $configSystem->urlSuffix;
    } else {
        return $request->getRootUrl() . '/?admin=1&route=' . $route . (count($params) > 0 ? '&' . http_build_query($params) : '');
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
    \Be\Be::getAdminService('System.AdminOpLog')->addLog($content, $details);
}
