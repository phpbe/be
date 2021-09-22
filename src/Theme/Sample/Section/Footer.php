<?php if ($sectionData['enable']) { ?>
    <style type="text/css">
        <?php
        echo '#footer-' . $sectionType . '-' . $sectionKey . ' {';
        echo 'background-color: ' . $sectionData['backgroundColor'] . ';';
        echo '}';

        // 手机端
        echo '@media (max-width: 768px) {';
        echo '#footer-' . $sectionType . '-' . $sectionKey . ' {';
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
        echo '#footer-' . $sectionType . '-' . $sectionKey . ' {';
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
        echo '#footer-' . $sectionType . '-' . $sectionKey . ' {';
        if ($sectionData['paddingTopDesktop']) {
            echo 'padding-top: ' . $sectionData['paddingTopDesktop'] . 'px;';
        }
        if ($sectionData['paddingBottomDesktop']) {
            echo 'padding-bottom: ' . $sectionData['paddingBottomDesktop'] . 'px;';
        }
        echo '}';
        echo '}';
        ?>
    </style>

    <div id="footer-<?php echo $sectionType . '-' . $sectionKey; ?>">
        <div class="be-container">
            <div class="text-center">
                <?php
                if (isset($sectionData['copyright'])) {
                    echo $sectionData['copyright'];
                }
                ?>
            </div>
        </div>
    </div>
<?php } ?>