<be-center>
    <div id="app" v-cloak>

        <div class="be-mt-200">
            <el-input size="medium" v-model="url" clearable placeholder="指定网址"></el-input>
        </div>

        <div class="be-mt-200 be-ta-right">
            <el-button type="primary" icon="el-icon-check" @click="submit" :disabled="url === ''">确定</el-button>
        </div>
    </div>

    <script>
        let vueCenter = new Vue({
            el: '#app',
            data: {
                url: "",
                t: false
            },
            methods: {
                submit: function () {
                    parent.setMenuLink({
                        route: "",
                        params: {},
                        url: this.url,
                        description: "指定网址：" + this.url
                    });
                },
                t: function () {
                }
            },
        });
    </script>
</be-center>