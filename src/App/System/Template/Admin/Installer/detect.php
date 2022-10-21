<be-center>
    <?php
    foreach ([
            [
                'name' => 'PHP版本（7.4+）',
                'value' => $this->value['isPhpVersionGtMatch'],
                'values' => ['不符合', '符合'],
            ],
            [
                'name' => 'PDO Mysql 扩展',
                'value' => $this->value['isPdoMysqlInstalled'],
                'values' => ['未完装', '已安装'],
            ],
            [
                'name' => 'Redis 扩展',
                'value' => $this->value['isRedisInstalled'],
                'values' => ['未完装', '已安装'],
            ],
            [
                'name' => 'data 目录可写',
                'value' => $this->value['isDataDirWritable'],
                'values' => ['否', '是'],
            ],
            [
                'name' => 'www 目录可写',
                'value' => $this->value['isWwwDirWritable'],
                'values' => ['否', '是'],
            ],
             ] as $item) {

        ?>
        <div class="be-row be-mt-200">
            <div class="be-col be-ta-right be-c-999">
                <?php echo $item['name']; ?>：
            </div>
            <div class="be-col">
                <?php
                if ($item['value'] ) {
                    echo '<span class="be-c-green">' . $item['values'][1] . '</span>';
                } else {
                    echo '<span class="be-c-red">' . $item['values'][0] . '</span>';
                }
                ?>
            </div>
        </div>
        <?php
    }
    ?>
    <div class="be-mt-200 be-bt-eee be-pt-100 be-ta-center">
        <form action="<?php echo \Be\Be::getRequest()->getUrl(); ?>" method="post">
            <input type="submit" class="be-btn be-btn-major"<?php echo $this->isAllPassed ? '' : ' disabled'; ?> value="继续安装">
        </form>
    </div>


</be-center>
