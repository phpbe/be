
<be-body>
    <div class="be-p-100">

        <div class="be-ta-center be-c-red be-mt-300">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
            </svg>
        </div>

        <div class="be-ta-center be-fs-150 be-mt-300">
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
                    $redirectMessage = 'Return after {timeout} seconds.';
                }

                foreach ([
                             '{url}' => $redirectUrl,
                             '{link}' => '<a href="' . $redirectUrl . '">' . $redirectUrl . '</a>',
                             '{timeout}' => '<span id="redirect-timeout">' . $redirectTimeout . '</span>',
                         ] as $key => $val) {
                    $redirectMessage = str_replace($key, $val, $redirectMessage);
                }

                echo '<div class="be-ta-center be-c-999 be-mt-100">' . $redirectMessage . '</div>';
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


    <?php
    if (isset($this->redirect))
    {
        $redirectTimeout = $this->redirect['timeout'];
        if ($redirectTimeout > 0) {
            ?>
            <script>
                var redirectTimer = <?php echo $redirectTimeout; ?>;
                var timer = setInterval(function () {
                    redirectTimer--;
                    document.getElementById("redirect-timeout").innerHTML = redirectTimer;
                    if (redirectTimer <= 0) {
                        clearInterval(timer);
                        document.getElementById("form-history").submit();
                    }
                }, 1000);
            </script>
        <?php
        }
    } else {
    ?>
        <script>document.getElementById("form-history").submit();</script>
        <?php
    }
    ?>

</be-body>
