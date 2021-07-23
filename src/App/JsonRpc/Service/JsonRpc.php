<?php

namespace Be\App\JsonRpc\Service;


use Be\App\ServiceException;
use Be\Be;

/**
 * Class JsonRpc
 * @package Be\App\JsonRpc\Service
 *
 * 调用 JsonRpc 服务的客户端类封装
 */
class JsonRpc
{

    /**
     * 调用指定服务
     *
     * @param string $service
     * @return JsonRpcProxy
     */
    public function proxy($service)
    {
        return new JsonRpcProxy($service);
    }

    /**
     * 批量调用
     *
     * @param $calls
     * @return array
     * @throws ServiceException
     * @throws \Throwable
     */
    public function bulk($calls = [])
    {
        $data = [];
        foreach ($calls as &$call) {
            if (!isset($call['service'])) {
                throw new ServiceException('Parameter (service) missed!');
            }

            if (!isset($call['method'])) {
                throw new ServiceException('Parameter (method) missed!');
            }

            if (!isset($call['params'])) {
                $call['params'] = [];
            }

            $data[] = [
                'jsonrpc' => '2.0',
                'method' => $call['service'] . '::' . $call['method'],
                'params' => $call['params'],
                'id' => uniqid(rand(10000, 99999), true)
            ];
        }


        $handler = curl_init();
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handler, CURLOPT_USERAGENT, '');
        curl_setopt($handler, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($handler, CURLOPT_TIMEOUT, 15);
        curl_setopt($handler, CURLOPT_MAXREDIRS, 3);
        curl_setopt($handler, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($handler, CURLOPT_HEADER, false);

        $header = array();
        $header[] = 'Content-Type: application/json; charset=utf-8';
        curl_setopt($handler, CURLOPT_HTTPHEADER, $header);

        curl_setopt($handler, CURLOPT_URL, Be::getConfig('JsonRpc.Test')->url);
        curl_setopt($handler, CURLOPT_POST, true);
        curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($handler);
        curl_close($handler);

        //file_put_contents(Be::getRuntime()->getRootPath().'/JsonRpc', json_encode($data) . "\r\n", FILE_APPEND);
        //file_put_contents(Be::getRuntime()->getRootPath().'/JsonRpc', $response . "\r\n\r\n", FILE_APPEND);

        if (!$response) {
            throw new ServiceException('RPC service (' . $call['service'] . '::' . $call['method'] . ') error: no body data returned!');
        }

        $decodedResponse = json_decode($response, true);
        if ($decodedResponse === NULL) {
            throw new ServiceException('Call RPC service (' . $call['service'] . '::' . $call['method'] . ') error: body data is not a valid JSON!');
        }

        if (count($decodedResponse) != count($data)) {
            throw new ServiceException('Call RPC service (' . $call['service'] . '::' . $call['method'] . ') error: response data items does not match request!');
        }

        $results = [];
        foreach ($decodedResponse as $decodedRes) {

            if (isset($decodedRes['error'])) {

                $msg = 'Call RPC service (' . $call['service'] . '::' . $call['method'] . ') error'; //'调用RPC服务出错';

                /*
                if (isset($decodedRes['error']['code'])) {
                    $msg .= ' #'.$decodedRes['error']['code'];
                }
                */

                if (isset($decodedRes['error']['message'])) {
                    $msg .= ': ' . $decodedRes['error']['message'];
                }

                $msg .= '!';

                throw new ServiceException($msg);
            }

            if (!isset($decodedRes['result'])) {
                throw new ServiceException('Call RPC service (' . $call['service'] . '::' . $call['method'] . ') error: no result part in response data!');
            }

            $results[] = $decodedRes['result'];
        }

        return $results;

    }


}
