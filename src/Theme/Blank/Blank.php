<be-html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $this->title; ?></title>
    <base href="<?php echo beUrl(); ?>/">
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

    <link rel="stylesheet" href="https://cdn.phpbe.com/ui/be.css" />
    <link rel="stylesheet" href="https://cdn.phpbe.com/ui/be-icons.css"/>

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
            color: #ff5c35;
        }
    </style>

    <be-head>
    </be-head>
</head>
<body>
    <be-body>
        <be-middle>
            <be-center>
                <be-page-content></be-page-content>
            </be-center>
        </be-middle>
    </be-body>
</body>
</html>
</be-html>