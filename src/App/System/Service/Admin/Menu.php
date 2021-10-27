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
        if ($this->menus === null) {
            $sql = 'SELECT * FROM system_menu WHERE is_enable = 1 AND is_delete = 0 ORDER BY ordering ASC';
            $this->menus = Be::getDb()->getObjects($sql);
        }
        return $this->menus;
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
            $params = [];
            if ($v->params) {
                parse_str($v->params, $parsedParams);
                if ($parsedParams) {
                    $params = $parsedParams;
                }
            }
            $code .= '    $this->addMenu(\'' . $v->id . '\', \'' . $v->parent_id . '\', \'' . $v->name . '\', \'' . $v->route . '\', ' . var_export($params, true) . ', \'' . $v->url . '\', \'' . $v->target . '\');' . "\n";
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
