<?php
if ($sectionData['enable']) {

    echo '<style type="text/css">';

    echo '#images-' . $sectionType . '-' . $sectionKey . ' {';
    echo 'background-color: ' . $sectionData['backgroundColor'] . ';';
    echo '}';

    // 手机端
    echo '@media (max-width: 768px) {';
    echo '#images-' . $sectionType . '-' . $sectionKey . ' {';
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
    echo '#images-' . $sectionType . '-' . $sectionKey . ' {';
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
    echo '#images-' . $sectionType . '-' . $sectionKey . ' {';
    if ($sectionData['paddingTopDesktop']) {
        echo 'padding-top: ' . $sectionData['paddingTopDesktop'] . 'px;';
    }
    if ($sectionData['paddingBottomDesktop']) {
        echo 'padding-bottom: ' . $sectionData['paddingBottomDesktop'] . 'px;';
    }
    echo '}';
    echo '}';

    echo '#images-' . $sectionType . '-' . $sectionKey . ' img,';
    echo '#images-' . $sectionType . '-' . $sectionKey . ' a img {';
    echo 'width: 100%;';
    echo 'transition: all 0.7s ease;';
    echo '}';

    if ($sectionData['hoverEffect'] !== 'none') {
        switch ($sectionData['hoverEffect']) {
            case 'scale':
                echo '#images-' . $sectionType . '-' . $sectionKey . ' a:hover img {';
                echo 'transform: scale(1.1);';
                echo '}';
                break;
            case 'rotateScale':
                echo '#images-' . $sectionType . '-' . $sectionKey . ' a:hover img {';
                echo 'transform: rotate(3deg) scale(1.1);';
                echo '}';
                break;
        }
    }

    echo '#images-' . $sectionType . '-' . $sectionKey . ' .images-items {';
    echo 'display: flex;';
    echo 'flex-wrap: wrap;';
    echo 'justify-content: space-between;';
    echo '}';

    // 手机端
    if ($sectionData['spacingMobile']) {
        echo '@media (max-width: 768px) {';
        echo '#images-' . $sectionType . '-' . $sectionKey . ' .images-items {';
        echo 'margin-bottom: -' . $sectionData['spacingMobile'] . 'px;';
        echo 'overflow: hidden;';
        echo '}';
        echo '}';
    }

    // 平析端
    if ($sectionData['spacingTablet']) {
        echo '@media (min-width: 768px) {';
        echo '#images-' . $sectionType . '-' . $sectionKey . ' .images-items {';
        echo 'margin-bottom: -' . $sectionData['spacingTablet'] . 'px;';
        echo 'overflow: hidden;';
        echo '}';
        echo '}';
    }

    // 电脑端
    if ($sectionData['spacingDesktop']) {
        echo '@media (min-width: 992px) {';
        echo '#images-' . $sectionType . '-' . $sectionKey . ' .images-items {';
        echo 'margin-bottom: -' . $sectionData['spacingDesktop'] . 'px;';
        echo 'overflow: hidden;';
        echo '}';
        echo '}';
    }

    $counter = 0;
    foreach ($sectionData['items'] as $item) {
        if ($item['data']['enable']) {
            $counter++;
        }
    }
    $cols = $counter > 3 ? 3 : $counter;

    echo '#images-' . $sectionType . '-' . $sectionKey . ' .images-item {';
    echo 'flex: 0 1 auto;';
    echo 'overflow: hidden;';
    echo '}';

    // 手机端
    echo '@media (max-width: 768px) {';
    echo '#images-' . $sectionType . '-' . $sectionKey . ' .images-item {';
    $width = $cols === 1 ? '100%;' : ('calc((100% - ' . $sectionData['spacingMobile'] . 'px)/2)');
    echo 'width: ' . $width . ';';
    if ($sectionData['spacingMobile']) {
        echo 'margin-bottom: ' . $sectionData['spacingMobile'] . 'px;';
    }
    echo '}';
    echo '}';

    // 手机端小于 512px 时, 100% 宽度
    echo '@media (max-width: 512px) {';
    echo '#images-' . $sectionType . '-' . $sectionKey . ' .images-item {';
    echo 'width: 100% !important;';
    echo '}';
    echo '}';

    // 平析端
    echo '@media (min-width: 768px) {';
    echo '#images-' . $sectionType . '-' . $sectionKey . ' .images-item {';
    $width = $cols === 1 ? '100%;' : ('calc((100% - ' . ($sectionData['spacingTablet'] * ($cols - 1)) . 'px)/' . $cols . ')');
    echo 'width: ' . $width . ';';
    if ($sectionData['spacingTablet']) {
        echo 'margin-bottom: ' . $sectionData['spacingTablet'] . 'px;';
    }
    echo '}';
    echo '}';

    // 电脑端
    echo '@media (min-width: 992px) {';
    echo '#images-' . $sectionType . '-' . $sectionKey . ' .images-item {';
    $width = $cols === 1 ? '100%;' : ('calc((100% - ' . ($sectionData['spacingDesktop'] * ($cols - 1)) . 'px)/' . $cols . ')');
    echo 'width: ' . $width . ';';
    if ($sectionData['spacingDesktop']) {
        echo 'margin-bottom: ' . $sectionData['spacingDesktop'] . 'px;';
    }
    echo '}';
    echo '}';


    echo '#images-' . $sectionType . '-' . $sectionKey . ' .images-item .no-image {';
    echo 'width: 100%;';
    echo 'height: 200px;';
    echo 'line-height: 200px;';
    echo 'color: #fff;';
    echo 'font-size: 24px;';
    echo 'text-align: center;';
    echo 'text-shadow:  5px 5px 5px #999;';
    echo 'background-color: rgba(35, 35, 35, 0.2);';
    echo '}';


    echo '</style>';

    echo '<div id="images-' . $sectionType . '-' . $sectionKey . '">';
    echo '<div class="be-container">';
    if (isset($sectionData['items']) && is_array($sectionData['items']) && count($sectionData['items']) > 0) {
        echo '<div class="images-items">';
        foreach ($sectionData['items'] as $item) {
            if ($item['data']['enable']) {
                echo '<div class="images-item">';
                switch ($item['name']) {
                    case 'Image':
                        if (!$item['data']['image']) {
                            echo '<div class="no-image">400X200px+</div>';
                        } else {
                            if ($item['data']['link']) {
                                echo '<a href="' . $item['data']['link'] . '">';
                            }
                            echo '<img src="';
                            if (strpos($item['data']['image'], '/') === false) {
                                echo \Be\Be::getRequest()->getUploadUrl() . '/Theme/Sample/Section/Images/Image/image/' . $item['data']['image'];
                            } else {
                                echo $item['data']['image'];
                            }
                            echo '" />';

                            if ($item['data']['link']) {
                                echo '</a>';
                            }
                        }
                        break;
                }
                echo '</div>';
            }
        }
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';
}
