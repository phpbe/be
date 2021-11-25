<?php

namespace Be\Router;

use Be\Be;


/**
 * Class Driver
 * @package Be\Route
 */
class Driver
{

    public function encode($route, $params = []) {
        $urlParams = '';
        if (count($params)) {
            foreach ($params as $key => $val) {
                $urlParams .= '/' . $key . '-' . $val;
            }
        }

        $configSystem = \Be\Be::getConfig('App.System.System');
        return Be::getRequest()->getRootUrl() . '/' . str_replace('.', '/', $route) . $urlParams . $configSystem->urlSuffix;
    }


    public function decode($uri) {



    }

}
