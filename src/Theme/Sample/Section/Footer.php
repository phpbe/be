<?php if ($sectionData['enable']) { ?>
<style type="text/css">
    <?php
    echo '#footer-' . $sectionType . '-' . $sectionKey . ' {';
    echo 'background-color: ' . $sectionData['backgroundColor'] . ';';
    if ($sectionData['paddingTop']) {
        echo 'padding-top: ' . $sectionData['paddingTop'] . 'px;';
    }
    if ($sectionData['paddingBottom']) {
        echo 'padding-bottom: ' . $sectionData['paddingBottom'] . 'px;';
    }
    echo '}';
    ?>
</style>

<div id="footer-<?php echo $sectionType . '-' . $sectionKey; ?>">
    <div class="text-center">
        <?php
        if (isset($sectionData['copyright'])) {
            echo $sectionData['copyright'];
        }
        ?>
    </div>
</div>
<?php } ?>