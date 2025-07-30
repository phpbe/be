<?php
namespace Be\App\System\Config;

/**
 * @BeConfig("用户")
 */
class User
{

    /**
     * @BeConfigItem("用户头像宽度",
     *     driver="FormItemInputNumberInt",
     *     description="单位：像素，修改后仅对此后上传的头像生效",
     *     ui="return [':min' => 1];")
     */
    public int $avatarWidth = 96;

    /**
     * @BeConfigItem("用户头像高度",
     *     driver="FormItemInputNumberInt",
     *     description="单位：像素，修改后仅对此后上传的头像生效",
     *     ui="return [':min' => 1];")
     */
    public int $avatarHeight = 96;

    /**
     * @BeConfigItem("Hashmap路由 - 内存缓存",
     *     driver="FormItemSwitch",
     *     description="当有使用 Hashmap路由 时才需要此配置藉由, 开启后将占用较多内存")
     */
    public int $username = 1;

    /**
     * @BeConfigItem("Hashmap路由 - 内存缓存",
     *     driver="FormItemSwitch",
     *     description="当有使用 Hashmap路由 时才需要此配置藉由, 开启后将占用较多内存")
     */
    public int $email = 1;

    /**
     * @BeConfigItem("Hashmap路由 - 内存缓存",
     *     driver="FormItemSwitch",
     *     description="当有使用 Hashmap路由 时才需要此配置藉由, 开启后将占用较多内存")
     */
    public int $mobile = 1;



}
