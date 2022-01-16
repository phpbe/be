<?php

namespace Be\App\System\Service\Admin;

use Be\Util\Annotation;
use Be\Be;

class Menu
{
    private $menus = [];
    private $items = [];

    /**
     * 获取菜单
     */
    public function getMenu($name)
    {
        if (!isset($this->menus[$name])) {
            $sql = 'SELECT * FROM system_menu WHERE `name`=?';
            $this->menus[$name] = Be::getDb()->getObject($sql, [$name]);
        }
        return $this->menus[$name];
    }

    /**
     * 获取菜单项
     */
    public function getItems($menuName)
    {
        if (!isset($this->items[$menuName])) {
            $sql = 'SELECT * FROM system_menu_item WHERE menu_name=? ORDER BY ordering ASC';
            $this->items[$menuName] = Be::getDb()->getObjects($sql, [$menuName]);
        }
        return $this->items[$menuName];
    }

    /**
     * 更新事台菜单
     */
    public function update($name)
    {
        $menus = $this->getItems($name);

        $code = '<?php' . "\n";
        $code .= 'namespace Be\\Data\\Cache\\Menu;' . "\n";
        $code .= "\n";
        $code .= 'class ' . $name . ' extends \\Be\\Menu\\Driver' . "\n";
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
            $code .= '    $this->addItem(\'' . $v->id . '\', \'' . $v->parent_id . '\', \'' . $v->name . '\', \'' . $v->route . '\', ' . var_export($params, true) . ', \'' . $v->url . '\', \'' . $v->target . '\');' . "\n";
        }

        $code .= '  }' . "\n";
        $code .= '}' . "\n";

        $path = Be::getRuntime()->getCachePath() . '/Menu/' . $name . '.php';
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        file_put_contents($path, $code, LOCK_EX);
        @chmod($path, 0755);
    }

}
