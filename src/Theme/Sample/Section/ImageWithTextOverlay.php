<?php
if ($sectionData['enable']) {

    echo '<style type="text/css">';

    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' {';
    echo 'background-color: ' . $sectionData['backgroundColor'] . ';';
    echo '}';

    // 手机端
    echo '@media (max-width: 768px) {';
    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' {';
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
    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' {';
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
    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' {';
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
    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-image {';
    echo 'display:none;';
    echo '}';
    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-image-mobile {';
    echo 'display:block;';
    echo '}';
    echo '}';
    // 手机版，电脑版上传不同的图片
    echo '@media (min-width: 768px) {';
    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-image {';
    echo 'display:block;';
    echo '}';
    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-image-mobile {';
    echo 'display:none;';
    echo '}';
    echo '}';

    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-image img {';
    echo 'width: 100%;';
    echo 'min-width: 1024px;';
    echo '}';

    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-image-mobile img {';
    echo 'width: 100%;';
    echo '}';

    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-image .no-image,';
    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-image-mobile .no-image {';
    echo 'width: 100%;';
    echo 'height: 400px;';
    echo 'line-height: 400px;';
    echo 'color: #fff;';
    echo 'font-size: 24px;';
    echo 'text-align: center;';
    echo 'text-shadow:  5px 5px 5px #999;';
    echo 'background-color: rgba(35, 35, 35, 0.2);';
    echo '}';

    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-container {';
    echo 'position: relative;';
    echo 'overflow: hidden;';
    echo '}';

    if ($sectionData['width'] == 'fullWidth') {
        echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-content-container {';
        echo 'position: absolute;';
        echo 'padding-left: 0.75rem;';
        echo 'padding-right: 0.75rem;';
        echo 'width: 100%;';
        echo 'z-index; 2;';
        echo 'top: 0;';
        echo 'bottom: 0;';
        echo '}';
        echo '@media (max-width: 768px) {';
        echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-content-container {';
        echo '}';
        echo '}';
        echo '@media (min-width: 768px) {';
        echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-content-container {';
        echo 'max-width: 720px;';
        echo 'left: calc((100% - 720px) / 2);';
        echo '}';
        echo '}';
        echo '@media (min-width: 992px) {';
        echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-content-container {';
        echo 'max-width: 960px;';
        echo 'left: calc((100% - 960px) / 2);';
        echo '}';
        echo '}';
        echo '@media (min-width: 1200px) {';
        echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-content-container {';
        echo 'max-width: 1140px;';
        echo 'left: calc((100% - 1140px) / 2);';
        echo '}';
        echo '}';
        echo '@media (min-width: 1400px) {';
        echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-content-container {';
        echo 'max-width: 1320px;';
        echo 'left: calc((100% - 1320px) / 2);';
        echo '}';
        echo '}';
    }

    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-content {';
    echo 'position: absolute;';
    echo 'max-width: ' . $sectionData['contentWidth'] . 'px;';
    echo '}';

    // 手机端 默认居中
    echo '@media only screen and (max-width: 768px) {';
    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-content {';
    echo 'width: 80%;';
    echo 'left: 50%;';
    echo 'top: 50%;';
    echo 'transform: translate(-50%, -50%);';
    echo '}';
    echo '}';

    // 电脑端
    echo '@media only screen and (min-width: 769px) {';
    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-content {';
    echo 'width: ' . $sectionData['contentWidth'] . 'px;';
    if ($sectionData['contentPosition'] == 'custom') {
        if ($sectionData['contentPositionLeft'] >= 0) {
            echo 'left: ' . $sectionData['contentPositionLeft'] . 'px;';
        }
        if ($sectionData['contentPositionRight'] >= 0) {
            echo 'right: ' . $sectionData['contentPositionRight'] . 'px;';
        }
        if ($sectionData['contentPositionTop'] >= 0) {
            echo 'top: ' . $sectionData['contentPositionTop'] . 'px;';
        }
        if ($sectionData['contentPositionBottom'] >= 0) {
            echo 'bottom: ' . $sectionData['contentPositionBottom'] . 'px;';
        }
    } else {
        echo 'top: 50%;';
        echo 'transform: translateY(-50%);';
        if ($sectionData['contentPosition'] == 'left') {
            echo 'left: 5%;';
        } elseif ($sectionData['contentPosition'] == 'center') {
            echo 'left: 50%;';
            echo 'transform: translateX(-50%);';
        } elseif ($sectionData['contentPosition'] == 'right') {
            echo 'right: 5%;';
        }
    }
    echo '}';
    echo '}';

    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-title {';
    echo 'text-align: center;';
    echo 'font-size: ' . $sectionData['contentTitleFontSize'] . 'px;';
    echo 'color: ' . $sectionData['contentTitleColor'] . ';';
    echo '}';

    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-description {';
    echo 'text-align: center;';
    echo 'font-size: ' . $sectionData['contentDescriptionFontSize'] . 'px;';
    echo 'color: ' . $sectionData['contentDescriptionColor'] . ';';
    echo 'margin-bottom: 35px;';
    echo '}';

    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-button {';
    echo 'text-align: center;';
    echo '}';

    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-button .be-btn {';
    echo 'background-color: transparent;';
    echo 'color: ' . $sectionData['contentButtonColor'] . ';';
    echo 'border-color: ' . $sectionData['contentButtonColor'] . ';';
    echo '}';

    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-button .be-btn:hover {';
    echo 'background-color: ' . $sectionData['contentButtonColor'] . ';';
    echo 'color: #333;';
    echo '}';

    echo '</style>';

    echo '<div id="image-with-text-overlay-' . $sectionType . '-' . $sectionKey . '">';
    if ($sectionData['width'] == 'default') {
        echo '<div class="be-container">';
    }
    echo '<div class="image-with-text-overlay-container">';

    echo '<div class="image-with-text-overlay-image">';
    if (!$sectionData['image']) {
        echo '<div class="no-image">1200X400px+</div>';
    } else {
        echo '<img src="';
        if (strpos($sectionData['image'], '/') === false) {
            echo \Be\Be::getRequest()->getUploadUrl() . '/Theme/Sample/Section/ImageWithTextOverlay/image/' . $sectionData['image'];
        } else {
            echo $sectionData['image'];
        }
        echo '">';
    }
    echo '</div>';
    echo '<div class="image-with-text-overlay-image-mobile">';
    if (!$sectionData['imageMobile']) {
        echo '<div class="no-image">720X400px+</div>';
    } else {
        echo '<img src="';
        if (strpos($sectionData['imageMobile'], '/') === false) {
            echo \Be\Be::getRequest()->getUploadUrl() . '/Theme/Sample/Section/ImageWithTextOverlay/imageMobile/' . $sectionData['imageMobile'];
        } else {
            echo $sectionData['imageMobile'];
        }
        echo '">';
    }
    echo '</div>';

    if ($sectionData['width'] == 'fullWidth') {
        echo '<div class="image-with-text-overlay-content-container">';
    }
    echo '<div class="image-with-text-overlay-content">';
    echo '<h2 class="image-with-text-overlay-title">' . $sectionData['contentTitle'] . '</h2>';
    echo '<div class="image-with-text-overlay-description">' . $sectionData['contentDescription'] . '</div>';
    echo '<div class="image-with-text-overlay-button">';
    echo '<a href="' . $sectionData['contentButtonLink'] . '" class="be-btn be-btn-large">' . $sectionData['contentButton'] . '</a>';
    echo '</div>';
    echo '</div>';
    if ($sectionData['width'] == 'fullWidth') {
        echo '</div>';
    }

    echo '</div>';
    if ($sectionData['width'] == 'default') {
        echo '</div>';
    }
    echo '</div>';
}
