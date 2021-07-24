
<be-body>
    <div class="p-2">
        <div class="text-center display-1 text-danger mt-5">
            <i class="bi bi-x-circle-fill"></i>
        </div>

        <div class="text-center fs-4 mt-5">
            <?php echo $this->message; ?>
        </div>

        <?php
        if (isset($this->redirectTimeout) && $this->redirectTimeout > 0 )
        {
            ?>
            <div class="text-center text-muted mt-2">
                Return after <span id="redirect-timer"><?php echo $this->redirectTimeout; ?></span> seconds.
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

    <?php
    if (isset($this->redirectTimeout) && $this->redirectTimeout > 0)
    {
        ?>
        <script>
            var redirectTimer = <?php echo $this->redirectTimeout; ?>;
            var timer = setInterval(function () {
                redirectTimer--;
                document.getElementById("redirect-timer").innerHTML = redirectTimer;
                if (redirectTimer <= 0) {
                    clearInterval(timer);
                    document.getElementById("form-history").submit();
                }
            }, 1000);
        </script>
        <?php
    } else {
        ?>
        <script>document.getElementById("form-history").submit();</script>
        <?php
    }
    ?>

</be-body>
