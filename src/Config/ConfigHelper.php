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

        $constructCode = '';

        $vars = get_object_vars($instance);
        foreach ($vars as $k => $v) {
            $varType = '';
            if (is_object($v)) {
                $code .= '  public ?object $' . $k . ' = null;' . "\n";
                $constructCode .= '    $this->' . $k . ' = ' . self::encode($v). ';' . "\n";
            } elseif (is_array($v)) {
                $code .= '  public ?array $' . $k . ' = null;' . "\n";
                $constructCode .= '    $this->' . $k . ' = ' . self::encode($v). ';' . "\n";
            } else {
                if (is_int($v)) {
                    $varType .= 'int';
                } elseif (is_float($v)) {
                    $varType .= 'float';
                } else {
                    $varType .= 'string';
                }

                $code .= '  public ' . $varType . ' $' . $k . ' = ' . var_export($v, true) . ';' . "\n";
            }
        }

        if (!$constructCode !== '') {
            $code .= "\n";
            $code .= '  public function __construct() {' . "\n";
            $code .= $constructCode;
            $code .= '  }' . "\n";
            $code .= "\n";
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

    public static function encode($x)
    {
        $code = '';
        if (is_object($x)) {
            $arr = get_object_vars($x);
            $code .= '(object)[';
            foreach ($arr as $k => $v) {
                $code .= '\'' . $k . '\'';
                $code .= ' => ';
                $code .= self::encode($v);
                $code .= ',';
            }
            $code .= ']';
        } else if (is_array($x)) {
            $code .= '[';
            $i = 0;
            foreach ($x as $k => $v) {
                if ($i !== $k) {
                    $code .= '\'' . $k . '\'';
                    $code .= ' => ';
                }
                $code .= self::encode($v);
                $code .= ',';
                $i++;
            }
            $code .= ']';
        } else {
            $code = var_export($x, true);
        }

        return $code;
    }
}


