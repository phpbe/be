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


}
