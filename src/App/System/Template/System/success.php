<be-body>
    <div class="be-p-100">

        <div class="be-ta-center be-c-green be-mt-300">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
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
                    $redirectMessage = 'Redirect to <a href="{url}">{url}</a> after {timeout} seconds.';
                }

                foreach ([
                             '{url}' => $redirectUrl,
                             '{timeout}' => '<span id="redirect-timeout">' . $redirectTimeout . '</span>',
                         ] as $key => $val) {
                    $redirectMessage = str_replace($key, $val, $redirectMessage);
                }

                echo '<div class="be-ta-center be-c-999 be-mt-100">' . $redirectMessage . '</div>';
            }
        }
        ?>
    </div>

    <?php
    if (isset($this->redirect))
    {
        $redirectUrl = $this->redirect['url'];
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
                        window.location.href = "<?php echo $redirectUrl; ?>";
                    }
                }, 1000);
            </script>
        <?php
        } else {
        ?>
            <script>window.location.href = "<?php echo $redirectUrl; ?>";</script>
            <?php
        }
    }
    ?>
</be-body>
