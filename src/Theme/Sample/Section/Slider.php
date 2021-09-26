<?php if ($sectionData['enable']) { ?>
<script src="<?php echo \Be\Be::getProperty('Theme.Sample')->getUrl(); ?>/js/swiper-bundle.min.js"></script>
<link rel="stylesheet" href="<?php echo \Be\Be::getProperty('Theme.Sample')->getUrl(); ?>/css/swiper-bundle.min.css">

<style type="text/css">
    <?php
    $configTheme = \Be\Be::getConfig('Theme.Sample.Theme');

    echo '#slider-' . $sectionType . '-' . $sectionKey . ' {';
    echo 'background-color: ' . $sectionData['backgroundColor'] . ';';
    echo '}';

    // 手机端
    echo '@media (max-width: 768px) {';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' {';
    if ($sectionData['paddingTopMobile']) {
        echo 'padding-top: ' . $sectionData['paddingTopMobile'] . 'px;';
    }
    if ($sectionData['paddingBottomMobile']) {
        echo 'padding-bottom: ' . $sectionData['paddingBottomMobile'] . 'px;';
    }
    echo '}';
    echo '}';

    // 平析端
    echo '@media (min-width: 768px) {';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' {';
    if ($sectionData['paddingTopTablet']) {
        echo 'padding-top: ' . $sectionData['paddingTopTablet'] . 'px;';
    }
    if ($sectionData['paddingBottomTablet']) {
        echo 'padding-bottom: ' . $sectionData['paddingBottomTablet'] . 'px;';
    }
    echo '}';
    echo '}';

    // 电脑端
    echo '@media (min-width: 992px) {';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' {';
    if ($sectionData['paddingTopDesktop']) {
        echo 'padding-top: ' . $sectionData['paddingTopDesktop'] . 'px;';
    }
    if ($sectionData['paddingBottomDesktop']) {
        echo 'padding-bottom: ' . $sectionData['paddingBottomDesktop'] . 'px;';
    }
    echo '}';
    echo '}';

    if ($sectionData['pagination']) {
       echo '#slider-' . $sectionType . '-' . $sectionKey . ' .swiper-pagination-bullet-active {';
       echo 'background-color: ' . $configTheme->mainColor . ';';
       echo '}';
    }

    if ($sectionData['navigation']) {
        echo '#slider-' . $sectionType . '-' . $sectionKey . ' .swiper-button-prev, ';
        echo '#slider-' . $sectionType . '-' . $sectionKey . ' .swiper-button-next {';
        echo 'color: ' . $configTheme->mainColor . ';';
        echo 'width: ' . ($sectionData['navigationSize'] / 44 * 27) . 'px;';
        echo 'height: ' . $sectionData['navigationSize'] . 'px;';
        echo 'margin-top: -' . ($sectionData['navigationSize'] / 2) . 'px;';
        echo '}';

        echo '#slider-' . $sectionType . '-' . $sectionKey . ' .swiper-button-prev:after, ';
        echo '#slider-' . $sectionType . '-' . $sectionKey . ' .swiper-button-next:after {';
        echo 'font-size: ' . $sectionData['navigationSize'] . ';';
        echo '}';
    }

    // 手机版，电脑版上传不同的图片
    echo '@media (max-width: 768px) {';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image,';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-image {';
    echo 'display:none;';
    echo '}';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-mobile,';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-image-mobile {';
    echo 'display:block;';
    echo '}';
    echo '}';
    // 手机版，电脑版上传不同的图片
    echo '@media (min-width: 768px) {';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image,';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-image {';
    echo 'display:block;';
    echo '}';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-mobile,';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-image-mobile {';
    echo 'display:none;';
    echo '}';
    echo '}';

    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image img,';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-mobile img,';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-image img,';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-image-mobile img {';
    echo 'width: 100%;';
    echo '}';

    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image img,';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-image img {';
    echo 'min-width: 1024px;';
    echo '}';

    // 未上传图像时的占位符
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image .no-image,';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-mobile .no-image,';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-image .no-image,';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-image-mobile .no-image {';
    echo 'width: 100%;';
    echo 'height: 400px;';
    echo 'line-height: 400px;';
    echo 'color: #fff;';
    echo 'font-size: 24px;';
    echo 'text-align: center;';
    echo 'text-shadow:  5px 5px 5px #999;';
    echo 'background-color: rgba(35, 35, 35, 0.2);';
    echo '}';


    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-container {';
    echo 'position: relative;';
    echo 'overflow: hidden;';
    echo '}';

    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-content-container {';
    echo 'position: absolute;';
    echo 'padding-left: 0.75rem;';
    echo 'padding-right: 0.75rem;';
    echo 'width: 100%;';
    echo 'z-index; 2;';
    echo 'top: 0;';
    echo 'bottom: 0;';
    echo '}';
    echo '@media (max-width: 768px) {';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-content-container {';
    echo '}';
    echo '}';
    echo '@media (min-width: 768px) {';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-content-container {';
    echo 'max-width: 720px;';
    echo 'left: calc((100% - 720px) / 2);';
    echo '}';
    echo '}';
    echo '@media (min-width: 992px) {';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-content-container {';
    echo 'max-width: 960px;';
    echo 'left: calc((100% - 960px) / 2);';
    echo '}';
    echo '}';
    echo '@media (min-width: 1200px) {';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-content-container {';
    echo 'max-width: 1140px;';
    echo 'left: calc((100% - 1140px) / 2);';
    echo '}';
    echo '}';
    echo '@media (min-width: 1400px) {';
    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-content-container {';
    echo 'max-width: 1320px;';
    echo 'left: calc((100% - 1320px) / 2);';
    echo '}';
    echo '}';

    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-content {';
    echo 'position: absolute;';
    echo '}';

    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-title {';
    echo 'text-align: center;';
    echo 'margin-bottom: 10px;';
    echo '}';

    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-description {';
    echo 'text-align: center;';
    echo 'margin-bottom: 35px;';
    echo '}';

    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-button {';
    echo 'text-align: center;';
    echo '}';

    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-button .be-btn {';
    echo 'background-color: transparent;';
    echo '}';

    echo '#slider-' . $sectionType . '-' . $sectionKey . ' .slider-image-with-text-overlay-button .be-btn:hover {';
    echo 'color: #333 !important;';
    echo '}';
    ?>
</style>

<div class="swiper-container" id="slider-<?php echo $sectionType . '-' . $sectionKey; ?>">
    <div class="swiper-wrapper">
        <?php
        if (isset($sectionData['items']) && is_array($sectionData['items']) && count($sectionData['items']) > 0) {
            foreach ($sectionData['items'] as $item) {
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
                                echo '<img src="';
                                if (strpos($item['data']['image'], '/') === false) {
                                    echo \Be\Be::getRequest()->getUploadUrl() . '/Theme/Sample/Section/Slider/image/' . $item['data']['image'];
                                } else {
                                    echo $item['data']['image'];
                                }
                                echo '" />';
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
                                echo '<img src="';
                                if (strpos($item['data']['imageMobile'], '/') === false) {
                                    echo \Be\Be::getRequest()->getUploadUrl() . '/Theme/Sample/Section/Slider/imageMobile/' . $item['data']['imageMobile'];
                                } else {
                                    echo $item['data']['imageMobile'];
                                }
                                echo '" />';
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
                                echo '<img src="';
                                if (strpos($item['data']['image'], '/') === false) {
                                    echo \Be\Be::getRequest()->getUploadUrl() . '/Theme/Sample/Section/Slider/ImageWithTextOverlay/image/' . $item['data']['image'];
                                } else {
                                    echo $item['data']['image'];
                                }
                                echo '">';
                            }
                            echo '</div>';
                            echo '<div class="slider-image-with-text-overlay-image-mobile">';
                            if (!$item['data']['imageMobile']) {
                                echo '<div class="no-image">720X400px+</div>';
                            } else {
                                echo '<img src="';
                                if (strpos($item['data']['imageMobile'], '/') === false) {
                                    echo \Be\Be::getRequest()->getUploadUrl() . '/Theme/Sample/Section/Slider/ImageWithTextOverlay/imageMobile/' . $item['data']['imageMobile'];
                                } else {
                                    echo $item['data']['imageMobile'];
                                }
                                echo '">';
                            }
                            echo '</div>';

                            echo '<div class="slider-image-with-text-overlay-content-container">';
                            echo '<div class="slider-image-with-text-overlay-content" style="';
                            echo 'width: ' . $item['data']['contentWidth'] . 'px;';
                            if ($item['data']['contentPosition'] == 'custom') {
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
                                if ($item['data']['contentPosition'] == 'left') {
                                    echo 'left: 5%;';
                                } elseif ($item['data']['contentPosition'] == 'center') {
                                    echo 'left: 50%;';
                                    echo 'transform: translateX(-50%);';
                                } elseif ($item['data']['contentPosition'] == 'right') {
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
    if ($sectionData['pagination'] && count($sectionData['items']) > 1) {
        ?>
        <div class="swiper-pagination"></div>
        <?php
    }

    if ($sectionData['navigation'] && count($sectionData['items']) > 1) {
        ?>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
        <?php
    }
    ?>
</div>
<script>
    $(document).ready(function(){
        new Swiper('#slider-<?php echo $sectionType . '-' . $sectionKey; ?>', {
            <?php
            if (count($sectionData['items']) > 1) {
                if ($sectionData['autoplay']) {
                    echo 'autoplay: true,';
                    echo 'delay: ' . $sectionData['delay'] . ',';
                    echo 'speed: ' . $sectionData['speed'] . ',';
                }

                if ($sectionData['loop']) {
                    echo 'loop: true,';
                }

                if ($sectionData['pagination']) {
                    echo 'pagination: {el: \'.swiper-pagination\'},';
                }

                if ($sectionData['navigation']) {
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
<?php } ?>