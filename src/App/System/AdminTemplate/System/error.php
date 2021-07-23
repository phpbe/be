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
                <span>{{timer}}</span> 秒后跳转到：<el-link type="primary" href="<?php echo $this->redirectUrl; ?>"><?php echo $this->redirectUrl; ?></el-link>
            </div>
            <?php
        }
        ?>
    </div>

    <script>
        new Vue({
            el: '#app',
            data: {
                timer: <?php echo isset($this->redirectTimeout) ? $this->redirectTimeout : 0; ?>
            },
            created: function () {
                <?php
                if (isset($this->redirectUrl)) {
                if (isset($this->redirectTimeout) && $this->redirectTimeout > 0) {
                ?>
                var _this = this;
                setInterval(function () {
                    _this.timer--;
                    if (_this.timer <= 0) {
                        window.location.href = "<?php echo $this->redirectUrl; ?>";
                    }
                }, 1000);
                <?php
                } else {
                ?>
                window.location.href = "<?php echo $this->redirectUrl; ?>";
                <?php
                }
                }
                ?>
            }
        });
    </script>

</be-body>
