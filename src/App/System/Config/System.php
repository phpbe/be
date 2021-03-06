<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("系统")
 */
class System
{
    /**
     * @BeConfigItem("是否开启伪静态"
     *     driver = "FormItemSelect",
     *     keyValues = "return ['0' => '不启用', '1' => '简单', '2' => '路由器'];")
     */
    public $urlRewrite = '0';

    /**
     * @BeConfigItem("伪静态页后辍",
     *     driver="FormItemInput",
     *     ui="return ['form-item' => ['v-show' => 'formData.urlRewrite === \'1\'']];")
     */
    public $urlSuffix = '.html';

    /**
     * @BeConfigItem("允许上传的文件大小", driver="FormItemInput")
     */
    public $uploadMaxSize = '100M';

    /**
     * @BeConfigItem("允许上传的文件类型", driver="FormItemCode", language="json", valueType = "array(string)")
     */
    public $allowUploadFileTypes = ['jpg', 'jpeg', 'gif', 'png', 'svg', 'webp', 'txt', 'pdf', 'doc', 'docx', 'csv', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar', 'ttf', 'woff'];

    /**
     * @BeConfigItem("允许上传的图片类型", driver="FormItemCode", language="json", valueType = "array(string)")
     */
    public $allowUploadImageTypes = ['jpg', 'jpeg', 'gif', 'png', 'svg', 'webp'];

    /**
     * @BeConfigItem("时区", driver="FormItemInput")
     */
    public $timezone = 'Asia/Shanghai';

    /**
     * @BeConfigItem("默认首页", driver="FormItemInput")
     */
    public $home = 'System.Installer.index';

    /**
     * @BeConfigItem("是否开启开发者模式", driver="FormItemSwitch")
     */
    public $developer = 1;

    /**
     * @BeConfigItem("是否开启可安装及重装", driver="FormItemSwitch")
     */
    public $installable = 1;

}
