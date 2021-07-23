<?php

namespace Be\App\JsonRpc\Service;

/**
 * JsonRpc 测试服务
 *
 * Class Test
 * @package Be\App\JsonRpc\Service
 */
class Test
{
    /**
     * 加法
     *
     * @param int $a
     * @param int $b
     * @return int
     */
    public function sum(int $a, int $b): int
    {
        return $a + $b;
    }

}
