<?php
namespace Be\Theme\System\Section\Slider;

use Be\Theme\Section;

class Template extends Section
{
    protected array $position = ['Middle', 'Center'];

    public function display()
    {
        if ($this->config->enable) {
            ?>
            <script src="<?php echo \Be\Be::getProperty('Theme.Sample')->getWwwUrl(); ?>/lib/swiper/swiper-bundle.min.js"></script>
            <link rel="stylesheet" href="<?php echo \Be\Be::getProperty('Theme.Sample')->getWwwUrl(); ?>/lib/swiper/swiper-bundle.min.css">

            <style type="text/css">
                <?php
                $configTheme = \Be\Be::getConfig('Theme.Sample.Theme');

                echo '#slider-' .  $this->id . ' {';
                echo 'background-color: ' . $this->config->backgroundColor . ';';
                echo '}';

                // 手机端
                echo '@media (max-width: 768px) {';
                echo '#slider-' .  $this->id . ' {';
                if ($this->config->paddingTopMobile) {
                    echo 'padding-top: ' . $this->config->paddingTopMobile . 'px;';
                }
                if ($this->config->paddingBottomMobile) {
                    echo 'padding-bottom: ' . $this->config->paddingBottomMobile . 'px;';
                }
                echo '}';
                echo '}';

                // 平析端
                echo '@media (min-width: 768px) {';
                echo '#slider-' .  $this->id . ' {';
                if ($this->config->paddingTopTablet) {
                    echo 'padding-top: ' . $this->config->paddingTopTablet . 'px;';
                }
                if ($this->config->paddingBottomTablet) {
                    echo 'padding-bottom: ' . $this->config->paddingBottomTablet . 'px;';
                }
                echo '}';
                echo '}';

                // 电脑端
                echo '@media (min-width: 992px) {';
                echo '#slider-' .  $this->id . ' {';
                if ($this->config->paddingTopDesktop) {
                    echo 'padding-top: ' . $this->config->paddingTopDesktop . 'px;';
                }
                if ($this->config->paddingBottomDesktop) {
                    echo 'padding-bottom: ' . $this->config->paddingBottomDesktop . 'px;';
                }
                echo '}';
                echo '}';

                if ($this->config->pagination) {
                   echo '#slider-' .  $this->id . ' .swiper-pagination-bullet-active {';
                   echo 'background-color: ' . $configTheme->mainColor . ';';
                   echo '}';
                }

                if ($this->config->navigation) {
                    echo '#slider-' .  $this->id . ' .swiper-button-prev, ';
                    echo '#slider-' .  $this->id . ' .swiper-button-next {';
                    echo 'color: ' . $configTheme->mainColor . ';';
                    echo 'width: ' . ($this->config->navigationSize / 44 * 27) . 'px;';
                    echo 'height: ' . $this->config->navigationSize . 'px;';
                    echo 'margin-top: -' . ($this->config->navigationSize / 2) . 'px;';
                    echo '}';

                    echo '#slider-' .  $this->id . ' .swiper-button-prev:after, ';
                    echo '#slider-' .  $this->id . ' .swiper-button-next:after {';
                    echo 'font-size: ' . $this->config->navigationSize . ';';
                    echo '}';
                }

                // 手机版，电脑版上传不同的图片
                echo '@media (max-width: 768px) {';
                echo '#slider-' .  $this->id . ' .slider-image,';
                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-image {';
                echo 'display:none;';
                echo '}';
                echo '#slider-' .  $this->id . ' .slider-image-mobile,';
                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-image-mobile {';
                echo 'display:block;';
                echo '}';
                echo '}';
                // 手机版，电脑版上传不同的图片
                echo '@media (min-width: 768px) {';
                echo '#slider-' .  $this->id . ' .slider-image,';
                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-image {';
                echo 'display:block;';
                echo '}';
                echo '#slider-' .  $this->id . ' .slider-image-mobile,';
                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-image-mobile {';
                echo 'display:none;';
                echo '}';
                echo '}';

                echo '#slider-' .  $this->id . ' .slider-image img,';
                echo '#slider-' .  $this->id . ' .slider-image-mobile img,';
                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-image img,';
                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-image-mobile img {';
                echo 'width: 100%;';
                echo '}';

                echo '#slider-' .  $this->id . ' .slider-image img,';
                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-image img {';
                echo 'min-width: 1024px;';
                echo '}';

                // 未上传图像时的占位符
                echo '#slider-' .  $this->id . ' .slider-image .no-image,';
                echo '#slider-' .  $this->id . ' .slider-image-mobile .no-image,';
                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-image .no-image,';
                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-image-mobile .no-image {';
                echo 'width: 100%;';
                echo 'height: 400px;';
                echo 'line-height: 400px;';
                echo 'color: #fff;';
                echo 'font-size: 24px;';
                echo 'text-align: center;';
                echo 'text-shadow:  5px 5px 5px #999;';
                echo 'background-color: rgba(35, 35, 35, 0.2);';
                echo '}';


                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-container {';
                echo 'position: relative;';
                echo 'overflow: hidden;';
                echo '}';

                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-content-container {';
                echo 'position: absolute;';
                echo 'padding-left: 0.75rem;';
                echo 'padding-right: 0.75rem;';
                echo 'width: 100%;';
                echo 'z-index; 2;';
                echo 'top: 0;';
                echo 'bottom: 0;';
                echo '}';
                echo '@media (max-width: 768px) {';
                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-content-container {';
                echo '}';
                echo '}';
                echo '@media (min-width: 768px) {';
                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-content-container {';
                echo 'max-width: 720px;';
                echo 'left: calc((100% - 720px) / 2);';
                echo '}';
                echo '}';
                echo '@media (min-width: 992px) {';
                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-content-container {';
                echo 'max-width: 960px;';
                echo 'left: calc((100% - 960px) / 2);';
                echo '}';
                echo '}';
                echo '@media (min-width: 1200px) {';
                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-content-container {';
                echo 'max-width: 1140px;';
                echo 'left: calc((100% - 1140px) / 2);';
                echo '}';
                echo '}';
                echo '@media (min-width: 1400px) {';
                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-content-container {';
                echo 'max-width: 1320px;';
                echo 'left: calc((100% - 1320px) / 2);';
                echo '}';
                echo '}';

                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-content {';
                echo 'position: absolute;';
                echo '}';

                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-title {';
                echo 'text-align: center;';
                echo 'margin-bottom: 10px;';
                echo '}';

                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-description {';
                echo 'text-align: center;';
                echo 'margin-bottom: 35px;';
                echo '}';

                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-button {';
                echo 'text-align: center;';
                echo '}';

                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-button .be-btn {';
                echo 'background-color: transparent;';
                echo '}';

                echo '#slider-' .  $this->id . ' .slider-image-with-text-overlay-button .be-btn:hover {';
                echo 'color: #333 !important;';
                echo '}';
                ?>
            </style>

            <div class="swiper-container" id="slider-<?php echo  $this->id; ?>">
                <div class="swiper-wrapper">
                    <?php
                    if (isset($this->config->items) && is_array($this->config->items) && count($this->config->items) > 0) {
                        foreach ($this->config->items as $item) {
                            if ($item['data']['enable']) {
                                echo '<div class="swiper-slide">';
                                switch ($item['name']) {
                                    case 'Image':
                                        echo '<div class="slider-image">';
                                        if (!$item['data']['image']) {
                                            echo '<div class="no-image">1200X400px+</div>';
                                        } else {
                                            if ($item['data']['link']) {
                                                echo '<a href="'.$item['data']['link'].'">';
                                            }
                                            echo '<img src="' . $item['data']['image'] . '" />';
                                            if ($item['data']['link']) {
                                                echo '</a>';
                                            }
                                        }
                                        echo '</div>';

                                        echo '<div class="slider-image-mobile">';
                                        if (!$item['data']['image']) {
                                            echo '<div class="no-image">720X400px+</div>';
                                        } else {
                                            if ($item['data']['link']) {
                                                echo '<a href="'.$item['data']['link'].'">';
                                            }
                                            echo '<img src="' . $item['data']['imageMobile'] . '" />';
                                            if ($item['data']['link']) {
                                                echo '</a>';
                                            }
                                        }
                                        echo '</div>';
                                        break;
                                    case 'ImageWithTextOverlay':

                                        echo '<div class="slider-image-with-text-overlay">';
                                        echo '<div class="slider-image-with-text-overlay-container">';

                                        echo '<div class="slider-image-with-text-overlay-image">';
                                        if (!$item['data']['image']) {
                                            echo '<div class="no-image">1200X400px+</div>';
                                        } else {
                                            echo '<img src="' . $item['data']['image'] . '">';
                                        }
                                        echo '</div>';
                                        echo '<div class="slider-image-with-text-overlay-image-mobile">';
                                        if (!$item['data']['imageMobile']) {
                                            echo '<div class="no-image">720X400px+</div>';
                                        } else {
                                            echo '<img src="' . $item['data']['imageMobile'] . '">';
                                        }
                                        echo '</div>';

                                        echo '<div class="slider-image-with-text-overlay-content-container">';
                                        echo '<div class="slider-image-with-text-overlay-content" style="';
                                        echo 'width: ' . $item['data']['contentWidth'] . 'px;';
                                        if ($item['data']['contentPosition'] === 'custom') {
                                            if ($item['data']['contentPositionLeft'] >= 0) {
                                                echo 'left: ' . $item['data']['contentPositionLeft'] . 'px;';
                                            }
                                            if ($item['data']['contentPositionRight'] >= 0) {
                                                echo 'right: ' . $item['data']['contentPositionRight'] . 'px;';
                                            }
                                            if ($item['data']['contentPositionTop'] >= 0) {
                                                echo 'top: ' . $item['data']['contentPositionTop'] . 'px;';
                                            }
                                            if ($item['data']['contentPositionBottom'] >= 0) {
                                                echo 'bottom: ' . $item['data']['contentPositionBottom'] . 'px;';
                                            }
                                        } else {
                                            echo 'top: 50%;';
                                            echo 'transform: translateY(-50%);';
                                            if ($item['data']['contentPosition'] === 'left') {
                                                echo 'left: 5%;';
                                            } elseif ($item['data']['contentPosition'] === 'center') {
                                                echo 'left: 50%;';
                                                echo 'transform: translateX(-50%);';
                                            } elseif ($item['data']['contentPosition'] === 'right') {
                                                echo 'right: 5%;';
                                            }
                                        }
                                        echo '">';
                                        echo '<h2 class="slider-image-with-text-overlay-title" style="color: ' . $item['data']['contentTitleColor'] . ';font-size: ' . $item['data']['contentTitleFontSize'] . 'px;">' . $item['data']['contentTitle'] . '</h2>';
                                        echo '<div class="slider-image-with-text-overlay-description" style="color: ' . $item['data']['contentDescriptionColor'] . ';font-size: ' . $item['data']['contentDescriptionFontSize'] . 'px;">' . $item['data']['contentDescription'] . '</div>';
                                        echo '<div class="slider-image-with-text-overlay-button">';
                                        echo '<a href="' . $item['data']['contentButtonLink'] . '" class="be-btn be-btn-large" style="color: ' . $item['data']['contentButtonColor'] . ';border-color: ' . $item['data']['contentButtonColor'] . ';" onMouseOver="this.style.backgroundColor=\'' . $item['data']['contentButtonColor'] . '\'" onMouseOut="this.style.backgroundColor=\'transparent\'">' . $item['data']['contentButton'] . '</a>';
                                        echo '</div>';

                                        echo '</div>';
                                        echo '</div>';

                                        echo '</div>';
                                        echo '</div>';

                                        break;
                                    case 'Video':

                                        break;
                                }
                                echo '</div>';
                            }
                        }
                    }
                    ?>
                </div>

                <?php
                if ($this->config->pagination && count($this->config->items) > 1) {
                    ?>
                    <div class="swiper-pagination"></div>
                    <?php
                }

                if ($this->config->navigation && count($this->config->items) > 1) {
                    ?>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                    <?php
                }
                ?>
            </div>
            <script>
                $(document).ready(function(){
                    new Swiper('#slider-<?php echo  $this->id; ?>', {
                        <?php
                        if (count($this->config->items) > 1) {
                            if ($this->config->autoplay) {
                                echo 'autoplay: true,';
                                echo 'delay: ' . $this->config->delay . ',';
                                echo 'speed: ' . $this->config->speed . ',';
                            }

                            if ($this->config->loop) {
                                echo 'loop: true,';
                            }

                            if ($this->config->pagination) {
                                echo 'pagination: {el: \'.swiper-pagination\'},';
                            }

                            if ($this->config->navigation) {
                                echo 'navigation: {nextEl: \'.swiper-button-next\', prevEl: \'.swiper-button-prev\'},';
                            }
                            echo 'grabCursor : true';
                        } else {
                            echo 'enabled:false';
                        }
                        ?>

                    });
                });
            </script>
        <?php
        }
    }
}
