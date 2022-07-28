<?php

namespace Be\App\JsonRpc\Controller;

use Be\App\ControllerException;
use Be\Be;

class Index
{

    const ERR_PARSE = -32700;
    const ERR_REQUEST = -32600;
    const ERR_METHOD = -32601;
    const ERR_PARAMS = -32602;
    const ERR_INTERNAL = -32603;
    const ERR_SERVER = -32000;

    private $configLog = null;

    public function __construct()
    {
        $configJsonRpc = Be::getConfig('App.JsonRpc.JsonRpc');
        if (!$configJsonRpc->enable) {
            throw new ControllerException('JsonRpc 未启用');
        }

        $this->configLog = Be::getConfig('App.JsonRpc.Log');
    }

    public function index()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if (Be::getRuntime()->isSwooleMode()) {
            $inputDataStr = $request->getRequest()->getContent();
        } else {
            $inputDataStr = file_get_contents('php://input');
        }
        $inputData = json_decode($inputDataStr);
        if (!$inputData) {
            $response->end(json_encode($this->error(null, static::ERR_PARSE)));
            return;
        }

        if (is_array($inputData)) {
            $results = [];
            $hasError = false;
            foreach ($inputData as $x) {
                $result = $this->handle($x);
                if ($result) {
                    $results[] = $result;
                    if (isset($result['error'])) {
                        $hasError = true;
                    }
                }
            }

            $resultsStr = json_encode($results);
            if ($this->configLog->accessLog) {
                Be::getService('App.JsonRpc.Log')->accessLog($inputDataStr, $resultsStr);
            }

            if ($this->configLog->errorLog && $hasError) {
                Be::getService('App.JsonRpc.Log')->errorLog($inputDataStr, $resultsStr);
            }
            $response->end($resultsStr);
        } else {
            $result = $this->handle($inputData);
            $resultStr = json_encode($result);
            if ($this->configLog->accessLog) {
                Be::getService('App.JsonRpc.Log')->accessLog($inputDataStr, $resultStr);
            }

            if ($this->configLog->errorLog && isset($result['error'])) {
                Be::getService('App.JsonRpc.Log')->errorLog($inputDataStr, $resultStr);
            }
            $response->end($resultStr);
        }
    }

    private function handle($inputData)
    {
        $inputArray = $this->obj2Arr($inputData);
        $id = false;
        if (isset($inputArray['id'])) {
            $id = $inputArray['id'];
        }

        if (!isset($inputArray['method'])) {
            return $this->error($id, static::ERR_METHOD);
        }

        if (!isset($inputArray['params'])) {
            $inputArray['params'] = [];
        }

        $method = $inputArray['method'];
        $methods = explode('::', $method);
        if (count($methods) !== 2) {
            return $this->error($id, static::ERR_METHOD);
        }

        try {
            $serviceName = $methods[0];
            $function = $methods[1];
            $service = Be::getService($serviceName);
            $result = $service->$function(...$inputArray['params']);
            if ($result === null) $result = false;
            return $this->success($id, $result);
        } catch (\Throwable $t) {
            Be::getLog()->fatal($t);
            return $this->error($id, $t->getCode(), $t->getMessage());
        }
    }


    private function success($id, $result)
    {
        if ($id === false) return false;

        return [
            'jsonrpc' => '2.0',
            'result' => $result,
            'id' => $id
        ];
    }

    private function error($id, $code, $message = null)
    {
        if ($id === false) return false;

        if (!$message) {
            switch ($code) {
                case static::ERR_PARSE:
                    $message = 'Parse error'; // 语法解析错误
                    break;
                case static::ERR_REQUEST:
                    $message = 'Invalid Request';  // 无效请求
                    break;
                case static::ERR_METHOD:
                    $message = 'Method not found'; // 找不到方法
                    break;
                case static::ERR_PARAMS:
                    $message = 'Invalid params'; // 无效的参数
                    break;
                case static::ERR_INTERNAL:
                    $message = 'Internal error'; // 内部错误
                    break;
                case static::ERR_SERVER:
                    $message = 'Server error'; // 服务端错误
                    break;
                default:
                    $message = 'Unknown error'; // 未知错误
                    break;
            }
        }

        return [
            'jsonrpc' => '2.0',
            'error' => [
                'code' => $code,
                'message' => $message
            ],
            'id' => $id
        ];
    }

    private function obj2Arr($obj)
    {
        $arr = (array)$obj;
        foreach ($arr as $k => $v) {
            if (gettype($v) === 'object' || gettype($v) === 'array') {
                $arr[$k] = (array)$this->obj2Arr($v);
            }
        }

        return $arr;
    }


}
