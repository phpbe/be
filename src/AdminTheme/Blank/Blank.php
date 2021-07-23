<be-html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title><?php echo $this->title; ?></title>

    <script src="https://unpkg.com/vue@2.6.11/dist/vue.min.js"></script>

    <script src="https://unpkg.com/axios@0.19.0/dist/axios.min.js"></script>
    <script>Vue.prototype.$http = axios;</script>

    <script src="https://unpkg.com/vue-cookies@1.5.13/vue-cookies.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/element-ui@2.13.2/lib/theme-chalk/index.css">
    <script src="https://unpkg.com/element-ui@2.13.2/lib/index.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/font-awesome@4.7.0/css/font-awesome.min.css" />

    <style>
        body {background-color: #fff;}
        ::-webkit-scrollbar {width: 8px;}
        ::-webkit-scrollbar-thumb {background-color: #555;}
        [v-cloak] {display: none;}
        [class^="el-icon-fa"],
        [class*="el-icon-fa"] {
            display: inline-block;
            font-style: normal;
            font-variant: normal;
            font-weight: normal;
            font-family: FontAwesome!important;
            font-size: inherit;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
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