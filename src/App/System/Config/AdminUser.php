<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("后台用户")
 */
class AdminUser
{

    /**
     * @BeConfigItem("锁定IP",
     *     driver="FormItemSwitch",
     *     description="启用锁定IP时，若用户IP变化，需重新登录。")
     */
    public $ipLock = 1;

    /**
     * @BeConfigItem("用户头像宽度",
     *     driver="FormItemInputNumberInt",
     *     description="单位：像素，修改后仅对此后上传的头像生效",
     *     ui="return [':min' => 1];")
     */
    public $avatarWidth = 96;

    /**
     * @BeConfigItem("用户头像高度",
     *     driver="FormItemInputNumberInt",
     *     description="单位：像素，修改后仅对此后上传的头像生效",
     *     ui="return [':min' => 1];")
     */
    public $avatarHeight = 96;

    /**
     * @BeConfigItem("启用LDAP单点登录",
     *     driver="FormItemSwitch",
     *     description="启用锁定IP时，若用户IP变化，需重新登录。")
     */
    public $ldap = 0;

    /**
     * @BeConfigItem("LDAP服务器地址",
     *     driver="FormItemInput",
     *     ui="return ['form-item' => ['v-show' => 'formData.ldap == 1']];")
     */
    public $ldap_host = '';

    /**
     * @BeConfigItem("LDAP服务器用户名模式",
     *     driver="FormItemInput",
     *     description="示例：cu={username},ou=users,dc=phpbe.dc=com，其中 {username} 为用户名占位符",
     *     ui="return ['form-item' => ['v-show' => 'formData.ldap == 1']];")
     */
    public $ldap_pattern = '';

}
