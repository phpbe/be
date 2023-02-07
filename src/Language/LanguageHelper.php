<?php

namespace Be\Language;

use Be\Be;

class LanguageHelper
{

    /**
     * 更新语言包
     *
     * @param string $package 语言包包
     * @param string $languageName 语言名
     * @throws \Exception
     */
    public static function update(string $package, string $languageName)
    {

        $underlineLanguageName = str_replace('-', '_', $languageName);

        $path = Be::getRuntime()->getRootPath() . '/data/Runtime/Language/' . str_replace('.', '/', $package) . '/'. $underlineLanguageName . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            @chmod($dir, 0777);
        }

        $namespace = 'Be\\Data\\Runtime\\Language\\' . str_replace('.', '\\', $package);

        $codePhp = '<?php' . "\n";
        $codePhp .= 'namespace ' . $namespace . ";\n\n";
        $codePhp .= "\n";
        $codePhp .= 'class ' . $underlineLanguageName . ' extends \\Be\\Language\\Driver' ."\n";
        $codePhp .= '{' . "\n";
        $codePhp .= '  public string $package = \'' . $package . '\';' . "\n";
        $codePhp .= '  public string $name = \'' . $languageName . '\';' . "\n";

        $property = Be::getProperty($package);
        $languageFile = $property->getPath() . '/Language/' . $languageName . '.ini';
        $keyValues = [];
        if (file_exists($languageFile)) {
            $iniKeyValues = parse_ini_file($languageFile, false, INI_SCANNER_RAW);
            if ($iniKeyValues !== false) {
                $keyValues = $iniKeyValues;
            }
        }

        $codePhp .= '  public array $keyValues = ' . var_export($keyValues, true) . ';' . "\n";
        $codePhp .= '}' . "\n\n";

        file_put_contents($path, $codePhp, LOCK_EX);
        @chmod($path, 0777);
    }


    /**
     * 文件是否有改动
     *
     * @param string $package 语言包包
     * @param string $languageName 语言名
     * @return bool
     * @throws \Exception
     */
    public static function hasChange(string $package, string $languageName): bool
    {
        $underlineLanguageName = str_replace('-', '_', $languageName);
        $compiledFile = Be::getRuntime()->getRootPath() . '/data/Runtime/Language/' . str_replace('.', '/', $package) . '/' . $underlineLanguageName . '.php';

        $property = Be::getProperty($package);
        $languageFile = $property->getPath() . '/Language/' . $languageName . '.ini';

        $compileTime = file_exists($compiledFile) ? filemtime($compiledFile) : 0;
        $languageModifyTime = file_exists($languageFile) ? filemtime($languageFile) : 0;

        return $languageModifyTime > $compileTime;
    }

}


