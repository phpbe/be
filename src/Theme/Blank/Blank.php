<be-html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $this->title; ?></title>
    <?php
    $beUrl = beUrl();
    ?>
    <base href="<?php echo $beUrl; ?>/">
    <script>var beUrl = "<?php echo $beUrl; ?>"; </script>

    <script src="https://cdn.jsdelivr.net/npm/jquery@1.12.4/dist/jquery.min.js"></script>

    <link rel="stylesheet" href="<?php echo $beUrl; ?>/vendor/be/scss/src/be.css" />

    <style type="text/css">
        html {
            font-size: 14px;
            background-color: #fff;
            color: #333;
        }

        a {
            color:  #1f7df8;
        }

        a:hover {
            color: #f60;
        }
    </style>

    <be-head>
    </be-head>
</head>
<body>
    <be-body>
    <div class="be-body">
        <be-center>
        </be-center>
    </div>
    </be-body>
</body>
</html>
</be-html>