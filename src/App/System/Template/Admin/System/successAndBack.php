<be-head>
    <link type="text/css" rel="stylesheet" href="<?php echo \Be\Be::getProperty('App.System')->getUrl(); ?>/Template/Admin/System/css/success.css">
</be-head>

<be-body>

    <div id="app" v-cloak>
        <div class="success-icon">
            <i class="el-icon-success"></i>
        </div>

        <div class="success-message">
            <?php echo $this->message; ?>
        </div>

        <?php
        if (isset($this->redirectTimeout) && $this->redirectTimeout > 0)
        {
            ?>
            <div class="success-timer">
                <span>{{redirectTimer}}</span> 秒后返回
            </div>
            <?php
        }
        ?>

        <form action="<?php echo $this->historyUrl; ?>" id="form-history" method="post">
            <?php
            if (is_array($this->historyPost) && count($this->historyPost) > 0) {
                foreach ($this->historyPost as $key => $val) {
                    echo '<input type="hidden" name="' . $key . '" value="' . $val . '"/>';
                }
            }
            ?>
        </form>
    </div>

    <script>
        new Vue({
            el: '#app',
            data: {
                redirectTimer: <?php echo isset($this->redirectTimeout) ? $this->redirectTimeout : 0; ?>
            },
            created: function () {
                <?php
                if (isset($this->redirectTimeout) && $this->redirectTimeout > 0) {
                    ?>
                    var _this = this;
                    var timer = setInterval(function () {
                        _this.redirectTimer--;
                        if (_this.redirectTimer <= 0) {
                            clearInterval(timer);
                            document.getElementById("form-history").submit();
                        }
                    }, 1000);
                    <?php
                } else {
                    ?>
                    document.getElementById("form-history").submit();
                    <?php
                }
                ?>
            }
        });
    </script>

</be-body>
