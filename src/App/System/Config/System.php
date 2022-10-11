<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("系统")
 */
class System
{

    /**
     * @BeConfigItem("网站根网址",
     *     description="例：https://www.phpbe.com，结尾不加斜杠。为空时系统将自动检测（适合多域名的情况）。"
     *     driver = "FormItemInput")
     */
    public string $rootUrl = '';

    /**
     * @BeConfigItem("是否开启伪静态"
     *     driver = "FormItemSelect",
     *     keyValues = "return ['disable' => '不启用', 'simple' => '简单', 'router' => '路由器'];")
     */
    public string $urlRewrite = 'disable';

    /**
     * @BeConfigItem("伪静态页后辍",
     *     driver="FormItemInput",
     *     ui="return ['form-item' => ['v-show' => 'formData.urlRewrite === \'simple\'']];")
     */
    public string $urlSuffix = '.html';

    /**
     * @BeConfigItem("允许上传的文件大小", driver="FormItemInput")
     */
    public string $uploadMaxSize = '100M';

    /**
     * @BeConfigItem("允许上传的文件类型", driver="FormItemCode", language="json", valueType = "array(string)")
     */
    public array $allowUploadFileTypes = ['jpg', 'jpeg', 'gif', 'png', 'svg', 'webp', 'txt', 'pdf', 'doc', 'docx', 'csv', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar', 'ttf', 'woff'];

    /**
     * @BeConfigItem("允许上传的图片类型", driver="FormItemCode", language="json", valueType = "array(string)")
     */
    public array $allowUploadImageTypes = ['jpg', 'jpeg', 'gif', 'png', 'svg', 'webp'];

    /**
     * @BeConfigItem("时区", driver="FormItemInput")
     */
    public string $timezone = 'Asia/Shanghai';

    /**
     * @BeConfigItem("默认首页", driver="FormItemInput")
     */
    public string $home = 'System.Installer.index';

    /**
     * @BeConfigItem("是否开启开发者模式", driver="FormItemSwitch")
     */
    public int $developer = 1;

    /**
     * @BeConfigItem("是否开启可安装及重装", driver="FormItemSwitch")
     */
    public int $installable = 1;

}
