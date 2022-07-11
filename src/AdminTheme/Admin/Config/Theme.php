<?php
namespace Be\AdminTheme\Admin\Config;

/**
 * @BeConfig("主题")
 */
class Theme
{

    /**
     * @BeConfigItem("Logo",
     *     driver="FormItemStorageImage",
     *     description="200px x 60px，左侧菜单折叠时，显示最左侧 64px"
     * )
     */
    public string $logo = '';


}
