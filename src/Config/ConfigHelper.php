<?php

namespace Be\Config;

use Be\Be;

class ConfigHelper
{

    /**
     * 保存配置
     *
     * @param string $name 配置名称，格式：应用名.配置名
     * @param object $instance 配置实例
     */
    public static function update($name, $instance)
    {
        $parts = explode('.', $name);
        $type = array_shift($parts);
        $catalog = array_shift($parts);
        $className = array_pop($parts);

        $runtime = Be::getRuntime();
        $code = "<?php\n";
        $namespace = 'Be\\Data\\' . $type . '\\' . $catalog . '\\Config';
        if (count($parts) > 0) {
            $namespace .= '\\' . implode('\\', $parts);
        }

        $code .= 'namespace ' . $namespace . ";\n\n";
        $code .= 'class ' . $className . "\n";
        $code .= "{\n";

        $vars = get_object_vars($instance);
        foreach ($vars as $k => $v) {
            $code .= '  public $' . $k . ' = ' . var_export($v, true) . ';' . "\n";
        }
        $code .= "}\n";

        $path = $runtime->getRootPath() . '/data/' . $type . '/' . $catalog . '/Config/' . implode('/', $parts) . '/' . $className . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        file_put_contents($path, $code, LOCK_EX);
        chmod($path, 0777);
    }

    public static function reset($name)
    {
        $parts = explode('.', $name);
        $type = array_shift($parts);
        $catalog = array_shift($parts);
        $className = array_pop($parts);

        $path = Be::getRuntime()->getRootPath() . '/data/' . $type . '/' . $catalog . '/Config/' . implode('/', $parts) . '/' . $className . '.php';
        if (file_exists($path)) {
            unlink($path);
        }
    }
}


