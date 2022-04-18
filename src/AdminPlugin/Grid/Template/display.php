<be-head>
    <style type="text/css">

        .be-center .el-dropdown-link {
            cursor: pointer;
            color: #409eff;
        }

        <?php
        if ($this->setting['actualLayout'] === 'table') {
            ?>
            .el-table__row .el-divider__text,
            .el-table .el-link {
                margin-left: 4px;
                margin-right: 4px;
            }

            .el-table th.el-table__cell {
                color: #666;
                background-color: #EBEEF5;
            }

            .el-table__cell .el-avatar,
            .el-table__cell .el-image {
                display: block;
            }

            .table-action {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                z-index: 9;
                background-color: #EBEEF5;
                display: flex;
                height: 55px;
                align-items: center;
            }

            <?php
        } elseif ($this->setting['actualLayout'] === 'card') {
            ?>
            .el-card__body {
                font-size: 14px;
                line-height: 25px;
            }

            <?php
            if (isset($this->setting['card']['image'])) {
                if ($this->setting['card']['image']['position'] === 'left') {
                    ?>
                    .card-lr {
                        display: flex;
                    }

                    .card-lr-image {
                        flex: 0 0 <?php echo $this->setting['card']['image']['maxWidth']; ?>px;
                        height:<?php echo $this->setting['card']['image']['maxHeight']; ?>px;
                        line-height:<?php echo $this->setting['card']['image']['maxHeight']; ?>px;
                        overflow:hidden;
                    }

                    .card-lr-image a {
                        display: block;
                    }

                    .card-lr-image img {
                        width: 100%;
                    }

                    .card-lr-space {
                        flex: 0 0 <?php echo $this->setting['card']['image']['space']; ?>px;
                    }

                    .card-lr-right {
                        flex: 1 1 auto;
                        position: relative;
                    }

                    .card-lr-right-items {
                        position: absolute;
                        width:100%; top: 0;
                        bottom: 30px;
                        overflow-y: auto;
                    }

                    .card-lr-right-operations {
                        position: absolute;
                        bottom: 0;
                        height: 30px;
                        line-height: 30px;
                        <?php
                        if (isset($this->setting['card']['operation']['position'])) {
                            echo $this->setting['card']['operation']['position'] . ':0;';
                        }
                        ?>
                    }
                    <?php
                } elseif ($this->setting['card']['image']['position'] === 'top') {
                    ?>
                    .card-tb {

                    }

                    .card-tb-image {
                        <?php if (isset($this->setting['card']['image']['maxWidth'])) { ?>
                            max-width: <?php echo $this->setting['card']['image']['maxWidth']; ?>px;;
                        <?php } ?>

                        <?php if (isset($this->setting['card']['image']['maxHeight'])) { ?>
                            height: <?php echo $this->setting['card']['image']['maxHeight']; ?>px;;
                            line-height: <?php echo $this->setting['card']['image']['maxHeight']; ?>px;
                        <?php } ?>

                        <?php if (isset($this->setting['card']['image']['maxWidth']) || isset($this->setting['card']['image']['maxHeight'])) { ?>
                            overflow: hidden;
                        <?php } ?>

                        margin-bottom: <?php echo $this->setting['card']['image']['space']; ?>px;
                    }

                    .card-tb-image a {
                        display: block;
                    }

                    .card-tb-image img {
                        width: 100%;
                    }

                    .card-tb-items {

                    }

                    .card-tb-operations {
                        <?php
                        if (isset($this->setting['card']['operation']['position'])) {
                            echo 'text-align:' . $this->setting['card']['operation']['position'] . ';';
                        }
                        ?>
                    }
                    <?php
                }
            } else {
                ?>
                .card-items {

                }

                .card-operations {
                    <?php
                    if (isset($this->setting['card']['operation']['position'])) {
                        echo 'text-align:' . $this->setting['card']['operation']['position'] . ';';
                    }
                    ?>
                }
                <?php
            }
            ?>

            .card-item {
                min-height: 30px;
                line-height: 30px;
            }

            <?php
        }
        ?>
    </style>
</be-head>


<be-center>
    <div class="be-center">
        <?php
        $js = [];
        $css = [];
        $formData = [];
        $vueData = [];
        $vueMethods = [];
        $vueHooks = [];

        $titleToolbarItems = [];
        $titleRightToolbarItems = [];
        $formToolbarItems = [];
        $tableToolbarItems = [];
        $toolbarItems = [];

        $allToolBarItems = [];
        if (isset($this->setting['titleToolbar']['items']) && count($this->setting['titleToolbar']['items']) > 0) {
            $allToolBarItems['titleToolbar'] = $this->setting['titleToolbar']['items'];
        }

        if (isset($this->setting['titleRightToolbar']['items']) && count($this->setting['titleRightToolbar']['items']) > 0) {
            $allToolBarItems['titleRightToolbar'] = $this->setting['titleRightToolbar']['items'];
        }

        if (isset($this->setting['formToolbar']['items']) && count($this->setting['formToolbar']['items']) > 0) {
            $allToolBarItems['formToolbar'] = $this->setting['formToolbar']['items'];
        }

        if (isset($this->setting['tableToolbar']['items']) && count($this->setting['tableToolbar']['items']) > 0) {
            $allToolBarItems['tableToolbar'] = $this->setting['tableToolbar']['items'];
        }

        if (isset($this->setting['toolbar']['items']) && count($this->setting['toolbar']['items']) > 0) {
            $allToolBarItems['toolbar'] = $this->setting['toolbar']['items'];
        }

        if (count($allToolBarItems) > 0) {
            foreach ($allToolBarItems as $type => $items) {
                foreach ($items as $item) {

                    if ($type === 'tableToolbar') {
                        if (!isset($item['ui']['size'])) {
                            $item['ui']['size'] = 'medium';
                        }
                    }

                    $driverClass = null;
                    if (isset($item['driver'])) {
                        if (substr($item['driver'], 0, 11) === 'ToolbarItem') {
                            $driverClass = '\\Be\\AdminPlugin\\Toolbar\\Item\\' . $item['driver'];
                        } else {
                            $driverClass = $item['driver'];
                        }
                    } else {
                        $driverClass = \Be\AdminPlugin\Toolbar\Item\ToolbarItemButton::class;
                    }
                    $driver = new $driverClass($item);

                    $toolbar = [
                        'name' => $driver->name,
                        'params' => $item,
                        'html' => $driver->getHtml()
                    ];

                    switch ($type) {
                        case 'titleToolbar':
                            $titleToolbarItems[] = $toolbar;
                            break;
                        case 'titleRightToolbar':
                            $titleRightToolbarItems[] = $toolbar;
                            break;
                        case 'formToolbar':
                            $formToolbarItems[] = $toolbar;
                            break;
                        case 'tableToolbar':
                            $tableToolbarItems[] = $toolbar;
                            break;
                        default:
                            $toolbarItems[] = $toolbar;
                    }

                    $vueDataX = $driver->getVueData();
                    if ($vueDataX) {
                        $vueData = \Be\Util\Arr::merge($vueData, $vueDataX);
                    }

                    $vueMethodsX = $driver->getVueMethods();
                    if ($vueMethodsX) {
                        $vueMethods = array_merge($vueMethods, $vueMethodsX);
                    }
                }
            }
        }
        ?>
        <div id="app" v-cloak>
            <?php
            if (count($titleToolbarItems) > 0 || count($titleRightToolbarItems) > 0) {
                ?>
                <div class="be-row be-my-80" style="align-items:center;">
                    <div class="be-col-auto">
                        <div class="be-fs-125 be-py-50"><?php echo $this->title; ?></div>
                    </div>

                    <?php
                    if (count($titleToolbarItems) > 0) {
                        foreach ($titleToolbarItems as $item) {
                            echo '<div class="be-col-auto be-pl-100">';
                            echo $item['html'];
                            echo '</div>';
                        }
                    }

                    echo '<div class="be-col"></div>';

                    if (count($titleRightToolbarItems) > 0) {
                        foreach ($titleRightToolbarItems as $item) {
                            echo '<div class="be-col-auto be-pl-100">';
                            echo $item['html'];
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
                <?php
            } else {
                ?>
                <div class="be-center-title"><?php echo $this->title; ?></div>
                <?php
            }
            ?>

            <div class="be-center-body">

                <el-form<?php
                $formUi = [
                    ':inline' => 'true',
                    'size' => 'medium',
                ];
                if (isset($this->setting['form']['ui'])) {
                    $formUi = array_merge($formUi, $this->setting['form']['ui']);
                }

                foreach ($formUi as $k => $v) {
                    if ($v === null) {
                        echo ' ' . $k;
                    } else {
                        echo ' ' . $k . '="' . $v . '"';
                    }
                }
                ?>>
                    <?php
                    if (isset($this->setting['headnote'])) {
                        echo $this->setting['headnote'];
                    }

                    $tabHtml = '';
                    $tabPosition = 'beforeForm';
                    if (isset($this->setting['tab'])) {
                        $driver = new \Be\AdminPlugin\Tab\Driver($this->setting['tab']);
                        $tabHtml = $driver->getHtml();
                        if (isset($this->setting['tab']['position'])) {
                            $tabPosition = $this->setting['tab']['position'];
                        }

                        $formData[$driver->name] = $driver->value;

                        $vueDataX = $driver->getVueData();
                        if ($vueDataX) {
                            $vueData = \Be\Util\Arr::merge($vueData, $vueDataX);
                        }

                        $vueMethodsX = $driver->getVueMethods();
                        if ($vueMethodsX) {
                            $vueMethods = array_merge($vueMethods, $vueMethodsX);
                        }
                    }

                    if ($tabHtml && $tabPosition === 'beforeForm') {
                        echo $tabHtml;
                    }

                    if (isset($this->setting['form']['items']) && count($this->setting['form']['items']) > 0) {
                        ?>
                        <el-row id="form-items" ref="formItemsRef">
                            <el-col :span="24">
                                <?php
                                foreach ($this->setting['form']['items'] as $item) {
                                    $driverClass = null;
                                    if (isset($item['driver'])) {
                                        if (substr($item['driver'], 0, 8) === 'FormItem') {
                                            $driverClass = '\\Be\\AdminPlugin\\Form\\Item\\' . $item['driver'];
                                        } else {
                                            $driverClass = $item['driver'];
                                        }
                                    } else {
                                        $driverClass = \Be\AdminPlugin\Form\Item\FormItemInput::class;
                                    }
                                    $driver = new $driverClass($item);

                                    echo $driver->getHtml();

                                    if ($driver->name !== null) {
                                        $formData[$driver->name] = $driver->getValueString();
                                    }

                                    $jsX = $driver->getJs();
                                    if ($jsX) {
                                        $js = array_merge($js, $jsX);
                                    }

                                    $cssX = $driver->getCss();
                                    if ($cssX) {
                                        $css = array_merge($css, $cssX);
                                    }

                                    $vueDataX = $driver->getVueData();
                                    if ($vueDataX) {
                                        $vueData = \Be\Util\Arr::merge($vueData, $vueDataX);
                                    }

                                    $vueMethodsX = $driver->getVueMethods();
                                    if ($vueMethodsX) {
                                        $vueMethods = array_merge($vueMethods, $vueMethodsX);
                                    }

                                    $vueHooksX = $driver->getVueHooks();
                                    if ($vueHooksX) {
                                        foreach ($vueHooksX as $k => $v) {
                                            if (isset($vueHooks[$k])) {
                                                $vueHooks[$k] .= "\r\n" . $v;
                                            } else {
                                                $vueHooks[$k] = $v;
                                            }
                                        }
                                    }
                                }

                                if (isset($this->setting['form']['actions']) && count($this->setting['form']['actions']) > 0) {
                                    $html = '';
                                    foreach ($this->setting['form']['actions'] as $key => $item) {
                                        if ($key === 'submit') {
                                            if ($item) {
                                                if ($item === true) {
                                                    $html .= '<el-button type="primary" icon="el-icon-search" @click="submit" :disabled="loading">查询</el-button> ';
                                                    continue;
                                                } elseif (is_string($item)) {
                                                    $html .= '<el-button type="primary" icon="el-icon-search" @click="submit" :disabled="loading">' . $item . '</el-button> ';
                                                    continue;
                                                }
                                            } else {
                                                continue;
                                            }
                                        }

                                        $driverClass = null;
                                        if (isset($item['driver'])) {
                                            if (substr($item['driver'], 0, 10) === 'FormAction') {
                                                $driverClass = '\\Be\\AdminPlugin\\Form\\Action\\' . $item['driver'];
                                            } else {
                                                $driverClass = $item['driver'];
                                            }
                                        } else {
                                            $driverClass = \Be\AdminPlugin\Form\Action\FormActionButton::class;
                                        }
                                        $driver = new $driverClass($item);

                                        $html .= $driver->getHtml() . ' ';

                                        $vueDataX = $driver->getVueData();
                                        if ($vueDataX) {
                                            $vueData = \Be\Util\Arr::merge($vueData, $vueDataX);
                                        }

                                        $vueMethodsX = $driver->getVueMethods();
                                        if ($vueMethodsX) {
                                            $vueMethods = array_merge($vueMethods, $vueMethodsX);
                                        }
                                    }

                                    if ($html) {
                                        echo '<el-form-item>' . $html . '</el-form-item>';
                                    }
                                }

                                if (count($formToolbarItems) > 0) {
                                    foreach ($formToolbarItems as $item) {
                                        echo '<el-form-item>';
                                        echo $item['html'];
                                        echo '</el-form-item>';
                                    }
                                }
                                ?>
                            </el-col>
                        </el-row>
                        <?php
                    }

                    if ($tabHtml && $tabPosition === 'beforeToolbar') {
                        echo $tabHtml;
                    }

                    $toggleLayoutHtml = '';
                    if ($this->setting['layout'] === 'toggle') {
                        $toggleLayoutHtml = '<el-dropdown @command="toggleLayout" style="float: right;">';
                        $toggleLayoutHtml .= '<el-button size="medium" style="border: none">';
                        $toggleLayoutHtml .= '<i class="' . ($this->setting['actualLayout'] === 'table' ? 'el-icon-s-grid' : 'el-icon-menu') . '" style="font-size: 1.5rem;"></i>';
                        $toggleLayoutHtml .= '</el-button>';

                        $toggleLayoutHtml .= '<el-dropdown-menu slot="dropdown">';
                        $toggleLayoutHtml .= '<el-dropdown-item command="table"' . ($this->setting['actualLayout'] === 'table' ? ' disabled' : '') . '><i class="el-icon-s-grid" style="font-size: 1.2rem;"></i> 表格布局</el-dropdown-item>';
                        $toggleLayoutHtml .= '<el-dropdown-item command="card"' . ($this->setting['actualLayout'] === 'card' ? 'disabled' : '') . '><i class="el-icon-menu" style="font-size: 1.2rem;"></i> 卡片布局</el-dropdown-item>';
                        $toggleLayoutHtml .= '</el-dropdown-menu>';
                        $toggleLayoutHtml .= '</el-dropdown>';
                    }


                    if (count($toolbarItems) > 0) {

                        echo '<el-row id="toolbar-items" ref="toolbarItemsRef"><el-col :span="24">';
                        echo '<div class="">';
                        foreach ($toolbarItems as $item) {
                            echo '<el-form-item>';
                            echo $item['html'];
                            echo '</el-form-item>';
                        }

                        if ($this->setting['layout'] === 'toggle') {
                            echo $toggleLayoutHtml;
                        }

                        echo '</div>';
                        echo '</el-col></el-row>';
                    } else {
                        if ($this->setting['layout'] === 'toggle') {
                            echo '<div>';
                            echo $toggleLayoutHtml;
                            echo '</div><div style="clear: right"></div>';
                        }
                    }

                    if ($tabHtml && ($tabPosition === 'beforeGrid')) {
                        echo $tabHtml;
                    }

                    if ($this->setting['actualLayout'] === 'table') {
                        ?>
                        <div class="be-p-relative">
                        <el-table<?php
                        $tableUi = [
                            ':data' => 'gridData',
                            'ref' => 'tableRef',
                            'v-loading' => 'loading',
                            'size' => 'medium',
                            ':height' => 'tableHeight',
                            ':default-sort' => '{prop:orderBy,order:orderByDir}',
                            '@sort-change' => 'sort',
                            '@selection-change' => 'selectionChange',
                        ];
                        if (isset($this->setting['table']['ui'])) {
                            $tableUi = array_merge($tableUi, $this->setting['table']['ui']);
                        }

                        foreach ($tableUi as $k => $v) {
                            if ($v === null) {
                                echo ' ' . $k;
                            } else {
                                echo ' ' . $k . '="' . $v . '"';
                            }
                        }
                        ?>>
                            <template slot="empty">
                                <?php
                                if (isset($this->setting['table']['empty']) && is_string($this->setting['table']['empty'])) {
                                    echo $this->setting['table']['empty'];
                                } else {
                                    echo '<el-empty description="暂无数据"></el-empty>';
                                }
                                ?>
                            </template>

                            <?php
                            $opHtml = null;
                            $opPosition = 'right';
                            if (isset($this->setting['table']['operation'])) {

                                $operationDriver = new \Be\AdminPlugin\Operation\TableWrap($this->setting['table']['operation']);
                                $opHtml = $operationDriver->getHtmlBefore();

                                if (isset($this->setting['table']['operation']['items'])) {
                                    foreach ($this->setting['table']['operation']['items'] as $item) {
                                        $driverClass = null;
                                        if (isset($item['driver'])) {
                                            if (substr($item['driver'], 0, 13) === 'OperationItem') {
                                                $driverClass = '\\Be\\AdminPlugin\\Operation\\Item\\' . $item['driver'];
                                            } else {
                                                $driverClass = $item['driver'];
                                            }
                                        } else {
                                            $driverClass = \Be\AdminPlugin\Operation\Item\OperationItemLink::class;
                                        }
                                        $driver = new $driverClass($item);

                                        $opHtml .= $driver->getHtml();

                                        $vueDataX = $driver->getVueData();
                                        if ($vueDataX) {
                                            $vueData = \Be\Util\Arr::merge($vueData, $vueDataX);
                                        }

                                        $vueMethodsX = $driver->getVueMethods();
                                        if ($vueMethodsX) {
                                            $vueMethods = array_merge($vueMethods, $vueMethodsX);
                                        }
                                    }
                                }

                                $opHtml .= $operationDriver->getHtmlAfter();
                                $opPosition = $operationDriver->position;

                                if ($opPosition === 'left') {
                                    echo $opHtml;
                                }
                            }

                            foreach ($this->setting['table']['items'] as $item) {

                                $driverClass = null;
                                if (isset($item['driver'])) {
                                    if (substr($item['driver'], 0, 9) === 'TableItem') {
                                        $driverClass = '\\Be\\AdminPlugin\\Table\\Item\\' . $item['driver'];
                                    } else {
                                        $driverClass = $item['driver'];
                                    }
                                } else {
                                    $driverClass = \Be\AdminPlugin\Table\Item\TableItemText::class;
                                }
                                $driver = new $driverClass($item);

                                echo $driver->getHtml();

                                $vueDataX = $driver->getVueData();
                                if ($vueDataX) {
                                    $vueData = \Be\Util\Arr::merge($vueData, $vueDataX);
                                }

                                $vueMethodsX = $driver->getVueMethods();
                                if ($vueMethodsX) {
                                    $vueMethods = array_merge($vueMethods, $vueMethodsX);
                                }
                            }

                            if (isset($this->setting['table']['operation']) && $opPosition === 'right') {
                                echo $opHtml;
                            }
                            ?>
                        </el-table>
                        <?php

                        if (isset($this->setting['table']['items'][0]) ) {
                            $tableItem0 = $this->setting['table']['items'][0];
                            if (isset($tableItem0['driver']) && $tableItem0['driver'] === \Be\AdminPlugin\Table\Item\TableItemSelection::class) {
                                if (count($tableToolbarItems) > 0) {
                                    echo '<div class="table-action" v-if="selectedRows.length > 0" :style="{left: tableActionLeft, height: tableActionHeight}">';
                                    echo '<span class="be-px-100">已选中 {{selectedRows.length}} 项</span>';
                                    foreach ($tableToolbarItems as $item) {
                                        echo $item['html'];
                                    }
                                    echo '</div>';
                                }
                            }
                        }

                        echo '</div>';

                    } elseif ($this->setting['actualLayout'] === 'card') {
                        ?>
                        <div v-loading='loading'>
                            <el-row<?php
                                if (isset($this->setting['card']['ui']['row'])) {
                                    foreach ($this->setting['card']['ui']['row'] as $k => $v) {
                                        if ($v === null) {
                                            echo ' ' . $k;
                                        } else {
                                            echo ' ' . $k . '="' . $v . '"';
                                        }
                                    }
                                }
                                ?> >

                                <el-col<?php
                                    if (isset($this->setting['card']['ui']['col'])) {
                                        foreach ($this->setting['card']['ui']['col'] as $k => $v) {
                                            if ($v === null) {
                                                echo ' ' . $k;
                                            } else {
                                                echo ' ' . $k . '="' . $v . '"';
                                            }
                                        }
                                    }
                                    ?> v-for="(item, itemKey) in gridData" style="margin-bottom: 15px;">
                                    <el-card<?php
                                        foreach ($this->setting['card']['ui'] as $k => $v) {
                                            if ($k === 'row' || $k === 'col') {
                                                continue;
                                            }

                                            if ($v === null) {
                                                echo ' ' . $k;
                                            } else {
                                                echo ' ' . $k . '="' . $v . '"';
                                            }
                                        }
                                        ?>>
                                        <?php
                                        if (isset($this->setting['card']['template'])) {
                                            echo $this->setting['card']['template'];
                                        } else {
                                            if (isset($this->setting['card']['image'])) {
                                                if ($this->setting['card']['image']['position'] === 'left') {
                                                    echo '<div class="card-lr">';
                                                    echo '<div class="card-lr-image">';
                                                    echo '<a :href="item.'. $this->setting['card']['image']['name'] .'" target="_blank">';
                                                    echo '<img :src="item.'. $this->setting['card']['image']['name'] .'">';
                                                    echo '</a>';
                                                    echo '</div>';
                                                    echo '<div class="card-lr-space"></div>';
                                                    echo '<div class="card-lr-right">';
                                                } elseif ($this->setting['card']['image']['position'] === 'top') {
                                                    echo '<div class="card-tb-image">';
                                                    echo '<a :href="item.'. $this->setting['card']['image']['name'] .'" target="_blank">';
                                                    echo '<img :src="item.'. $this->setting['card']['image']['name'] .'">';
                                                    echo '</a>';
                                                    echo '</div>';
                                                }
                                            }

                                            $cssClass = null;
                                            if (isset($this->setting['card']['image'])) {
                                                if ($this->setting['card']['image']['position'] === 'left') {
                                                    $cssClass = 'card-lr-right-items';
                                                } elseif ($this->setting['card']['image']['position'] === 'top') {
                                                    $cssClass = 'card-tb-items';
                                                }
                                            } else {
                                                $cssClass = 'card-items';
                                            }
                                            echo '<div class="' . $cssClass . '">';
                                            if (isset($this->setting['card']['items'])) {
                                                foreach ($this->setting['card']['items'] as $item) {
                                                    $driverClass = null;
                                                    if (isset($item['driver'])) {
                                                        if (substr($item['driver'], 0, 9) === 'CardItem') {
                                                            $driverClass = '\\Be\\AdminPlugin\\Card\\Item\\' . $item['driver'];
                                                        } else {
                                                            $driverClass = $item['driver'];
                                                        }
                                                    } else {
                                                        $driverClass = \Be\AdminPlugin\Card\Item\CardItemText::class;
                                                    }
                                                    $driver = new $driverClass($item);

                                                    echo $driver->getHtml();

                                                    $vueDataX = $driver->getVueData();
                                                    if ($vueDataX) {
                                                        $vueData = \Be\Util\Arr::merge($vueData, $vueDataX);
                                                    }

                                                    $vueMethodsX = $driver->getVueMethods();
                                                    if ($vueMethodsX) {
                                                        $vueMethods = array_merge($vueMethods, $vueMethodsX);
                                                    }
                                                }
                                            }
                                            echo '</div>';

                                            // 操作
                                            if (isset($this->setting['card']['operation'])) {
                                                if (isset($this->setting['card']['operation']['items'])) {
                                                    $cssClass = null;
                                                    if (isset($this->setting['card']['image'])) {
                                                        if ($this->setting['card']['image']['position'] === 'left') {
                                                            $cssClass = 'card-lr-right-operations';
                                                        } elseif ($this->setting['card']['image']['position'] === 'top') {
                                                            $cssClass = 'card-tb-operations';
                                                        }
                                                    } else {
                                                        $cssClass = 'card-operations';
                                                    }

                                                    echo '<div class="' . $cssClass . '">';
                                                    echo '<card-operation :data="item">';
                                                    echo '<template scope="scope">';
                                                    foreach ($this->setting['card']['operation']['items'] as $item) {
                                                        $driverClass = null;
                                                        if (isset($item['driver'])) {
                                                            if (substr($item['driver'], 0, 13) === 'OperationItem') {
                                                                $driverClass = '\\Be\\AdminPlugin\\Operation\\Item\\' . $item['driver'];
                                                            } else {
                                                                $driverClass = $item['driver'];
                                                            }
                                                        } else {
                                                            $driverClass = \Be\AdminPlugin\Operation\Item\OperationItemLink::class;
                                                        }
                                                        $driver = new $driverClass($item);

                                                        echo $driver->getHtml();

                                                        $vueDataX = $driver->getVueData();
                                                        if ($vueDataX) {
                                                            $vueData = \Be\Util\Arr::merge($vueData, $vueDataX);
                                                        }

                                                        $vueMethodsX = $driver->getVueMethods();
                                                        if ($vueMethodsX) {
                                                            $vueMethods = array_merge($vueMethods, $vueMethodsX);
                                                        }
                                                    }
                                                    echo '</template>';
                                                    echo '</card-operation>';
                                                    echo '</div>';
                                                }
                                            }

                                            if (isset($this->setting['card']['image'])) {
                                                if ($this->setting['card']['image']['position'] === 'left') {
                                                    echo '</div>';
                                                    echo '</div>';
                                                }
                                            }
                                        }
                                        ?>
                                    </el-card>
                                </el-col>
                            </el-row>
                        </div>
                        <?php
                    }

                    if (isset($this->setting['footnote'])) {
                        echo $this->setting['footnote'];
                    }
                    ?>

                    <div style="text-align: center; padding: 10px 10px 0 10px;" v-if="total > 0">
                        <el-pagination
                                @size-change="changePageSize"
                                @current-change="gotoPage"
                                :current-page="page"
                                :page-sizes="[10, 12, 15, 20, 25, 30, 50, 100, 200, 500]"
                                :page-size="pageSize"
                                layout="total, sizes, prev, pager, next, jumper"
                                :total="total">
                        </el-pagination>
                    </div>
                </el-form>

                <el-dialog
                        :title="dialog.title"
                        :visible.sync="dialog.visible"
                        :width="dialog.width"
                        :close-on-click-modal="false"
                        :destroy-on-close="true">
                    <iframe id="frame-dialog" name="frame-dialog" src="about:blank"
                            :style="{width:'100%',height:dialog.height,border:0}"></iframe>
                </el-dialog>

                <el-drawer
                        :visible.sync="drawer.visible"
                        :size="drawer.width"
                        :title="drawer.title"
                        :wrapper-closable="false"
                        :destroy-on-close="true">
                    <div style="padding:0 20px;height: 100%;overflow:hidden;">
                        <iframe id="frame-drawer" name="frame-drawer" src="about:blank"
                                style="width:100%;height:100%;border:0;"></iframe>
                    </div>
                </el-drawer>

            </div>

        </div>
    </div>


    <?php
    if (isset($this->setting['js'])) {
        $js = array_merge($js, $this->setting['js']);
    }

    if (isset($this->setting['css'])) {
        $css = array_merge($css, $this->setting['css']);
    }

    if (isset($this->setting['vueData'])) {
        $vueData = \Be\Util\Arr::merge($vueData, $this->setting['vueData']);
    }

    if (isset($this->setting['vueMethods'])) {
        $vueMethods = \Be\Util\Arr::merge($vueMethods, $this->setting['vueMethods']);
    }

    if (isset($this->setting['vueHooks'])) {
        foreach ($this->setting['vueHooks'] as $k => $v) {
            if (isset($vueHooks[$k])) {
                $vueHooks[$k] .= "\r\n" . $v;
            } else {
                $vueHooks[$k] = $v;
            }
        }
    }

    if (count($js) > 0) {
        $js = array_unique($js);
        foreach ($js as $x) {
            echo '<script src="' . $x . '"></script>';
        }
    }

    if (count($css) > 0) {
        $css = array_unique($css);
        foreach ($css as $x) {
            echo '<link rel="stylesheet" href="' . $x . '">';
        }
    }

    $tableActionLeft = '50px';
    if (isset($this->setting['table']['items'][0]) ) {
        $tableItem0 = $this->setting['table']['items'][0];
        if (isset($tableItem0['driver']) && $tableItem0['driver'] === \Be\AdminPlugin\Table\Item\TableItemSelection::class) {
            if (count($tableToolbarItems) > 0) {
                if (isset($tableItem0['width'])) {
                    $tableActionLeft = $tableItem0['width'] . 'px';
                }
            }
        }
    }
    ?>

    <script>

        <?php if ($this->setting['actualLayout'] === 'card') { ?>
        Vue.component('card-operation', {
            template: '<slot :row="data"></slot>',
            props: {
                data: {
                    type: Array,
                    required: true
                }
            },
            data() {
                return {
                }
            }
        });
        <?php } ?>

        var pageSizeKey = "<?php echo $this->url; ?>:pageSize";
        var pageSize = localStorage.getItem(pageSizeKey);
        if (pageSize === null || isNaN(pageSize)) {
            pageSize = <?php echo $this->pageSize; ?>;
        } else {
            pageSize = Number(pageSize);
        }

        var vueGrid = new Vue({
            el: '#app',
            data: {
                formData: <?php echo json_encode($formData); ?>,
                gridData: [],
                orderBy: "",
                orderByDir: "",
                pageSize: pageSize,
                page: 1,
                pages: 1,
                total: 0,
                selectedRows: [],
                loading: false,
                tableHeight: 500,
                tableActionLeft: "<?php echo $tableActionLeft; ?>",
                tableActionHeight: "55px",
                dialog: {visible: false, width: "600px", height: "400px", title: ""},
                drawer: {visible: false, width: "40%", title: ""}<?php
                if ($vueData) {
                    foreach ($vueData as $k => $v) {
                        echo ',' . $k . ':' . json_encode($v);
                    }
                }
                ?>
            },
            methods: {
                submit: function () {
                    this.page = 1;
                    this.loadGridData();
                },
                loadGridData: function () {
                    this.loading = true;
                    var _this = this;
                    _this.$http.post("<?php echo $this->setting['form']['action']; ?>", {
                        actualLayout: "<?php echo $this->setting['actualLayout']; ?>",
                        formData: _this.formData,
                        orderBy: _this.orderBy,
                        orderByDir: _this.orderByDir,
                        page: _this.page,
                        pageSize: _this.pageSize
                    }).then(function (response) {
                        _this.loading = false;
                        //console.log(response);
                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                _this.total = parseInt(responseData.data.total);
                                _this.gridData = responseData.data.gridData;
                                _this.pages = Math.floor(_this.total / _this.pageSize);
                                <?php
                                if (isset($this->setting['tab']['counter']) && $this->setting['tab']['counter']) {
                                    echo '_this.formItems.' . $this->setting['tab']['name'].'.counters = responseData.data.tabCounters;';
                                }
                                ?>
                            } else {
                                _this.total = 0;
                                _this.gridData = [];
                                _this.page = 1;
                                _this.pages = 1;

                                if (responseData.message) {
                                    _this.$message({
                                        showClose: true,
                                        message: responseData.message,
                                        type: 'error'
                                    });
                                }
                            }
                            _this.resize();
                            _this.updateToolbars();
                        }
                    }).catch(function (error) {
                        _this.loading = false;
                        _this.$message.error(error);
                    });
                },
                reloadGridData: function () {
                    this.loading = true;
                    var _this = this;
                    _this.$http.post("<?php echo $this->setting['form']['action']; ?>", {
                        actualLayout: "<?php echo $this->setting['actualLayout']; ?>",
                        formData: _this.formData,
                        orderBy: _this.orderBy,
                        orderByDir: _this.orderByDir,
                        page: _this.page,
                        pageSize: _this.pageSize
                    }).then(function (response) {
                        _this.loading = false;
                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                _this.total = parseInt(responseData.data.total);
                                _this.gridData = responseData.data.gridData;
                                _this.pages = Math.floor(_this.total / _this.pageSize);
                                <?php
                                if (isset($this->setting['tab']['counter']) && $this->setting['tab']['counter']) {
                                    echo '_this.formItems.' . $this->setting['tab']['name'].'.counters = responseData.data.tabCounters;';
                                }
                                ?>
                            }
                            _this.resize();
                            _this.updateToolbars();
                        }
                    }).catch(function (error) {
                        _this.loading = false;
                        _this.$message.error(error);
                    });
                },
                changePageSize: function (pageSize) {
                    this.pageSize = pageSize;
                    this.page = 1;
                    localStorage.setItem(pageSizeKey, pageSize);
                    this.loadGridData();
                },
                gotoPage: function (page) {
                    this.page = page;
                    this.loadGridData();
                },
                sort: function (option) {
                    if (option.order === "ascending" || option.order === "descending") {
                        this.orderBy = option.prop;
                        this.orderByDir = option.order === "ascending" ? "ASC" : "DESC";
                    } else {
                        this.orderBy = "";
                        this.orderByDir = "";
                    }
                    this.loadGridData();
                },
                <?php if ($this->setting['layout'] === 'toggle') { ?>
                toggleLayout: function (command) {
                    var url = "<?php echo $this->setting['form']['action']; ?>";
                    if (url.indexOf("layout=") >=0 ) {
                        url = url.replace(/layout=[a-z]+/, "layout=" + command);
                    } else {
                        url += (url.indexOf("?") >= 0 ? "&" : "?") + "layout=" + command;
                    }
                    window.location.href = url;
                },
                <?php } ?>
                formAction: function (name, option) {
                    var data = {
                        formData: this.formData,
                        orderBy: this.orderBy,
                        orderByDir: this.orderByDir,
                        page: this.page,
                        pageSize: this.pageSize
                    };

                    data.postData = option.postData;
                    data.selectedRows = this.selectedRows;
                    return this.action(option, data);
                },
                toolbarItemAction: function (name, option) {
                    var data = {
                        formData: this.formData,
                        orderBy: this.orderBy,
                        orderByDir: this.orderByDir,
                        page: this.page,
                        pageSize: this.pageSize
                    };

                    data.postData = option.postData;
                    data.selectedRows = this.selectedRows;
                    return this.action(option, data);
                },
                gridItemAction: function (name, option, row) {
                    switch (option.target) {
                        case "dialog":
                            option.dialog.title = row[name];
                            break;
                        case "drawer":
                            option.drawer.title = row[name];
                            break;
                    }

                    var data = {};
                    data.postData = option.postData;
                    data.row = row;
                    return this.action(option, data);
                },
                operationItemAction: function (name, option, row) {
                    var data = {};
                    data.postData = option.postData;
                    data.row = row;
                    return this.action(option, data);
                },
                action: function (option, data) {
                    if (option.target === 'ajax') {
                        this.loading = true;
                        var _this = this;
                        this.$http.post(option.url, data).then(function (response) {
                            _this.loading = false;
                            if (response.status === 200) {
                                if (response.data.success) {
                                    _this.$message({
                                        showClose: true,
                                        message: response.data.message,
                                        type: 'success'
                                    });
                                } else {
                                    if (response.data.message) {
                                        _this.$message({
                                            showClose: true,
                                            message: response.data.message,
                                            type: 'error'
                                        });
                                    }
                                }

                                if (response.data.callback) {
                                    setTimeout(function () {
                                        try {
                                            eval(response.data.callback);
                                        } catch (e) {}
                                    }, 1000);
                                }

                                _this.loadGridData();
                            }
                        }).catch(function (error) {
                            _this.loading = false;
                            _this.$message({
                                showClose: true,
                                message: error,
                                type: 'error'
                            });
                            _this.loadGridData();
                        });
                    } else {
                        var eForm = document.createElement("form");
                        eForm.action = option.url;
                        switch (option.target) {
                            case "self":
                            case "_self":
                                eForm.target = "_self";
                                break;
                            case "blank":
                            case "_blank":
                                eForm.target = "_blank";
                                break;
                            case "window":
                                let windowName = "window-" + Math.floor(Math.random() * 100000000);
                                window.open("about:blank", windowName);
                                eForm.target = windowName;
                                break;
                            case "dialog":
                                eForm.target = "frame-dialog";
                                this.dialog.title = option.dialog.title;
                                this.dialog.width = option.dialog.width;
                                this.dialog.height = option.dialog.height;
                                this.dialog.visible = true;
                                break;
                            case "drawer":
                                eForm.target = "frame-drawer";
                                this.drawer.title = option.drawer.title;
                                this.drawer.width = option.drawer.width;
                                this.drawer.visible = true;
                                break;
                        }
                        eForm.method = "post";
                        eForm.style.display = "none";

                        var e = document.createElement("textarea");
                        e.name = 'data';
                        e.value = JSON.stringify(data);
                        eForm.appendChild(e);

                        document.body.appendChild(eForm);

                        setTimeout(function () {
                            eForm.submit();
                        }, 50);

                        setTimeout(function () {
                            document.body.removeChild(eForm);
                        }, 3000);
                    }

                    return false;
                },
                hideDialog: function () {
                    this.dialog.visible = false;
                },
                hideDrawer: function () {
                    this.drawer.visible = false;
                },
                selectionChange: function (rows) {
                    this.selectedRows = rows;
                    this.updateToolbars();
                },
                updateToolbars: function () {
                    var toolbarEnable;
                    <?php
                    if (count($tableToolbarItems) > 0) {
                        foreach ($tableToolbarItems as $item) {
                            if (isset($item['params']['task']) && $item['params']['task'] === 'fieldEdit' && isset($item['params']['postData']['field']) && isset($item['params']['postData']['value'])) {
                                ?>
                                if (this.selectedRows.length > 0) {
                                    toolbarEnable = true;
                                    for (var x in this.selectedRows) {
                                        if (this.selectedRows[x].<?php echo $item['params']['postData']['field']; ?> === "<?php echo $item['params']['postData']['value']; ?>") {
                                            toolbarEnable = false;
                                        }
                                    }
                                } else {
                                    toolbarEnable = false;
                                }
                                this.toolbarItems.<?php echo $item['name']; ?>.enable = toolbarEnable;
                                <?php
                            }
                        }
                    }
                    ?>
                },
                resize: function () {
                    <?php if ($this->setting['actualLayout'] === 'table') { ?>
                    let offset = this.total > 0 ? 55 : 15;
                    let rect = this.$refs.tableRef.$el.getBoundingClientRect();
                    //console.log(rect);
                    this.tableHeight = Math.max(document.documentElement.clientHeight - rect.top - offset, 100);
                    <?php } ?>
                }
                <?php
                if ($vueMethods) {
                    foreach ($vueMethods as $k => $v) {
                        echo ',' . $k . ':' . $v;
                    }
                }
                ?>
            },
            created: function () {
                this.submit();
                <?php
                if (isset($this->setting['reload']) && is_numeric($this->setting['reload'])) {
                    echo 'var _this = this;';
                    echo 'setInterval(function () {_this.reloadGridData();}, ' . ($this->setting['reload'] * 1000) . ');';
                }

                if (isset($vueHooks['created'])) {
                    echo $vueHooks['created'];
                }
                ?>
            },
            mounted: function () {
                this.$nextTick(function () {
                    this.resize();
                    let _this = this;
                    window.onresize = function () {
                        _this.resize();
                    };
                });

                <?php
                if (isset($vueHooks['mounted'])) {
                    echo $vueHooks['mounted'];
                }
                ?>
            },
            updated: function () {
                <?php if ($this->setting['actualLayout'] === 'table') { ?>
                let _this = this;
                this.$nextTick(function () {
                    _this.$refs.tableRef.doLayout();

                    if (_this.selectedRows.length > 0) {
                        let tableActionLeft = _this.$refs.tableRef.$el.getElementsByClassName('el-table-column--selection')[0].clientWidth;
                        _this.tableActionLeft = tableActionLeft + "px";

                        let tableActionHeight = _this.$refs.tableRef.$el.getElementsByClassName('el-table__header')[0].clientHeight;
                        _this.tableActionHeight = tableActionHeight + "px";
                    }
                });
                <?php
                }

                if (isset($vueHooks['updated'])) {
                    echo $vueHooks['updated'];
                }
                ?>
            }
            <?php
            if (isset($vueHooks['beforeCreate'])) {
                echo ',beforeCreate: function () {' . $vueHooks['beforeCreate'] . '}';
            }

            if (isset($vueHooks['beformMount'])) {
                echo ',beformMount: function () {' . $vueHooks['beformMount'] . '}';
            }

            if (isset($vueHooks['beforeUpdate'])) {
                echo ',beforeUpdate: function () {' . $vueHooks['beforeUpdate'] . '}';
            }


            if (isset($vueHooks['beforeDestroy'])) {
                echo ',beforeDestroy: function () {' . $vueHooks['beforeDestroy'] . '}';
            }

            if (isset($vueHooks['destroyed'])) {
                echo ',destroyed: function () {' . $vueHooks['destroyed'] . '}';
            }
            ?>
        });

        function reload() {
            vueGrid.reloadGridData();
        }

        function close() {
            vueGrid.drawer.visible = false;
            vueGrid.dialog.visible = false;
        }

        function closeDrawer() {
            vueGrid.drawer.visible = false;
        }

        function closeDialog() {
            vueGrid.dialog.visible = false;
        }

        function closeAndReload() {
            vueGrid.drawer.visible = false;
            vueGrid.dialog.visible = false;
            vueGrid.reloadGridData();
        }

        function closeDrawerAndReload() {
            vueGrid.drawer.visible = false;
            vueGrid.reloadGridData();
        }

        function closeDialogAndReload() {
            vueGrid.dialog.visible = false;
            vueGrid.reloadGridData();
        }

    </script>
</be-center>
