<?php

namespace Be\App\JsonRpc\Service;

use Be\App\ServiceException;
use Be\Be;

/**
 * Class JsonRpcProxy
 * @package Be\App\JsonRpc\Service
 *
 * 调用 JsonRpc 服务的客户端类封装
 */
class JsonRpcProxy
{
    private $service = null;

    public function __construct($service)
    {
        $this->service = $service;
    }

    /**
     * 魔术方法调用
     *
     * @ignore
     * @param string $function
     * @param array $arguments
     * @return bool|mixed
     * @throws \Exception
     */
    public function __call($function, $arguments)
    {
        $method = $this->service . '::' . $function;

        $data = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $arguments,
            'id' => uniqid(rand(10000, 99999), true)
        ];

        $handler = curl_init();
        curl_setopt($handler,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($handler,CURLOPT_USERAGENT, '');
        curl_setopt($handler,CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($handler,CURLOPT_TIMEOUT,15);
        curl_setopt($handler,CURLOPT_MAXREDIRS,3);
        curl_setopt($handler,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_0);
        curl_setopt($handler,CURLOPT_HEADER,false);

        $header = array();
        $header[] = 'Content-Type: application/json; charset=utf-8';
        curl_setopt($handler, CURLOPT_HTTPHEADER, $header);

        curl_setopt($handler, CURLOPT_URL, Be::getConfig('JsonRpc.Test')->url);
        curl_setopt($handler,CURLOPT_POST,true);
        curl_setopt($handler,CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($handler);
        curl_close($handler);

        //file_put_contents(Be::getRuntime()->getRootPath().'/JsonRpcProxy', json_encode($data) . "\r\n", FILE_APPEND);
        //file_put_contents(Be::getRuntime()->getRootPath().'/JsonRpcProxy', $response . "\r\n\r\n", FILE_APPEND);

        if (!$response) {
            throw new ServiceException('Call RPC service (' . $method . ') error:  no body data returned!');
        }

        $decodedResponse = json_decode($response, true);
        if ($decodedResponse === NULL) {
            throw new ServiceException('Call RPC service (' . $method . ') error: body data is not a valid JSON!');
        }

        if (isset($decodedResponse['error'])) {

            $msg = 'Error'; //'调用RPC服务（' . $method . '）出错';

            /*
            if (isset($decodedResponse['error']['code'])) {
                $msg .= ' #'.$decodedResponse['error']['code'];
            }
            */

            if (isset($decodedResponse['error']['message'])) {
                $msg .= ': '.$decodedResponse['error']['message'];
            }

            $msg .= '!';

            throw new ServiceException($msg);
        }

        if (!isset($decodedResponse['result'])) {
            throw new ServiceException('Call RPC service (' . $method . ') error: no result part in response data!');
        }

        return $decodedResponse['result'];
    }

}