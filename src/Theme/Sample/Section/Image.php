<?php
if ($sectionData['enable']) {

    echo '<style type="text/css">';

    echo '#image-' . $sectionType . '-' . $sectionKey . ' {';
    echo 'background-color: ' . $sectionData['backgroundColor'] . ';';
    echo '}';

    // 手机端
    echo '@media (max-width: 768px) {';
    echo '#image-' . $sectionType . '-' . $sectionKey . ' {';
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
    echo '#image-' . $sectionType . '-' . $sectionKey . ' {';
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
    echo '#image-' . $sectionType . '-' . $sectionKey . ' {';
    if ($sectionData['paddingTopDesktop']) {
        echo 'padding-top: ' . $sectionData['paddingTopDesktop'] . 'px;';
    }
    if ($sectionData['paddingBottomDesktop']) {
        echo 'padding-bottom: ' . $sectionData['paddingBottomDesktop'] . 'px;';
    }
    echo '}';
    echo '}';

    // 手机版，电脑版上传不同的图片
    echo '@media (max-width: 768px) {';
    echo '#image-' . $sectionType . '-' . $sectionKey . ' .image {';
    echo 'display:none;';
    echo '}';
    echo '#image-' . $sectionType . '-' . $sectionKey . ' .image-mobile {';
    echo 'display:block;';
    echo '}';
    echo '}';
    // 手机版，电脑版上传不同的图片
    echo '@media (min-width: 768px) {';
    echo '#image-' . $sectionType . '-' . $sectionKey . ' .image {';
    echo 'display:block;';
    echo '}';
    echo '#image-' . $sectionType . '-' . $sectionKey . ' .image-mobile {';
    echo 'display:none;';
    echo '}';
    echo '}';

    echo '#image-' . $sectionType . '-' . $sectionKey . ' .image img,';
    echo '#image-' . $sectionType . '-' . $sectionKey . ' .image-mobile img {';
    echo 'width: 100%;';
    echo '}';

    echo '#image-' . $sectionType . '-' . $sectionKey . ' .image .no-image,';
    echo '#image-' . $sectionType . '-' . $sectionKey . ' .image-mobile .no-image {';
    echo 'width: 100%;';
    echo 'height: 400px;';
    echo 'line-height: 400px;';
    echo 'color: #fff;';
    echo 'font-size: 24px;';
    echo 'text-align: center;';
    echo 'text-shadow:  5px 5px 5px #999;';
    echo 'background-color: rgba(35, 35, 35, 0.2);';
    echo '}';

    echo '</style>';

    echo '<div id="image-' . $sectionType . '-' . $sectionKey . '">';
    if ($sectionData['width'] === 'default') {
        echo '<div class="be-container">';
    }

    echo '<div class="image">';
    if (!$sectionData['image']) {
        echo '<div class="no-image">1200X400px+</div>';
    } else {
        if ($sectionData['link']) {
            echo '<a href="' . $sectionData['link'] . '">';
        }
        echo '<img src="' . $sectionData['image'] . '">';
        if ($sectionData['link']) {
            echo '</a>';
        }
    }
    echo '</div>';
    echo '<div class="image-mobile">';
    if (!$sectionData['imageMobile']) {
        echo '<div class="no-image">720X400px+</div>';
    } else {
        if ($sectionData['link']) {
            echo '<a href="' . $sectionData['link'] . '">';
        }
        echo '<img src="' . $sectionData['imageMobile'] . '">';
        if ($sectionData['link']) {
            echo '</a>';
        }
    }
    echo '</div>';

    if ($sectionData['width'] === 'default') {
        echo '</div>';
    }
    echo '</div>';
}
