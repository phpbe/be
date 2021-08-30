<?php if ($sectionData['enable']) { ?>
<script src="<?php echo \Be\Be::getProperty('Theme.Sample')->getUrl(); ?>/Template/App/System/Index/js/swiper-bundle.min.js"></script>
<link rel="stylesheet" href="<?php echo \Be\Be::getProperty('Theme.Sample')->getUrl(); ?>/Template/App/System/Index/css/swiper-bundle.min.css">

<style type="text/css">
    <?php
    if ($sectionData['marginTop']) {
        echo '#slider-' . $sectionType . '-' . $sectionKey . ' {margin-top: ' . $sectionData['marginTop'] . 'px;}';
    }

    if ($sectionData['pagination']) {
       echo '#slider-' . $sectionType . '-' . $sectionKey . ' .swiper-pagination-bullet-active {';
       echo 'background-color: ' . $sectionData['paginationColor'] . ';';
       echo '}';
    }

    if ($sectionData['navigation']) {
        echo '#slider-' . $sectionType . '-' . $sectionKey . ' .swiper-button-prev, ';
        echo '#slider-' . $sectionType . '-' . $sectionKey . ' .swiper-button-next {';
        echo 'color: ' . $sectionData['navigationColor'] . ';';
        echo 'width: ' . ($sectionData['navigationSize'] / 44 * 27) . 'px;';
        echo 'height: ' . $sectionData['navigationSize'] . 'px;';
        echo 'margin-top: -' . ($sectionData['navigationSize'] / 2) . 'px;';
        echo '}';

        echo '#slider-' . $sectionType . '-' . $sectionKey . ' .swiper-button-prev:after, ';
        echo '#slider-' . $sectionType . '-' . $sectionKey . ' .swiper-button-next:after {';
        echo 'font-size: ' . $sectionData['navigationSize'] . ';';
        echo '}';
    }
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
                            if ($item['data']['link']) {
                                echo '<a href="'.$item['data']['link'].'">';
                            }

                            echo '<img src="';
                            if (strpos($item['data']['image'], '/') === false) {
                                echo \Be\Be::getRequest()->getUploadUrl() . '/Theme/Sample/Section/Slider/Image/' . $item['data']['image'];
                            } else {
                                echo $item['data']['image'];
                            }
                            echo '" class="img-fluid" />';

                            if ($item['data']['link']) {
                                echo '</a>';
                            }
                            break;
                        case 'Rich':

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
    if ($sectionData['pagination']) {
        ?>
        <div class="swiper-pagination"></div>
        <?php
    }

    if ($sectionData['navigation']) {
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
            ?>

            grabCursor : true
        });
    });
</script>
<?php } ?>