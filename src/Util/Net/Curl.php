<?php

namespace Be\Util\Net;

use Be\Util\UtilException;

class Curl
{

    /**
     * GET请求
     *
     * @param string $url
     * @param array|null $headers
     * @param array|null $options
     * @return bool|string 返回的数据
     * @throws UtilException
     */
    static public function get(string $url, array $headers = null, array $options = null) :string
    {
        return self::request('GET', $url, null, $headers, $options);
    }

    /**
     * POST 请求
     *
     * @param string $url
     * @param $data
     * @param array|null $headers
     * @param array|null $options
     * @return bool|string
     * @throws UtilException
     */
    static public function post(string $url, $data, array $headers = null, array $options = null)
    {
        if (is_array($data)) {
            // 数据为数组时，走 x-www-form-urlencoded，如果需要 form-data，需调用 Curl::request 方法
            $data = http_build_query($data);
        }

        return self::request('POST', $url, $data, $headers, $options);
    }

    /**
     *
     * @param string $url
     * @param $data
     * @param array|null $headers
     * @param array|null $options
     * @return bool|string
     * @throws UtilException
     */
    static public function postJson(string $url, $data, array $headers = null, array $options = null)
    {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }

        if ($headers === null) {
            $headers = [];
        }
        $headers[] = 'Content-Type: application/json; charset=utf-8';
        return self::request('POST', $url, $data, $headers, $options);
    }

    /**
     * PUT请求
     *
     * @param string $url
     * @param $data
     * @param array|null $headers
     * @param array|null $options
     * @return bool|string
     * @throws UtilException
     */
    static public function put(string $url, $data, array $headers = null, array $options = null)
    {
        return self::request('PUT', $url, $data, $headers, $options);
    }

    /**
     * DELETE 请求
     *
     * @param string $url
     * @param $data
     * @param array|null $headers
     * @param array|null $options
     * @return bool|string
     * @throws UtilException
     */
    static public function delete(string $url, $data = null, array $headers = null, array $options = null)
    {
        return self::request('DELETE', $url, $data, $headers, $options);
    }

    /**
     * PATCH请求
     *
     * @param string $url
     * @param $data
     * @param array|null $headers
     * @param array|null $options
     * @return bool|string
     * @throws UtilException
     */
    static public function patch(string $url, $data = null, array $headers = null, array $options = null)
    {
        return self::request('PATCH', $url, $data, $headers, $options);
    }

    /**
     * 请求数据方法封装
     *
     * @param string $method
     * @param string $url
     * @param $data
     * @param array|null $headers
     * @param array|null $customOptions
     * @return bool|string
     * @throws UtilException
     */
    static public function request(string $method, string $url, $data = null, array $headers = null, array $customOptions = null)
    {
        $url = strtolower($url);

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_CONNECTTIMEOUT => 15, // 连接超时
            CURLOPT_TIMEOUT => 30, // 总超时
            CURLOPT_RETURNTRANSFER => true, // 不直接输出
            CURLOPT_HEADER => false, // 不返回头信息
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ];

        // 默认不处理证书
        if (substr($url, 0, 5) == 'https') {
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = false;
        }

        if ($customOptions !== null) {
            foreach ($customOptions as $key => $val) {
                $options[$key] = $val;
            }
        }

        if ($headers !== null) {
            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                if ($data === null) {
                    throw new UtilException('POST请求数据垲失！');
                }
                $options[CURLOPT_POST] = 1;
                $options[CURLOPT_POSTFIELDS] = $data;
                break;
            case 'PUT':
            case 'DELETE':
            case 'PATCH':
                if ($data === null) {
                    throw new UtilException($method . '请求数据垲失！');
                }
                $options[CURLOPT_CUSTOMREQUEST] = $method;
                $options[CURLOPT_POSTFIELDS] = $data;
                break;
        }

        $curl = curl_init();

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);

        if ($response === false) {
            throw new UtilException('[CURL_' . curl_errno($curl) . ']: ' . curl_error($curl));
        }

        curl_close($curl);

        return $response;
    }

}
