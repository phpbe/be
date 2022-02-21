window.be = {

    activeIframe: null,

    setActiveIframe: function (iframe) {
        this.activeIframe = iframe;
    },

    getActiveIframe: function (iframe) {
        return this.activeIframe;
    },

    openDialog: function (title, url, options = {}) {
        vueBe.openDialog(title, url, options);
    },

    closeDialog: function () {
        vueBe.closeDialog();
    },

    openDrawer: function (title, url, options = {}) {
        vueBe.openDrawer(title, url, options);
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
        openDialog: function (title, url, option = {}) {
            this.dialog.visible = true;

            this.dialog.title = title;

            if (option.hasOwnProperty("width")) {
                this.dialog.width = option.width;
            } else {
                let width = document.documentElement.clientWidth;
                this.dialog.width = width * 0.75 + "px";
            }

            if (option.hasOwnProperty("height")) {
                this.dialog.height = option.height;
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
            this.dialog.url = url;
        },

        closeDialog: function () {
            this.dialog.visible = false;
            this.dialog.url = "about:blank";
        },

        openDrawer: function (title, url, option) {
            this.drawer.visible = true;

            this.drawer.title = title;

            if (option.hasOwnProperty("width")) {
                this.drawer.width = option.width;
            } else {
                this.drawer.width = "40%";
            }

            url += url.indexOf("?") === -1 ? "?" : "&"
            url += "_=" + Math.random();
            this.drawer.url = url;
        },

        closeDrawer: function () {
            this.drawer.visible = false;
            this.drawer.url = "about:blank";
        },

    }
});