<?php

namespace Be\Util\File;

/**
 * 文件夹操作类
 *
 * Class Dir
 * @package Be\Util\FileSystem
 */
class Dir
{

    /**
     * 删除文件夹, 同时删除文件夹下的所有文件
     *
     * @param string $path 文件路径
     * @return bool
     */
    public static function rm(string $path): bool
    {
        if (!file_exists($path)) {
            return true;
        }

        if (is_dir($path)) {
            $handle = opendir($path);
            while (($file = readdir($handle)) !== false) {
                if ($file !== '.' && $file !== '..') {
                    self::rm($path . '/' . $file);
                }
            }
            closedir($handle);

            rmdir($path);
        } else {
            unlink($path);
        }

        return true;
    }

    /**
     * 复制文件夹
     *
     * @param string $srcDir 源文件夹
     * @param string $dstDir 目标文件夹
     * @param bool $overWrite 是否覆盖
     * @return bool
     */
    public static function copy(string $srcDir, string $dstDir, bool $overWrite = false): bool
    {
        $srcDirSource = opendir($srcDir);

        if (!is_dir($dstDir)) {
            mkdir($dstDir, 0777, true);
            @chmod($dstDir, 0777);
        }

        if ($srcDirSource) {
            while (false !== ($file = readdir($srcDirSource))) {
                if ($file !== '.' && $file !== '..') {
                    $srcPath = $srcDir . '/' . $file;
                    $dstPath = $dstDir . '/' . $file;
                    if (is_dir($srcPath)) {
                        self::copy($srcPath, $dstPath, $overWrite);
                    } else {
                        if (file_exists($dstPath)) {
                            if ($overWrite) {
                                unlink($dstPath);
                            } else {
                                continue;
                            }
                        }

                        copy($srcPath, $dstPath);
                    }
                }
            }
        }

        closedir($srcDirSource);
        return true;
    }

    /**
     * 移动文件夹
     *
     * @param string $srcDir 源文件夹
     * @param string $dstDir 目标文件夹
     * @param bool $overWrite 是否覆盖
     * @return bool
     */
    public static function move(string $srcDir, string $dstDir, bool $overWrite = false): bool
    {
        $srcDirSource = opendir($srcDir);

        if (!is_dir($dstDir)) {
            mkdir($dstDir, 0777, true);
            @chmod($dstDir, 0777);
        }

        if ($srcDirSource) {
            while (false !== ($file = readdir($srcDirSource))) {
                if ($file !== '.' && $file !== '..') {
                    $srcPath = $srcDir . '/' . $file;
                    $dstPath = $dstDir . '/' . $file;

                    if (is_dir($srcPath)) {
                        self::move($srcPath, $dstPath, $overWrite);
                        rmdir($srcPath);
                    } else {
                        if (file_exists($dstPath)) {
                            if ($overWrite) {
                                unlink($dstPath);
                            } else {
                                continue;
                            }
                        }

                        rename($srcPath, $dstPath);
                    }
                }
            }
        }

        closedir($srcDirSource);
        rmdir($srcDir);
        return true;
    }


}
