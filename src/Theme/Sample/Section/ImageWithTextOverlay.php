<?php
if ($sectionData['enable']) {

    echo '<style type="text/css">';

    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' {';
    echo 'background-color: ' . $sectionData['backgroundColor'] . ';';
    echo '}';

    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-container {';
    echo 'position: relative;';
    echo '}';

    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-content {';
    echo 'position: absolute;';
    echo 'z-index; 2;';
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

    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-image img {';
    echo 'width: 100%;';
    echo '}';

    echo '#image-with-text-overlay-' . $sectionType . '-' . $sectionKey . ' .image-with-text-overlay-image .no-image {';
    echo 'width: 100%;';
    echo 'height: 400px;';
    echo 'line-height: 400px;';
    echo 'color: #fff;';
    echo 'font-size: 24px;';
    echo 'text-align: center;';
    echo 'text-shadow:  5px 5px 5px #999;';
    echo 'background-color: rgba(35, 35, 35, 0.2);';
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

    echo '</style>';

    echo '<div id="image-with-text-overlay-' . $sectionType . '-' . $sectionKey . '">';
    if ($sectionData['width'] == 'default') {
        echo '<div class="container-md">';
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

    echo '<div class="image-with-text-overlay-content">';
    echo '<h2 class="image-with-text-overlay-title">' . $sectionData['contentTitle'] . '</h2>';
    echo '<div class="image-with-text-overlay-description">' . $sectionData['contentDescription'] . '</div>';
    echo '<div class="image-with-text-overlay-button">';
    echo '<a href="' . $sectionData['contentButtonLink'] . '" class="btn btn-primary btn-sm">' . $sectionData['contentButton'] . '</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    if ($sectionData['width'] == 'default') {
        echo '</div>';
    }
    echo '</div>';
}
