<?php

namespace Be\Redis;

use Be\Be;
use Be\Config\Annotation\BeConfigItem;
use Be\Util\Annotation;


/**
 * Redis 帮助类
 */
abstract class RedisHelper
{

    public static function getConfigKeyValues()
    {
        $keyValues = [];
        $className = '\\Be\\App\\System\\Config\\Redis';
        if (class_exists($className)) {
            $originalConfigInstance = new $className();
            $reflection = new \ReflectionClass($className);
            $properties = $reflection->getProperties(\ReflectionMethod::IS_PUBLIC);
            foreach ($properties as $property) {
                $itemName = $property->getName();
                $itemComment = $property->getDocComment();
                $parseItemComments = Annotation::parse($itemComment);
                if (isset($parseItemComments['BeConfigItem'][0])) {
                    $annotation = new BeConfigItem($parseItemComments['BeConfigItem'][0]);
                    $configItem = $annotation->toArray();
                    if (isset($configItem['value'])) {
                        $keyValues[$itemName] = $configItem['value'];
                    }
                } else {
                    $fn = '_' . $itemName;
                    if (is_callable([$originalConfigInstance, $fn])) {
                        $configItem = $originalConfigInstance->$fn($itemName);
                        if (isset($configItem['label'])) {
                            $keyValues[$itemName] = $configItem['label'];
                        }
                    }
                }
            }
        }

        $config = Be::getConfig('App.System.redis');
        $arrConfig = get_object_vars($config);
        foreach ($arrConfig as $k => $v) {
            if (!isset($keyValues[$k])) {
                $keyValues[$k] = $k;
            }
        }

        return $keyValues;
    }


}
