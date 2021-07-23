<be-head>
    <link type="text/css" rel="stylesheet" href="<?php echo \Be\Be::getProperty('App.System')->getUrl(); ?>/AdminTemplate/System/css/error.css">
</be-head>

<be-body>

    <div id="app" v-cloak>
        <div class="error-icon">
            <i class="el-icon-warning"></i>
        </div>

        <div class="error-message">
            <?php echo $this->message; ?>
        </div>

        <?php
        if (isset($this->redirectUrl) && isset($this->redirectTimeout) && $this->redirectTimeout > 0 )
        {
            ?>
            <div class="error-timer">
                <span>{{timer}}</span> 秒后返回
            </div>
            <?php
        }
        ?>

        <form action="<?php echo $this->historyUrl; ?>" id="from-history" method="post">
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
                timer: <?php echo isset($this->redirectTimeout) ? $this->redirectTimeout : 0; ?>
            },
            created: function () {
                <?php
                if (isset($this->redirectTimeout) && $this->redirectTimeout > 0) {
                ?>
                var _this = this;
                setInterval(function () {
                    _this.timer--;
                    if (_this.timer <= 0) {
                        document.getElementById("from-history").submit();
                    }
                }, 1000);
                <?php
                } else {
                ?>
                document.getElementById("from-history").submit();
                <?php
                }
                ?>
            }
        });
    </script>

</be-body>
