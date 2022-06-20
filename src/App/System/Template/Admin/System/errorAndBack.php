<be-head>
    <link type="text/css" rel="stylesheet" href="<?php echo \Be\Be::getProperty('App.System')->getWwwUrl(); ?>/Template/Admin/System/css/error.css">
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
        if (isset($this->redirect))
        {
            $redirectTimeout = $this->redirect['timeout'];
            if ($redirectTimeout > 0) {
                $redirectUrl = $this->redirect['url'];
                $redirectMessage = $this->redirect['message'];
                if (!$redirectMessage) {
                    $redirectMessage = '{timeout} 秒后返回';
                }

                foreach ([
                             '{url}' => $redirectUrl,
                             '{link}' => '<el-link type="primary" href="' . $redirectUrl . '">' . $redirectUrl . '</el-link>',
                             '{timeout}' => '<span>{{redirectTimeout}}</span>',
                         ] as $key => $val) {
                    $redirectMessage = str_replace($key, $val, $redirectMessage);
                }

                echo '<div class="error-timer">' . $redirectMessage . '</div>';
            }
        }
        ?>

        <form action="<?php echo $this->historyUrl; ?>" id="form-history" method="post">
            <?php
            if ($this->historyPostData && is_array($this->historyPostData) && count($this->historyPostData) > 0) {
                foreach ($this->historyPostData as $key => $val) {
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
                redirectTimeout: <?php echo isset($this->redirect) ? $this->redirect['timeout'] : 0; ?>
            },
            created: function () {
                <?php
                if (isset($this->redirect))
                {
                    $redirectUrl = $this->redirect['url'];
                    $redirectTimeout = $this->redirect['timeout'];
                    if ($redirectTimeout > 0) {
                        ?>
                        var _this = this;
                        var timer = setInterval(function () {
                            _this.redirectTimeout--;
                            if (_this.redirectTimeout <= 0) {
                                clearInterval(timer);
                                document.getElementById("form-history").submit();
                            }
                        }, 1000);
                        <?php
                    }
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
