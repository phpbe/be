<?php if ($sectionData['enable']) { ?>
<style type="text/css">
    <?php
    if ($sectionData['marginTop']) {
        echo '#footer-' . $sectionType . '-' . $sectionKey . ' {margin-top: ' . $sectionData['marginTop'] . 'px;}';
    }
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