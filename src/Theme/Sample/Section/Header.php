<?php if ($sectionData['enable']) { ?>
<style type="text/css">
    <?php
    echo '#header-' . $sectionType . '-' . $sectionKey . ' {';
    echo 'background-color: ' . $sectionData['backgroundColor'] . ';';
    echo '}';

    // 手机端
    echo '@media (max-width: 768px) {';
    echo '#header-' . $sectionType . '-' . $sectionKey . ' {';
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
    echo '#header-' . $sectionType . '-' . $sectionKey . ' {';
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
    echo '#header-' . $sectionType . '-' . $sectionKey . ' {';
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

<div id="header-<?php echo $sectionType . '-' . $sectionKey; ?>">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="be-container">
            <a class="navbar-brand" href="<?php echo beUrl(); ?>">
                <?php
                if ($sectionData['logoType'] === 'text') {
                    echo $sectionData['logoText'];
                } else {
                    echo '<img src="';
                    if (strpos($sectionData['logoImage'], '/') === false) {
                        echo \Be\Be::getRequest()->getUploadUrl() . '/Theme/Sample/Section/Header/logo/' . $sectionData['logoImage'];
                    } else {
                        echo $sectionData['logoImage'];
                    }
                    echo '"';

                    if ($sectionData['logoImageMaxWidth'] || $sectionData['logoImageMaxHeight']) {
                        echo ' style="';
                        if ($sectionData['logoImageMaxWidth']) {
                            echo 'max-width:' . $sectionData['logoImageMaxWidth'] . ';';
                        }
                        if ($sectionData['logoImageMaxHeight']) {
                            echo 'max-height:' . $sectionData['logoImageMaxHeight'] . ';';
                        }
                        echo '"';
                    }
                    echo '>';
                }
                ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav">
                    <?php
                    $menu = \Be\Be::getMenu();
                    $menuTree = $menu->getMenuTree();
                    if (is_array($menuTree)) {
                        foreach ($menuTree as $tmpMenu) {
                            $url = $tmpMenu->url;
                            if (strpos($url, 'menuId') === false) {
                                if (strpos($url, '?') === false) {
                                    $url .= '?menuId=' . $tmpMenu->id;
                                } else {
                                    $url .= '&menuId=' . $tmpMenu->id;
                                }
                            }

                            $hasSubMenu = false;
                            if (isset($tmpMenu->subMenu) && is_array($tmpMenu->subMenu) && count($tmpMenu->subMenu) > 0) {
                                $hasSubMenu = true;
                            }

                            echo '<li class="nav-item';
                            if ($hasSubMenu) {
                                echo ' dropdown';
                            }
                            echo '">';

                            echo '<a class="nav-link';
                            if (isset($this->menuId) && $this->menuId === $tmpMenu->id) {
                                echo ' active';
                            }
                            if ($hasSubMenu) {
                                echo ' dropdown-toggle';
                            }
                            echo '" href="#">'.$tmpMenu->label.'</a>';

                            if ($hasSubMenu) {
                                echo '<ul class="dropdown-menu">';
                                foreach ($tmpMenu->subMenu as $tmpSubMenu) {
                                    echo '<li><a class="dropdown-item" href="#">'. $tmpSubMenu->label .'</a></li>';
                                }
                                echo '</ul>';
                            }

                            echo '</li>';
                        }
                    }
                    ?>
                </ul>

            </div>
        </div>
    </nav>
</div>
<?php } ?>