
if (window.$cookies.isKey('be-admin-west-collapse') && window.$cookies.get('be-admin-west-collapse') === '1') {
    document.getElementById("be-middle").style.marginLeft = "64px";
    document.getElementById("be-west").style.width = "64px";
    document.getElementById("be-north").style.left = "64px";
}




window.be = {

    activeIframe: null,

    setActiveIframe: function (iframe) {
        this.activeIframe = iframe;
    },

    getActiveIframe: function (iframe) {
        return this.activeIframe;
    },

    openDialog: function (title, url, options = {}, formData = null) {
        vueBe.openDialog(title, url, options, formData);
    },

    closeDialog: function () {
        vueBe.closeDialog();
    },

    openDrawer: function (title, url, options = {}, formData = null) {
        vueBe.openDrawer(title, url, options, formData);
    },

    closeDrawer: function () {
        vueBe.closeDrawer();
    },
}

let vueBe = new Vue({
    el: '#app-be',
    data: {
        dialog: {visible: false, title: "", url: "about:blank", width: "", height: ""},
        drawer: {visible: false, title: "", url: "about:blank", width: ""}
    },
    methods: {
        openDialog: function (title, url, options = {}, formData = null) {
            this.dialog.visible = true;
            this.dialog.title = title;

            if (options.hasOwnProperty("width")) {
                this.dialog.width = options.width;
            } else {
                let width = document.documentElement.clientWidth;
                this.dialog.width = width * 0.75 + "px";
            }

            if (options.hasOwnProperty("height")) {
                this.dialog.height = options.height;
            } else {
                let clientHeight = document.documentElement.clientHeight;
                let height = clientHeight * 0.75;
                if (height < 400) {
                    height = clientHeight * 0.95;
                }
                this.dialog.height = height + "px";
            }

            url += url.indexOf("?") === -1 ? "?" : "&"
            url += "_=" + Math.random();

            if (formData === null) {
                this.dialog.url = url;
            } else {
                let form = document.createElement("form");
                form.action = url;
                form.target = "frame-be-dialog";
                form.method = "post";
                form.style.display = "none";

                var e = document.createElement("textarea");
                e.name = 'data';
                e.value = JSON.stringify(formData);
                form.appendChild(e);

                document.body.appendChild(form);

                setTimeout(function () {
                    form.submit();
                }, 50);

                setTimeout(function () {
                    document.body.removeChild(form);
                }, 3000);
            }
        },

        closeDialog: function () {
            this.dialog.visible = false;
            this.dialog.url = "about:blank";
        },

        openDrawer: function (title, url, options, formData = null) {
            this.drawer.visible = true;
            this.drawer.title = title;

            if (options.hasOwnProperty("width")) {
                this.drawer.width = options.width;
            } else {
                this.drawer.width = "40%";
            }

            url += url.indexOf("?") === -1 ? "?" : "&"
            url += "_=" + Math.random();

            if (formData === null) {
                this.drawer.url = url;
            } else {
                let form = document.createElement("form");
                form.action = url;
                form.target = "frame-be-drawer";
                form.method = "post";
                form.style.display = "none";

                var e = document.createElement("textarea");
                e.name = 'data';
                e.value = JSON.stringify(formData);
                form.appendChild(e);

                document.body.appendChild(form);

                setTimeout(function () {
                    form.submit();
                }, 50);

                setTimeout(function () {
                    document.body.removeChild(form);
                }, 3000);
            }
        },

        closeDrawer: function () {
            this.drawer.visible = false;
            this.drawer.url = "about:blank";
        },

    }
});
