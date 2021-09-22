<?php
if ($sectionData['enable']) {

    echo '<style type="text/css">';

    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' {';
    echo 'background-color: ' . $sectionData['backgroundColor'] . ';';
    echo '}';

    // 手机端
    echo '@media (max-width: 768px) {';
    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' {';
    echo 'padding-top: ' . $sectionData['paddingMobile'] . 'px;';
    echo 'padding-bottom: ' . $sectionData['paddingMobile'] . 'px;';
    echo '}';
    echo '}';

    // 平析端
    echo '@media (min-width: 768px) {';
    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' {';
    echo 'padding-top: ' . $sectionData['paddingTablet'] . 'px;';
    echo 'padding-bottom: ' . $sectionData['paddingTablet'] . 'px;';
    echo '}';
    echo '}';

    // 电脑端
    echo '@media (min-width: 992px) {';
    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' {';
    echo 'padding-top: ' . $sectionData['paddingDesktop'] . 'px;';
    echo 'padding-bottom: ' . $sectionData['paddingDesktop'] . 'px;';
    echo '}';
    echo '}';

    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-items {';
    echo 'display: flex;';
    echo 'flex-wrap: wrap;';
    echo 'justify-content: space-between;';
    echo '}';

    // 手机端
    echo '@media (max-width: 768px) {';
    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-items {';
    if ($sectionData['spacingMobile']) {
        echo 'margin-bottom: -' . $sectionData['spacingMobile'] . 'px;';
        echo 'overflow: hidden;';
    }
    echo '}';
    echo '}';

    // 平析端
    echo '@media (min-width: 768px) {';
    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-items {';
    if ($sectionData['spacingTablet']) {
        echo 'margin-bottom: -' . $sectionData['spacingTablet'] . 'px;';
        echo 'overflow: hidden;';
    }
    echo '}';
    echo '}';

    // 电脑端
    echo '@media (min-width: 992px) {';
    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-items {';
    if ($sectionData['spacingDesktop']) {
        echo 'margin-bottom: -' . $sectionData['spacingDesktop'] . 'px;';
        echo 'overflow: hidden;';
    }
    echo '}';
    echo '}';

    $counter = 0;
    foreach ($sectionData['items'] as $item) {
        if ($item['data']['enable']) {
            $counter++;
        }
    }
    $cols = $counter > 4 ? 4 : $counter;

    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-item {';
    echo 'background-color: ' . $sectionData['itemBackgroundColor'] . ';';
    echo 'height: 86px;';
    echo 'line-height: 86px;';
    echo 'text-align: center;';
    echo 'flex: 0 1 auto;';
    echo 'overflow: hidden;';
    echo '}';

    // 手机端
    echo '@media (max-width: 768px) {';
    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-item {';
    if ($cols >= 2) {
        $width = 'calc((100% - ' . $sectionData['spacingMobile'] . 'px)/2)';
        echo 'width: ' . $width . ';';
    } else {
        echo 'width:100%;';
    }
    if ($sectionData['spacingMobile']) {
        echo 'margin-bottom: ' . $sectionData['spacingMobile'] . 'px;';
    }
    echo '}';
    echo '}';

    // 手机端小于 512px 时, 100% 宽度
    echo '@media (max-width: 512px) {';
    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-item {';
    echo 'width: 100% !important;';
    echo '}';
    echo '}';

    // 平析端
    echo '@media (min-width: 768px) {';
    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-item {';
    $width = $cols == 1 ? '100%;' : ('calc((100% - ' . ($sectionData['spacingTablet'] * 1) . 'px)/2)');
    echo 'width: ' . $width . ';';
    if ($sectionData['spacingTablet']) {
        echo 'margin-bottom: ' . $sectionData['spacingTablet'] . 'px;';
    }
    echo '}';
    echo '}';

    // 电脑端
    echo '@media (min-width: 992px) {';
    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-item {';
    $width = $cols == 1 ? '100%;' : ('calc((100% - ' . ($sectionData['spacingDesktop'] * ($cols - 1)) . 'px)/' . $cols . ')');
    echo 'width: ' . $width . ';';
    if ($sectionData['spacingDesktop']) {
        echo 'margin-bottom: ' . $sectionData['spacingDesktop'] . 'px;';
    }
    echo '}';
    echo '}';


    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-item-container {';
    echo 'display: inline-block;';
    echo '}';

    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-item-icon {';
    echo 'display: inline-block;';
    echo 'vertical-align: middle;';
    echo 'margin-right: 14px;';
    echo '}';

    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-item-icon i {';
    echo 'font-size: 30px;';
    echo '}';

    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-item-icon svg,';
    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-item-icon img {';
    echo 'width: 30px;';
    echo 'height: 30px;';
    echo '}';

    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-item-content {';
    echo 'display: inline-block;';
    echo 'text-align: left;';
    echo 'vertical-align: middle;';
    echo '}';

    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-item-title {';
    echo 'font-size: 14px;';
    echo 'line-height: 16px;';
    echo '}';

    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-item-link {';
    echo 'font-size: 14px;';
    echo 'line-height: 16px;';
    echo 'margin-top: 4px;';
    echo 'color: #666666;';
    echo '}';

    echo '#group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . ' .group-of-icon-with-text-item-link a {';
    echo 'color: #666666;';
    echo '}';

    echo '</style>';

    echo '<div id="group-of-icon-with-text-' . $sectionType . '-' . $sectionKey . '">';
    echo '<div class="be-container">';
    if (isset($sectionData['items']) && is_array($sectionData['items']) && count($sectionData['items']) > 0) {
        echo '<div class="group-of-icon-with-text-items">';
        foreach ($sectionData['items'] as $item) {
            if ($item['data']['enable']) {
                echo '<div class="group-of-icon-with-text-item">';
                switch ($item['name']) {
                    case 'IconWithText':
                        echo '<div class="group-of-icon-with-text-item-container">';

                        echo '<div class="group-of-icon-with-text-item-icon">';
                        switch ($item['data']['icon']) {
                            case 'name':
                                echo '<i class="' . $item['data']['iconName'] . '"></i>';
                                break;
                            case 'svg':
                                echo $item['data']['iconSvg'];
                                break;
                            case 'image':
                                echo '<img src="';
                                if (strpos($item['data']['iconImage'], '/') === false) {
                                    echo \Be\Be::getRequest()->getUploadUrl() . '/Theme/Sample/Section/GroupOfIconWithText/IconWithText/iconImage/' . $item['data']['iconImage'];
                                } else {
                                    echo $item['data']['iconImage'];
                                }
                                echo '" />';
                                break;

                        }
                        echo '</div>';

                        echo '<div class="group-of-icon-with-text-item-content">';
                        if ($item['data']['title']) {
                            echo '<div class="group-of-icon-with-text-item-title">';
                            echo $item['data']['title'];
                            echo '</div>';
                        }
                        if ($item['data']['linkText']) {
                            echo '<div class="group-of-icon-with-text-item-link">';
                            if ($item['data']['linkUrl']) {
                                echo '<a href="' . $item['data']['linkUrl'] . '">';
                            }
                            echo $item['data']['linkText'];
                            if ($item['data']['linkUrl']) {
                                echo '</a>';
                            }
                            echo '</div>';
                        }
                        echo '</div>';

                        echo '</div>';

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
