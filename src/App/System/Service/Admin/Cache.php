<?php

namespace Be\App\System\Service\Admin;

use Be\Be;
use Be\Util\FileSystem\FileSize;

class Cache
{

    const CATEGORIES = [
        [
            'name' => 'AdminRole',
            'label' => '角色缓存',
            'description' => '角色资料、角色权限缓存',
            'type' => 'folder',
            'icon' => 'el-icon-folder',
        ],
        [
            'name' => 'AdminPermission',
            'label' => '权限缓存',
            'description' => '系统权限缓存',
            'type' => 'folder',
            'icon' => 'el-icon-folder',
        ],
        [
            'name' => 'Table',
            'label' => '表对象缓存',
            'description' => '表结构缓存，当表结构有变更时，需手动清除。',
            'type' => 'folder',
            'icon' => 'el-icon-folder',
        ],
        [
            'name' => 'TableProperty',
            'label' => '表属性缓存',
            'description' => '解析表结构结果缓存，当表结构有变更时，需手动清除。',
            'type' => 'folder',
            'icon' => 'el-icon-folder',
        ],
        [
            'name' => 'Tuple',
            'label' => '行对象缓存',
            'description' => '当表结构有变更时，需手动清除。',
            'type' => 'folder',
            'icon' => 'el-icon-folder',
        ],
        [
            'name' => 'AdminTemplate',
            'label' => '后台模板缓存',
            'description' => '按不同主题编译生成的模板缓存数据，当主题，模板变更时，需手动清除。',
            'type' => 'folder',
            'icon' => 'el-icon-folder',
        ],
        [
            'name' => 'Template',
            'label' => '模板缓存',
            'description' => '按不同主题编译生成的模板缓存数据，当主题，模板变更时，需手动清除。',
            'type' => 'folder',
            'icon' => 'el-icon-folder',
        ],
        [
            'name' => 'AdminMenu.php',
            'label' => '后台菜单缓存',
            'description' => '从注解中读取的菜单配置缓存，代码中菜单类注解有变动时，需手动清除。',
            'type' => 'file',
            'icon' => 'el-icon-document',
        ],
        [
            'name' => 'Menu.php',
            'label' => '菜单缓存',
            'description' => '从注解中读取的菜单配置缓存，代码中菜单类注解有变动时，需手动清除。',
            'type' => 'file',
            'icon' => 'el-icon-document',
        ],
    ];

    public function getCategories()
    {
        $categories = [];
        foreach (static::CATEGORIES as $v) {
            $path = Be::getRuntime()->getCachePath() . '/' . $v['name'];
            $count = $this->getFileCount($path);
            $size = $this->getFileSize($path);
            $sizeStr = FileSize::int2String($size);

            $categories[] = array_merge($v, [
                'count' => $count,
                'size' => $size,
                'sizeStr' => $sizeStr,
            ]);
        }
        return $categories;
    }


    /**
     * 清除缓存
     *
     * @param string $name 缓存类型
     * @return bool 是否清除成功
     */
    public function delete($name = null)
    {
        if ($name === null) {
            $success = true;
            foreach (static::CATEGORIES as $v) {
                if (!$this->delete($v['name'])) {
                    $success = false;
                }
            }
            return $success;
        }

        return \Be\Util\FileSystem\Dir::rm(Be::getRuntime()->getCachePath() . '/' . $name);
    }


    private function getFileCount($path)
    {
        if (is_dir($path)) {
            $count = 0;
            $handle = opendir($path);
            while (($file = readdir($handle)) !== false) {
                if ($file != '.' && $file != '..') {
                    $count += $this->getFileCount($path . '/' . $file);
                }
            }
            closedir($handle);

            return $count;
        } else {
            return file_exists($path) ? 1 : 0;
        }
    }


    private function getFileSize($path)
    {
        if (is_dir($path)) {
            $size = 0;
            $handle = opendir($path);
            while (($file = readdir($handle)) !== false) {
                if ($file != '.' && $file != '..') {
                    $size += $this->getFileSize($path . '/' . $file);
                }
            }
            closedir($handle);

            return $size;
        } else {
            return file_exists($path) ? filesize($path) : 0;
        }
    }
}
