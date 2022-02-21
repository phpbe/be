window.be = {

    activeIframe: null,

    setActiveIframe: function (iframe) {
        this.activeIframe = iframe;
    },

    getActiveIframe: function (iframe) {
        return this.activeIframe;
    },

    openDialog: function (url, options) {
        vueBe.openDialog(url, options);
    },

    closeDialog: function () {
        vueBe.closeDialog();
    },

    openDrawer: function (url, options) {
        vueBe.openDrawer(url, options);
    },

    closeDrawer: function () {
        vueBe.closeDrawer();
    },
}


let vueBe = new Vue({
    el: '#app-be',
    data: {
        dialog: {visible: false, title: "", width: "", height: ""},
        drawer: {visible: false, title: "", width: ""}
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

            var eForm = document.createElement("form");
            eForm.action = url;
            eForm.target = "frame-be-dialog";
            document.body.appendChild(eForm);

            setTimeout(function () {
                eForm.submit();
            }, 50);

            setTimeout(function () {
                document.body.removeChild(eForm);
            }, 3000);
        },

        closeDialog: function () {
            this.dialog.visible = false;
        },

        openDrawer: function (title, url, option) {
            this.drawer.visible = true;

            this.drawer.title = title;

            if (option.hasOwnProperty("width")) {
                this.drawer.width = option.width;
            } else {
                this.drawer.width = "40%";
            }

            var eForm = document.createElement("form");
            eForm.action = url;
            eForm.target = "frame-be-drawer";
            document.body.appendChild(eForm);

            setTimeout(function () {
                eForm.submit();
            }, 50);

            setTimeout(function () {
                document.body.removeChild(eForm);
            }, 3000);
        },

        closeDrawer: function () {
            this.drawer.visible = false;
        },

    }
});