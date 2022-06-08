<?php

namespace Be\Log;

use Be\Be;
use Be\Runtime\RuntimeException;
use Be\Util\File\FileSize;

/**
 * 日志类
 *
 * @method static bool debug(\Throwable $t)
 * @method static bool info(\Throwable $t)
 * @method static bool notice(\Throwable $t)
 * @method static bool warning(\Throwable $t)
 * @method static bool error(\Throwable $t)
 * @method static bool critical(\Throwable $t)
 * @method static bool alert(\Throwable $t)
 * @method static bool emergency(\Throwable $t)
 */
abstract class Driver
{

    /**
     * 获取级别编码
     *
     * @param $level
     * @return int
     */
    protected function getLevelCode($level): int
    {
        switch ($level) {
            case 'debug': return 100;
            case 'info': return 200;
            case 'notice': return 300;
            case 'warning': return 400;
            case 'error': return 500;
            case 'critical': return 600;
            case 'alert': return 700;
            case 'emergency': return 800;
        }
        return 0;
    }

    /**
     *
     * @param $name
     * @param $arguments
     * @return string
     * @throws RuntimeException
     */
    public function __call($name, $arguments)
    {
        $configSystemLog = Be::getConfig('App.System.Log');

        $configLevelCode = $this->getLevelCode($configSystemLog->level);
        $levelCode = $this->getLevelCode($name);

        if ($levelCode < $configLevelCode) {
            return '';
        }

        /**
         * @var \Throwable $t
         */
        $t = $arguments[0];

        $content = [
            'level' => strtoupper($name),
            'create_time' => date('Y-m-d H:i:s'),
        ];

        if ($t instanceof \Throwable) {
            $content['message'] = $t->getMessage();
            $content['file'] = $t->getFile();
            $content['line'] = $t->getLine();
            $content['code'] = $t->getCode();
            $content['trace'] = $t->getTrace();
        } else {
            if (is_object($t)) {
                $content = get_object_vars($t);
            }

            if (is_array($t)) {
                $content = $t;
            }

            if (!isset($content['message'])) {
                $content['message'] = is_string($t) ? $t : '';
            }

            if (!isset($content['file'])) {
                $content['file'] = '';
            }

            if (!isset($content['line'])) {
                $content['line'] = '';
            }
        }

        if (!isset($content['id'])) {
            $content['id'] = md5(json_encode([
                'file' => $content['file'],
                'line' => $content['line'],
                'message' => $content['message']
            ]));
        }

        $request = Be::getRequest();
        if ($request) {
            if (isset($configSystemLog->get) && $configSystemLog->get) {
                $content['get'] = $request->get();
            }

            if (isset($configSystemLog->post) && $configSystemLog->post) {
                $content['post'] = $request->post();
            }

            if (isset($configSystemLog->request) && $configSystemLog->request) {
                $content['request'] = $request->request();
            }

            if (isset($configSystemLog->cookie) && $configSystemLog->cookie) {
                $content['cookie'] = $request->cookie();
            }

            if (isset($configSystemLog->session) && $configSystemLog->session) {
                $session = Be::getSession();
                $content['session'] = $session->get();
            }

            if (isset($configSystemLog->header) && $configSystemLog->header) {
                $content['header'] = $request->header();
            }

            if (isset($configSystemLog->server) && $configSystemLog->server) {
                $content['server'] = $request->server();
            }

            if (isset($configSystemLog->memery) && $configSystemLog->memery) {
                $bytes = memory_get_usage();
                $content['memory_usage'] = FileSize::int2String($bytes);

                $bytes = memory_get_peak_usage();
                $content['memory_peak_usage'] = FileSize::int2String($bytes);
            }
        }

        $this->write($content);

        return $content['id'];
    }

    abstract protected function write(array $content);


}
