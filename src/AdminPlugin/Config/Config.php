<?php

namespace Be\AdminPlugin\Config;

use Be\Config\Annotation\BeConfig;
use Be\Config\Annotation\BeConfigItem;
use Be\Be;
use Be\AdminPlugin\Driver;
use Be\AdminPlugin\AdminPluginException;

/**
 * 配置
 *
 * Class Config
 * @package Be\AdminPlugin\Config
 */
class Config extends Driver
{

    public function display()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $appName = isset($this->setting['appName']) ? $this->setting['appName'] : $request->getAppName();
        $appProperty = Be::getProperty('App.' . $appName);

        $response->set('title', $this->setting['title'] ?? ($appProperty->getLabel() . '配置'));

        $configs = [];
        $dir = Be::getRuntime()->getRootPath() . Be::getProperty('App.' . $appName)->getPath() . '/Config';
        if (file_exists($dir) && is_dir($dir)) {
            $fileNames = scandir($dir);
            foreach ($fileNames as $fileName) {
                if ($fileName != '.' && $fileName != '..' && is_file($dir . '/' . $fileName)) {
                    $configName = substr($fileName, 0, -4);
                    $className = '\\Be\\App\\' . $appName . '\\Config\\' . $configName;
                    if (class_exists($className)) {
                        $reflection = new \ReflectionClass($className);
                        $classComment = $reflection->getDocComment();
                        $parseClassComments = \Be\Util\Annotation::parse($classComment);
                        if (isset($parseClassComments['BeConfig'][0])) {
                            $annotation = new BeConfig($parseClassComments['BeConfig'][0]);
                            $config = $annotation->toArray();
                            if (isset($config['value'])) {
                                $config['label'] = $config['value'];
                                unset($config['value']);
                            }
                            $config['name'] = $configName;
                            $config['url'] = beAdminUrl($request->getRoute(), ['configName' => $configName]);
                            $configs[] = $config;
                        }
                    }
                }
            }
        }
        $response->set('configs', $configs);

        $configName = $request->get('configName', '');
        if (!$configName) {
            $configName = $configs[0]['name'];
        }
        $response->set('configName', $configName);

        $configItemDrivers = [];
        $className = '\\Be\\App\\' . $appName . '\\Config\\' . $configName;
        if (class_exists($className)) {
            $configInstance = Be::getConfig('App.' . $appName . '.' . $configName);
            $originalConfigInstance = new $className();

            $reflection = new \ReflectionClass($className);
            $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
            foreach ($properties as $property) {
                $itemName = $property->getName();
                $itemComment = $property->getDocComment();
                $parseItemComments = \Be\Util\Annotation::parse($itemComment);

                $configItem = null;
                if (isset($parseItemComments['BeConfigItem'][0])) {
                    $annotation = new BeConfigItem($parseItemComments['BeConfigItem'][0]);
                    $configItem = $annotation->toArray();
                    if (isset($configItem['value'])) {
                        $configItem['label'] = $configItem['value'];
                        unset($configItem['value']);
                    }
                } else {
                    $fn = '_' . $itemName;
                    if (is_callable([$originalConfigInstance, $fn])) {
                        $configItem = $originalConfigInstance->$fn($itemName);
                    }
                }

                if ($configItem) {

                    $configItem['name'] = $itemName;
                    $configItem['value'] = $configInstance->$itemName ?? $originalConfigInstance->$itemName;

                    $driverClass = null;
                    if (isset($configItem['driver'])) {
                        if (substr($configItem['driver'], 0, 8) == 'FormItem') {
                            $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $configItem['driver'];
                        } else {
                            $driverClass = $configItem['driver'];
                        }
                    } else {
                        $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
                    }
                    $driver = new $driverClass($configItem);

                    $configItemDrivers[] = $driver;
                }
            }
        }
        $response->set('configItemDrivers', $configItemDrivers);

        $theme = null;
        if (isset($this->setting['theme'])) {
            $theme = $this->setting['theme'];
        }
        $response->display('AdminPlugin.Config.display', $theme);
    }


    public function saveConfig()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $appName = isset($this->setting['appName']) ? $this->setting['appName'] : $request->getAppName();
            $configName = $request->get('configName', '');
            if (!$configName) {
                throw new AdminPluginException('参数（configName）缺失！');
            }

            $postData = $request->json();
            $formData = $postData['formData'];

            $code = "<?php\n";
            $code .= 'namespace Be\\Data\\App\\' . $appName . '\\Config;' . "\n\n";
            $code .= 'class ' . $configName . "\n";
            $code .= "{\n";

            $className = '\\Be\\App\\' . $appName . '\\Config\\' . $configName;
            if (!class_exists($className)) {
                throw new AdminPluginException('配置项（' . $className . '）不存在！');
            }
            $configInstance = Be::getConfig('App.' . $appName . '.' . $configName);
            $originalConfigInstance = new $className();

            $newValues = [];
            $newValueStrings = [];
            $reflection = new \ReflectionClass($className);
            $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
            foreach ($properties as $property) {
                $itemName = $property->getName();
                $itemComment = $property->getDocComment();
                $parseItemComments = \Be\Util\Annotation::parse($itemComment);

                $configItem = null;
                if (isset($parseItemComments['BeConfigItem'][0])) {
                    $annotation = new BeConfigItem($parseItemComments['BeConfigItem'][0]);
                    $configItem = $annotation->toArray();
                    if (isset($configItem['value'])) {
                        $configItem['label'] = $configItem['value'];
                        unset($configItem['value']);
                    }
                } else {
                    $fn = '_' . $itemName;
                    if (is_callable([$originalConfigInstance, $fn])) {
                        $configItem = $originalConfigInstance->$fn($itemName);
                    }
                }

                if ($configItem) {
                    if (!isset($formData[$itemName])) {
                        throw new AdminPluginException('参数 (' . $itemName . ') 缺失！');
                    }

                    $configItem['name'] = $itemName;

                    $driverClass = null;
                    if (isset($configItem['driver'])) {
                        if (substr($configItem['driver'], 0, 8) == 'FormItem') {
                            $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $configItem['driver'];
                        } else {
                            $driverClass = $configItem['driver'];
                        }
                    } else {
                        $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
                    }
                    $driver = new $driverClass($configItem);
                    $driver->submit($formData);

                    $newValues[$itemName] = $driver->newValue;
                    $newValueString = null;
                    switch ($driver->valueType) {
                        case 'array(int)':
                        case 'array(float)':
                            $newValueString = '[' . implode(',', $driver->newValue) . ']';
                            break;
                        case 'array':
                        case 'array(string)':
                            $newValueString = $driver->newValue;
                            foreach ($newValueString as &$x) {
                                $x = str_replace('\'', '\\\'', $x);
                            }
                            unset($x);
                            $newValueString = '[\'' . implode('\',\'', $newValueString) . '\']';
                            break;
                        case 'mixed':
                            $newValueString = var_export($driver->newValue, true);
                            break;
                        case 'bool':
                            $newValueString = $driver->newValue ? 'true' : 'false';
                            break;
                        case 'int':
                        case 'float':
                            $newValueString = $driver->newValue;
                            break;
                        case 'string':
                            $newValueString = '\'' . str_replace('\'', '\\\'', $driver->newValue) . '\'';
                            break;
                        default:
                            $newValueString = var_export($driver->newValue, true);
                    }

                    $newValueStrings[$itemName] = $newValueString;
                } else {
                    $newValues[$itemName] = $configInstance->$itemName ?? $originalConfigInstance->$itemName;
                    $newValueStrings[$itemName] = var_export($configInstance->$itemName ?? $originalConfigInstance->$itemName, true);
                }
            }

            foreach ($newValueStrings as $k => $v) {
                $code .= '  public $' . $k . ' = ' . $newValueStrings[$k] . ';' . "\n";
            }

            $code .= "}\n";

            $path = Be::getRuntime()->getDataPath() . '/App/' . $appName . '/Config/' . $configName . '.php';
            $dir = dirname($path);
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            file_put_contents($path, $code, LOCK_EX);
            @chmod($path, 0755);

            // 更新 config 实例
            foreach ($newValues as $k => $v) {
                $configInstance->$k = $newValues[$k];
            }

            $response->success('保存成功，系统将自动重载！');

            // 重启系统
            Be::getRuntime()->reload();

        } catch (\Throwable $t) {
            $response->error('保存失败：' . $t->getMessage());
            Be::getLog()->error($t);
        }
    }

    public function resetConfig()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        try {
            $appName = isset($this->setting['appName']) ? $this->setting['appName'] : $request->getAppName();
            $configName = $request->get('configName', '');
            if (!$configName) {
                throw new AdminPluginException('参数（configName）缺失！');
            }

            $path = Be::getRuntime()->getDataPath() . '/App/' . $appName . '/Config/' . $configName . '.php';
            if (file_exists($path)) @unlink($path);

            // 更新 config 实例
            $config = Be::getConfig('App.' . $appName . '.' . $configName);

            $class = '\\Be\\App\\' . $appName . '\\Config\\' . $configName;
            $newConfig = new $class();

            $vars = get_object_vars($newConfig);
            foreach ($vars as $k => $v) {
                if (isset($config->$k)) {
                    $config->$k = $v;
                }
            }

            $vars = get_object_vars($config);
            foreach ($vars as $k => $v) {
                if (!isset($newConfig->$k)) {
                    unset($config->$k);
                }
            }

            $response->success('恢复默认值成功，系统将自动重载！');

            // 重启系统
            Be::getRuntime()->reload();

        } catch (\Throwable $t) {
            $response->error('恢复默认值失败：' . $t->getMessage());
            Be::getLog()->error($t);
        }
    }

}

