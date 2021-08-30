<?php
namespace Be\Theme;

/**
 * 主题属性
 */
abstract class Property extends \Be\Property\Driver
{

    /**
     * 可䨒置的页面
     *
     * @var string[]
     */
    public $pages = ['Home'];

}
