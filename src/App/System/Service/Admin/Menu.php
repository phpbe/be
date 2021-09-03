<?php

namespace Be\App\System\Service\Admin;

use Be\Util\Annotation;
use Be\Be;

class Menu
{

    private $menus = null;

    /**
     * 获取后台菜单
     */
    public function getMenus()
    {
        return [];
    }

    /**
     * 更新事台菜单
     */
    public function update()
    {
        $menus = $this->getMenus();

        $code = '<?php' . "\n";
        $code .= 'namespace Be\\Data\\Cache;' . "\n";
        $code .= "\n";
        $code .= 'class Menu extends \\Be\\Menu\\Driver' . "\n";
        $code .= '{' . "\n";
        $code .= '  public function __construct()' . "\n";
        $code .= '  {' . "\n";

        foreach ($menus as $k => $v) {
            $app = $v['app'];
            $code .= '    $this->addMenu(\'' . $app->key . '\', \'0\', \'' . $app->icon . '\',\'' . $app->label . '\', \'\', \'\');' . "\n";
            foreach ($v['groups'] as $key => $val) {
                $group = $val['group'];
                $code .= '    $this->addMenu(\'' . $group['key'] . '\',\'' . $app->key . '\',\'' . (isset($group['icon']) ? $group['icon'] : 'el-icon-folder') . '\',\'' . $group['label'] . '\', \'\', \'\');' . "\n";
                foreach ($val['menus'] as $menu) {
                    $code .= '    $this->addMenu(\'' . $menu['key'] . '\', \'' . $group['key'] . '\', \'' . (isset($menu['icon']) ? $menu['icon'] : 'el-icon-arrow-right') . '\', \'' . $menu['label'] . '\', ' . $menu['url'] . ', \'' . (isset($menu['target']) ? $menu['target'] : '') . '\');' . "\n";
                }
            }
        }
        $code .= '  }' . "\n";
        $code .= '}' . "\n";

        $path = Be::getRuntime()->getCachePath() . '/Menu.php';
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        file_put_contents($path, $code, LOCK_EX);
        @chmod($path, 0755);
    }

}
