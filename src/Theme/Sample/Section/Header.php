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
                    $menu = \Be\Be::getMenu('North');
                    $menuTree = $menu->getTree();
                    if (is_array($menuTree)) {
                        foreach ($menuTree as $item) {

                            $hasSubItem = false;
                            if (isset($item->subItems) && is_array($item->subItems) && count($item->subItems) > 0) {
                                $hasSubItem = true;
                            }

                            echo '<li class="nav-item';
                            if ($hasSubItem) {
                                echo ' dropdown';
                            }
                            echo '">';

                            echo '<a class="nav-link';
                            if ($hasSubItem) {
                                echo ' dropdown-toggle';
                            }

                            $url = 'javascript:void(0);';
                            if ($item->route) {
                                if ($item->params) {
                                    $url = beUrl($item->route, $item->params);
                                } else {
                                    $url = beUrl($item->route);
                                }
                            } else {
                                if ($item->url) {
                                    $url = $item->url;
                                }
                            }

                            echo '" href="'.$url.'"';
                            if ($item->target === '_blank') {
                                echo ' target="_blank"';
                            }
                            echo '>'.$item->label.'</a>';

                            if ($hasSubItem) {
                                echo '<ul class="dropdown-menu">';
                                foreach ($item->subMenu as $subItem) {
                                    $url = 'javascript:void(0);';
                                    if ($subItem->route) {
                                        if ($subItem->params) {
                                            $url = beUrl($subItem->route, $subItem->params);
                                        } else {
                                            $url = beUrl($subItem->route);
                                        }
                                    } else {
                                        if ($subItem->url) {
                                            $url = $subItem->url;
                                        }
                                    }

                                    echo '<li><a class="dropdown-item" href="'.$url.'"';
                                    if ($subItem->target === '_blank') {
                                        echo ' target="_blank"';
                                    }
                                    echo '>'. $subItem->label .'</a></li>';
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