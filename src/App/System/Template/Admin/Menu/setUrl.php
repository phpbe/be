<be-page-content>
    <div class="be-bc-fff be-px-100 be-pt-100 be-pb-50" id="app" v-cloak>

        <div class="be-mt-200">
            <div class="be-row">
                <div class="be-col-auto be-lh-250">
                    网址格式：
                </div>
                <div class="be-col be-lh-250 be-pl-200">
                    <el-radio v-model="mode" label="route" @change="toggleMode('route')">路由+参数</el-radio>
                    <el-radio v-model="mode" label="url" @change="toggleMode('url')">绝对网址</el-radio>
                </div>
            </div>
        </div>

        <div class="be-mt-200" v-if="mode === 'route'">
            <div class="be-row">
                <div class="be-col-auto be-lh-250">
                    <span class="be-c-red">*</span> 路由：
                </div>
                <div class="be-col be-pl-200">
                    <el-input size="medium" v-model.trim="route" placeholder="路由"></el-input>
                </div>
            </div>

            <div class="be-row be-mt-200">
                <div class="be-col-auto be-lh-250">
                    &nbsp;&nbsp;&nbsp;参数：
                </div>
                <div class="be-col be-pl-200">
                    <div class="be-row be-mb-50" v-for="item in params">
                        <div class="be-col-auto">
                            <el-input size="medium" v-model.trim="item.name" placeholder="参数名"></el-input>
                        </div>
                        <div class="be-col-auto be-px-100 be-lh-250">:</div>
                        <div class="be-col-auto">
                            <el-input size="medium" v-model.trim="item.value" placeholder="参数值"></el-input>
                        </div>
                        <div class="be-col-auto be-pl-100 be-lh-250">
                            <el-link type="danger" icon="el-icon-delete" @click="deleteParam(item)"></el-link>
                        </div>
                    </div>

                    <el-button size="medium" type="primary" icon="el-icon-plus" @click="addParam">增加参数</el-button>
                </div>
            </div>
        </div>

        <div class="be-mt-200" v-if="mode === 'url'">
            <div class="be-row">
                <div class="be-col-auto be-lh-250">
                    <span class="be-c-red">*</span> 绝对网址：
                </div>
                <div class="be-col be-pl-200">
                    <el-input size="medium" v-model.trim="url" placeholder="绝对网址"></el-input>
                </div>
            </div>
        </div>

        <div class="be-mt-200 be-ta-right">
            <el-button type="primary" icon="el-icon-check" @click="submit" :disabled="(mode === 'route' && route === '') || ( mode === 'url' && url === '')">确定</el-button>
        </div>
    </div>

    <script>
        let vueCenter = new Vue({
            el: '#app',
            data: {
                mode: "route",
                route: "",
                params: [],
                url: "",
                t: false
            },
            methods: {
                toggleMode: function (mode) {
                    this.mode = mode;
                },
                addParam: function () {
                    this.params.push({
                        name: "",
                        value: ""
                    });
                },
                deleteParam: function (item) {
                    this.params.splice(this.params.indexOf(item), 1);
                },
                submit: function () {
                    if (this.mode === "route") {
                        let params = {};
                        for (let item of this.params) {
                            if (item.name !== "" && item.value !== "") {
                                params[item.name] = item.value;
                            }
                        }

                        parent.setMenuLink({
                            route: this.route,
                            params: params,
                            url: "",
                            description: "指定网址：" + this.route + "..."
                        });
                    } else {
                        parent.setMenuLink({
                            route: "",
                            params: {},
                            url: this.url,
                            description: "指定网址：" + this.url
                        });
                    }

                },
                t: function () {
                }
            },
        });
    </script>
</be-page-content>