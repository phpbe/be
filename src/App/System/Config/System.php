<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("系统")
 */
class System
{
    /**
     * @BeConfigItem("是否开启伪静态", driver="FormItemSwitch")
     */
    public $urlRewrite = 0;

    /**
     * @BeConfigItem("伪静态页后辍", driver="FormItemInput")
     */
    public $urlSuffix = '.html';

    /**
     * @BeConfigItem("允许上传的文件大小", driver="FormItemInput")
     */
    public $uploadMaxSize = '100M';

    /**
     * @BeConfigItem("允许上传的文件类型", driver="FormItemCode", language="json", valueType = "array(string)")
     */
    public $allowUploadFileTypes = ['jpg', 'jpeg', 'gif', 'png', 'webp', 'txt', 'pdf', 'doc', 'docx', 'csv', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar'];

    /**
     * @BeConfigItem("允许上传的图片类型", driver="FormItemCode", language="json", valueType = "array(string)")
     */
    public $allowUploadImageTypes = ['jpg', 'jpeg', 'gif', 'png', 'webp'];

    /**
     * @BeConfigItem("时区", driver="FormItemInput")
     */
    public $timezone = 'Asia/Shanghai';

    /**
     * @BeConfigItem("主题",
     *     driver="FormItemSelect",
     *     keyValues = "return \Be\Be::getAdminService('System.Theme')->getThemeKeyValues();")
     */
    public $theme = 'Blank';

    /**
     * @BeConfigItem("默认首页", driver="FormItemInput")
     */
    public $home = 'System.Index.index';

    /**
     * @BeConfigItem("是否开启开发者模式", driver="FormItemSwitch")
     */
    public $developer = true;

    /**
     * @BeConfigItem("是否开启可安装及重装", driver="FormItemSwitch")
     */
    public $installable = true;

}
