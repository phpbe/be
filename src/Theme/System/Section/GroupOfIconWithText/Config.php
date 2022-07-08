<?php
namespace Be\Theme\System\Section\GroupOfIconWithText;


/**
 * @BeConfig("一组图标和文字", icon="el-icon-menu")
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
     *     driver = "FormItemColorPicker"
     * )
     */
    public string $backgroundColor = '#fff';

    /**
     * @BeConfigItem("子项背景颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public string $itemBackgroundColor = '#f7f7f7';

    /**
     * @BeConfigItem("子项标题颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public string $itemTitleColor = '#333333';

    /**
     * @BeConfigItem("子项链接颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public string $itemLinkColor = '#999';

    /**
     * @BeConfigItem("顶部内边距 - 电脑端（像素）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingTopDesktop = 40;

    /**
     * @BeConfigItem("顶部内边距 - 平板端（像素）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingTopTablet = 30;

    /**
     * @BeConfigItem("顶部内边距 - 手机端（像素）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingTopMobile = 20;

    /**
     * @BeConfigItem("底部内边距 - 电脑端（像素）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingBottomDesktop = 40;

    /**
     * @BeConfigItem("底部内边距 - 平板端（像素）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingBottomTablet = 30;

    /**
     * @BeConfigItem("底部内边距 - 手机端（像素）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $paddingBottomMobile = 20;

    /**
     * @BeConfigItem("间距 - 电脑端（像素）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $spacingDesktop = 40;

    /**
     * @BeConfigItem("间距 - 平板端（像素）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $spacingTablet = 30;

    /**
     * @BeConfigItem("间距 - 手机端（像素）",
     *     driver = "FormItemInputNumberInt",
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $spacingMobile = 20;

    /**
     * @BeConfigItem("子项",
     *     driver = "FormItemsConfigs",
     *     items = "return [
     *          \Be\Theme\System\Section\GroupOfIconWithText\Item\IconWithText::class,
     *     ]"
     * )
     */
    public array $items = [];

}
