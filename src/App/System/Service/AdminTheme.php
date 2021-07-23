<?php
namespace Be\App\System\Service;

use Be\Be;
use Be\App\ServiceException;

class AdminTheme
{

    private $themes = null;
    public function getThemes()
    {
        if ($this->themes === null) {
            $this->themes = Be::getDb()->getKeyObjects('SELECT * FROM system_admin_theme', null, 'name');
        }

        return $this->themes;
    }

    public function getThemeKeyValues(){
        return array_keys($this->getThemes());
    }

    public function getThemeCount()
    {
        return count($this->getThemes());
    }

    /**
     * @param string $themeName 主题用名
     * @return bool
     * @throws ServiceException
     */
    public function install($themeName)
    {
        try {
            $exist = Be::getTuple('system_admin_theme')->loadBy('name', $themeName);
            throw new ServiceException('主题已于' . $exist->install_time . '安装过！');
        } catch (\Throwable $t) {

        }

        $property = Be::getProperty('AdminTheme.' . $themeName);
        Be::getDb()->insert('system_admin_theme', [
            'name' => $property->getName(),
            'label' => $property->getLabel(),
            'install_time' => date('Y-m-d H:i:s')
        ]);

        return true;
    }

    /**
     * 卸载应用
     *
     * @param string $themeName 应应用名
     * @return bool
     * @throws ServiceException
     */
    public function uninstall($themeName)
    {
        $exist = null;
        try {
            $exist = Be::getTuple('system_admin_theme')->loadBy('name', $themeName);
        } catch (\Throwable $t) {
            throw new ServiceException('该主题尚未安装！');
        }

        $exist->delete();
        return true;
    }
}
