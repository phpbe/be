<?php
namespace Be\Theme\System\Section\PageContent;


/**
 * @BeConfig("页面主体内容", icon="el-icon-fa fa-navicon", ordering="2")
 */
class Config
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public int $enable = 1;

    /**
     * @BeConfigItem("宽度",
     *     description="位于middle时有效",
     *     driver="FormItemSelect",
     *     keyValues = "return ['default' => '默认', 'fullWidth' => '全屏'];"
     * )
     */
    public string $width = 'default';

    /**
     * @BeConfigItem("背景颜色",
     *     driver="FormItemColorPicker"
     * )
     */
    public string $backgroundColor = '#fff';

    /**
     * @BeConfigItem("顶部内边距 - 电脑端（像素）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingTopDesktop = 0;

    /**
     * @BeConfigItem("顶部内边距 - 平板端（像素）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingTopTablet = 0;

    /**
     * @BeConfigItem("顶部内边距 - 手机端（像素）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingTopMobile = 0;

    /**
     * @BeConfigItem("底部内边距 - 电脑端（像素）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingBottomDesktop = 0;

    /**
     * @BeConfigItem("底部内边距 - 平板端（像素）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingBottomTablet = 0;

    /**
     * @BeConfigItem("底部内边距 - 手机端（像素）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingBottomMobile = 0;

}
