(function() {

    var factory = function (exports) {

		var pluginName   = "be-image-dialog";

        window.beImage = {};
        beImage.dialog = null;
        beImage.selectImage = function (files) {
            if (files.length === 0) {
                return;
            }

            let file = files[0];

            beImage.dialog.find("[data-url]").val(file.url);

            let alt = file.name;
            if (file.hasOwnProperty("alt")) {
                alt = file.alt;
            } else {
                if (file.hasOwnProperty("title")) {
                    alt = file.title;
                }
            }

            beImage.dialog.find("[data-alt]").val(alt);

            be.closeDialog();
        };

		exports.fn.beImageDialog = function() {
            var _this       = this;
            var cm          = this.cm;
            var lang        = this.lang;
            var editor      = this.editor;
            var settings    = this.settings;
            var cursor      = cm.getCursor();
            var selection   = cm.getSelection();
            var imageLang   = lang.dialog.image;
            var classPrefix = this.classPrefix;
			var dialogName  = classPrefix + pluginName, dialog;

			cm.focus();

            if (editor.find("." + dialogName).length < 1)
            {
                var guid   = (new Date).getTime();
                var dialogContent = "<div class=\"" + classPrefix + "form\">" +
                                    "<label>" + imageLang.url + "</label>" +
                                    "<input type=\"text\" data-url />" +
                                    "<div class=\"" + classPrefix + "file-input\">" +
                                    "<input type=\"button\" class='editormd-btn' onclick='be.openDialog(\"浏览\", \"" + settings.be_storage_url_filter_image +"\");' value=\"浏览\" />" +
                                    "</div>" +
                                    "<br/>" +
                                    "<label>" + imageLang.alt + "</label>" +
                                    "<input type=\"text\" value=\"" + selection + "\" data-alt />" +
                                    "<br/>" +
                                    "<label>" + imageLang.link + "</label>" +
                                    "<input type=\"text\" value=\"http://\" data-link />" +
                                    "<br/>" +
                                    "</div>";

                dialog = this.createDialog({
                    title      : imageLang.title,
                    width      : 465,
                    height     : 280,
                    name       : dialogName,
                    content    : dialogContent,
                    mask       : settings.dialogShowMask,
                    drag       : settings.dialogDraggable,
                    lockScreen : settings.dialogLockScreen,
                    maskStyle  : {
                        opacity         : settings.dialogMaskOpacity,
                        backgroundColor : settings.dialogMaskBgColor
                    },
                    buttons : {
                        enter : [lang.buttons.enter, function() {
                            var url  = this.find("[data-url]").val();
                            var alt  = this.find("[data-alt]").val();
                            var link = this.find("[data-link]").val();

                            if (url === "")
                            {
                                alert(imageLang.imageURLEmpty);
                                return false;
                            }

							var altAttr = (alt !== "") ? " \"" + alt + "\"" : "";

                            if (link === "" || link === "http://")
                            {
                                cm.replaceSelection("![" + alt + "](" + url + altAttr + ")");
                            }
                            else
                            {
                                cm.replaceSelection("[![" + alt + "](" + url + altAttr + ")](" + link + altAttr + ")");
                            }

                            if (alt === "") {
                                cm.setCursor(cursor.line, cursor.ch + 2);
                            }

                            this.hide().lockScreen(false).hideMask();

                            //删除对话框
                            this.remove();

                            return false;
                        }],

                        cancel : [lang.buttons.cancel, function() {
                            this.hide().lockScreen(false).hideMask();

                            //删除对话框
                            this.remove();

                            return false;
                        }]
                    }
                });

                dialog.attr("id", classPrefix + "image-dialog-" + guid);

                beImage.dialog = dialog;
            }

			dialog = editor.find("." + dialogName);
			dialog.find("[type=\"text\"]").val("");
			dialog.find("[type=\"file\"]").val("");
			dialog.find("[data-link]").val("http://");

			this.dialogShowMask(dialog);
			this.dialogLockScreen();
			dialog.show();

		};

	};

	// CommonJS/Node.js
	if (typeof require === "function" && typeof exports === "object" && typeof module === "object")
    {
        module.exports = factory;
    }
	else if (typeof define === "function")  // AMD/CMD/Sea.js
    {
		if (define.amd) { // for Require.js

			define(["editormd"], function(editormd) {
                factory(editormd);
            });

		} else { // for Sea.js
			define(function(require) {
                var editormd = require("../../editormd");
                factory(editormd);
            });
		}
	}
	else
	{
        factory(window.editormd);
	}

})();
