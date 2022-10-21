<be-center>
    <form id="form" action="<?php echo \Be\Be::getRequest()->getUrl(); ?>" method="post">
        <?php
        foreach ($this->appProperties as $appProperty)
        {
            ?>
            <div class="be-row be-mt-200 be-bt-eee be-pt-100">
                <div class="be-col-0 be-md-col-3 be-lg-col-6"></div>
                <div class="be-col-24 be-md-col-18 be-lg-col-12">
                    <div class="be-row">
                        <div class="be-col-auto">
                            <input type="checkbox" class="be-checkbox" name="names[]" id="name-<?php echo $appProperty['name']; ?>" <?php echo $appProperty['name'] === 'System' ? 'onchange="this.checked=true"': '' ?> value="<?php echo $appProperty['name']; ?>" checked>
                        </div>
                        <div class="be-col-auto">
                            <div class="be-pl-100">
                                <label for="name-<?php echo $appProperty['name']; ?>">
                                    <?php echo $appProperty['label']; ?> （<?php echo $appProperty['name']; ?> ）：
                                </label>
                            </div>
                        </div>
                        <div class="be-col be-c-666">
                            <label for="name-<?php echo $appProperty['name']; ?>">
                                <?php echo $appProperty['description']; ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="be-col-0 be-md-col-3 be-lg-col-6"></div>
            </div>
            <?php
        }
        ?>

        <div class="be-mt-200 be-bt-eee be-pt-100 be-ta-center">
            <a class="be-btn" href="<?php echo beAdminUrl('System.Installer.configDb'); ?>">上一步</a>
            <input type="button" class="be-btn be-btn-major" value="继续安装" onclick="installApp()">
        </div>

    </form>

    <script>
        function installApp() {
            $.ajax({
                url: "<?php echo beAdminUrl('System.Installer.installApp'); ?>",
                data : $("#form").serialize(),
                method: "POST",
                success: function (json) {
                    if (json.success) {
                        window.location.href=json.redirectUrl;
                    } else {
                        alert(json.message);
                    }
                },
                error: function () {
                    alert("系统错误!");
                }
            });
        }
    </script>
</be-center>
