<be-body>
    <div class="p-2">
        <div class="text-center display-1 text-danger mt-5">
            <i class="bi bi-x-circle-fill"></i>
        </div>

        <div class="text-center fs-4 mt-5">
            <?php echo $this->message; ?>
        </div>

        <?php
        if (isset($this->redirectUrl) && isset($this->redirectTimeout) && $this->redirectTimeout > 0 )
        {
            ?>
            <div class="text-center text-muted mt-2">
                Redirect to <a  href="<?php echo $this->redirectUrl; ?>"><?php echo $this->redirectUrl; ?></a> after <span id="redirect-timer"><?php echo $this->redirectTimeout; ?></span> seconds.
            </div>
            <?php
        }
        ?>
    </div>

    <?php
    if (isset($this->redirectUrl)) {
        if (isset($this->redirectTimeout) && $this->redirectTimeout > 0) {
            ?>
            <script>
                var redirectTimer = <?php echo $this->redirectTimeout; ?>;
                var timer = setInterval(function () {
                    redirectTimer--;
                    document.getElementById("redirect-timer").innerHTML = redirectTimer;
                    if (redirectTimer <= 0) {
                        clearInterval(timer);
                        window.location.href = "<?php echo $this->redirectUrl; ?>";
                    }
                }, 1000);
            </script>
            <?php
        } else {
            ?>
            <script>window.location.href = "<?php echo $this->redirectUrl; ?>";</script>
            <?php
        }
    }
    ?>
</be-body>

