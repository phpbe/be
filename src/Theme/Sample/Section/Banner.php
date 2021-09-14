<?php
if ($sectionData['enable']) {

    $configTheme = \Be\Be::getConfig('Theme.Sample.Theme');

    echo '<style type="text/css">';

    echo '#banner-' . $sectionType . '-' . $sectionKey . ' {';
    echo 'background-color: ' . $sectionData['backgroundColor'] . ';';
    if ($sectionData['paddingTop']) {
        echo 'padding-top: ' . $sectionData['paddingTop'] . 'px;';
    }
    if ($sectionData['paddingBottom']) {
        echo 'padding-bottom: ' . $sectionData['paddingBottom'] . 'px;';
    }
    echo '}';

    echo '#banner-' . $sectionType . '-' . $sectionKey . ' a img {';
    echo 'transition: all 0.7s ease;';
    echo '}';

    if ($sectionData['hoverEffect'] != 'none') {
        switch ($sectionData['hoverEffect']) {
            case 'scale':
                echo '#banner-' . $sectionType . '-' . $sectionKey . ' a:hover img {';
                echo 'transform: scale(1.1);';
                echo '}';
                break;
            case 'rotateScale':
                echo '#banner-' . $sectionType . '-' . $sectionKey . ' a:hover img {';
                echo 'transform: rotate(3deg) scale(1.1);';
                echo '}';
                break;
        }
    }

    echo '#banner-' . $sectionType . '-' . $sectionKey . ' .banner-items {';
    echo 'display: flex;';
    echo 'flex-wrap: wrap;';
    echo 'justify-content: space-between;';
    if ($sectionData['spacing']) {
        echo 'margin-bottom: -' . $sectionData['spacing'] . 'px;';
        echo 'overflow: hidden;';
    }
    echo '}';

    $counter = 0;
    foreach ($sectionData['items'] as $item) {
        if ($item['data']['enable']) {
            $counter++;
        }
    }
    $cols = $counter == 1 ? 1 : ($counter == 2 ? 2 : 3);
    $width = $cols == 1 ? '100%;' : ('calc((100% - ' . ($sectionData['spacing'] * ($cols - 1)) . 'px)/' . $cols . ')');

    echo '#banner-' . $sectionType . '-' . $sectionKey . ' .banner-item {';
    echo 'flex: 0 1 auto;';
    echo 'width: ' . $width . ';';
    echo 'overflow: hidden;';
    echo 'margin-bottom: ' . $sectionData['spacing'] . 'px;';
    echo '}';

    if ($cols == 3) {
        $width = 'calc((100% - ' . $sectionData['spacing'] . 'px)/2)';
        echo '@media only screen and (max-width: 768px) {';
        echo '#banner-' . $sectionType . '-' . $sectionKey . ' .banner-item {';
        echo 'width: ' . $width . ' !important;';
        echo '}';
        echo '}';
    }
    echo '</style>';

    echo '<div id="banner-' . $sectionType . '-' . $sectionKey . '">';
    echo '<div class="container-md">';
    if (isset($sectionData['items']) && is_array($sectionData['items']) && count($sectionData['items']) > 0) {
        echo '<div class="banner-items">';
        foreach ($sectionData['items'] as $item) {
            if ($item['data']['enable']) {
                echo '<div class="banner-item">';
                switch ($item['name']) {
                    case 'Image':
                        if ($item['data']['link']) {
                            echo '<a href="' . $item['data']['link'] . '">';
                        }

                        echo '<img src="';
                        if (strpos($item['data']['image'], '/') === false) {
                            echo \Be\Be::getRequest()->getUploadUrl() . '/Theme/Sample/Section/Banner/Image/' . $item['data']['image'];
                        } else {
                            echo $item['data']['image'];
                        }
                        echo '" class="img-fluid" />';

                        if ($item['data']['link']) {
                            echo '</a>';
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
