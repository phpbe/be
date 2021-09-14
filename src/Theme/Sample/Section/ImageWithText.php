<?php
if ($sectionData['enable']) {

    echo '<style type="text/css">';

    echo '#image-with-text-' . $sectionType . '-' . $sectionKey . ' {';
    echo 'background-color: ' . $sectionData['backgroundColor'] . ';';
    echo '}';

    echo '#image-with-text-' . $sectionType . '-' . $sectionKey . ' .image-with-text-content {';
    echo 'background-color: ' . $sectionData['contentBackgroundColor'] . ';';
    echo '}';

    // 手机端 图像 和 内容 均 100%
    echo '@media only screen and (max-width: 768px) {';
    echo '#image-with-text-' . $sectionType . '-' . $sectionKey . ' {';
    echo '}';
    echo '#image-with-text-' . $sectionType . '-' . $sectionKey . ' .image-with-text-iamge {';
    echo 'width: 100%;';
    echo '}';
    echo '#image-with-text-' . $sectionType . '-' . $sectionKey . ' .image-with-text-content {';
    echo 'width: 100%;';
    echo '}';
    echo '#image-with-text-' . $sectionType . '-' . $sectionKey . ' .image-with-text-content-wrap {';
    echo 'padding: 25px 15px 30px;';
    echo '}';
    echo '}';

    // 电脑版，图像 50%, 内容 50%
    echo '@media only screen and (min-width: 769px) {';
    echo '#image-with-text-' . $sectionType . '-' . $sectionKey . ' .image-with-text-container {';
    echo 'display: flex;';
    if ($sectionData['imagePosition'] == 'right') { // 图像居右
        echo 'flex-direction: row-reverse !important;';
    }
    echo '}';
    echo '#image-with-text-' . $sectionType . '-' . $sectionKey . ' .image-with-text-image {';
    echo 'flex: 0 0 50%;';
    echo '}';
    echo '#image-with-text-' . $sectionType . '-' . $sectionKey . ' .image-with-text-content {';
    echo 'flex: 0 0 50%;';
    echo 'position: relative;';
    echo '}';
    echo '#image-with-text-' . $sectionType . '-' . $sectionKey . ' .image-with-text-content-wrap {';
    echo 'position: absolute;';
    echo 'top: 50%;';
    echo 'transform: translateY(-50%);';
    echo 'width: 80%;';
    echo 'left: 10%;';
    echo '}';
    echo '}';

    echo '#image-with-text-' . $sectionType . '-' . $sectionKey . ' .image-with-text-image img {';
    echo 'width: 100%;';
    echo '}';

    echo '#image-with-text-' . $sectionType . '-' . $sectionKey . ' .image-with-text-image .no-image {';
    echo 'width: 100%;';
    echo 'height: 300px;';
    echo 'line-height: 300px;';
    echo 'color: #fff;';
    echo 'font-size: 24px;';
    echo 'text-align: center;';
    echo 'text-shadow:  5px 5px 5px #999;';
    echo 'background-color: rgba(35, 35, 35, 0.2);';
    echo '}';

    echo '#image-with-text-' . $sectionType . '-' . $sectionKey . ' .image-with-text-title {';
    echo 'text-align: center;';
    echo 'font-size: ' . $sectionData['contentTitleFontSize'] . 'px;';
    echo 'color: ' . $sectionData['contentTitleColor'] . ';';
    echo '}';

    echo '#image-with-text-' . $sectionType . '-' . $sectionKey . ' .image-with-text-description {';
    echo 'text-align: center;';
    echo 'font-size: ' . $sectionData['contentDescriptionFontSize'] . 'px;';
    echo 'color: ' . $sectionData['contentDescriptionColor'] . ';';
    echo 'margin-bottom: 35px;';
    echo '}';

    echo '#image-with-text-' . $sectionType . '-' . $sectionKey . ' .image-with-text-button {';
    echo 'text-align: center;';
    echo '}';

    echo '</style>';

    echo '<div id="image-with-text-' . $sectionType . '-' . $sectionKey . '">';

    if ($sectionData['width'] == 'default') {
        echo '<div class="container-md">';
    }

    echo '<div class="image-with-text-container">';
    echo '<div class="image-with-text-image">';
    if (!$sectionData['image']) {
        echo '<div class="no-image">600X300px+</div>';
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

    echo '<div class="image-with-text-content">';
    echo '<div class="image-with-text-content-wrap">';
    echo '<h2 class="image-with-text-title">' . $sectionData['contentTitle'] . '</h2>';
    echo '<div class="image-with-text-description">' . $sectionData['contentDescription'] . '</div>';
    echo '<div class="image-with-text-button">';
    echo '<a href="' . $sectionData['contentButtonLink'] . '" class="btn btn-primary btn-sm">' . $sectionData['contentButton'] . '</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    if ($sectionData['width'] == 'default') {
        echo '</div>';
    }
    echo '</div>';
}
