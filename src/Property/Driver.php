<?php

namespace Be\Property;

use Be\Be;
use Be\Runtime\RuntimeException;
use Be\Util\File\Dir;
use Be\Util\Str\CaseConverter;

/**
 * 属性基类
 */
abstract class Driver
{
    protected string $type = ''; // 类型: app/theme/admin-theme/admin-plugin 等
    protected string $name = ''; // 名称
    protected string $label = ''; // 中文名
    protected string $icon = ''; // 图标
    protected string $description = ''; // 描述
    protected string $path = ''; // 路径，相对于根路径
    protected ?string $wwwUrl = null; // www 目录的真实网址

    /**
     * 构造函数
     * @param string $path 文件咱径
     */
    public function __construct(string $path = '')
    {
        $class = get_called_class();
        $name = substr($class, 0, strrpos($class, '\\'));
        $name = substr($name, strrpos($name, '\\') + 1);
        $this->name = $name;

        $this->path = str_replace(Be::getRuntime()->getRootPath(), '', substr($path, 0, strrpos($path, DIRECTORY_SEPARATOR)));
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * 获取网址
     *
     * @return string
     */
    public function getWwwUrl(): string
    {
        if ($this->wwwUrl === null) {

            $dir = '/' . $this->type . '/' . \Be\Util\Str\CaseConverter::camel2Hyphen($this->name);

            $configWww = Be::getConfig('App.System.Www');
            if ($configWww->cdnEffect) {
                $this->wwwUrl = Be::getStorage()->getRootUrl() .  $dir;
                return $this->wwwUrl;
            }

            if (Be::getConfig('App.System.System')->developer === 1) {
                $rootPath = Be::getRuntime()->getRootPath();
                $dst = $rootPath . '/www' . $dir;
                if (!is_dir($dst)) {
                    $src = $rootPath . $this->path . '/www';
                    if (is_dir($src)) {
                        Dir::copy($src, $dst, true);
                    }
                }
            }

            $this->wwwUrl = Be::getRequest()->getRootUrl() . $dir;
        }

        return $this->wwwUrl;
    }


    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        } else {
            throw new RuntimeException($name . ' 属性未定义！');
        }
    }

}
