(function() {

    var factory = function (exports) {

		var pluginName   = "be-link-dialog";

        window.beLink = {};
        beLink.dialog = null;
        beLink.selectFile = function (files) {
            if (files.length === 0) {
                return;
            }

            let file = files[0];

            beLink.dialog.find("[data-url]").val(file.url);

            let title = file.name;
            if (file.hasOwnProperty("title")) {
                title = file.title;
            }

            beLink.dialog.find("[data-title]").val(title);

            be.closeDialog();
        };

		exports.fn.beLinkDialog = function() {

			var _this       = this;
			var cm          = this.cm;
            var editor      = this.editor;
            var settings    = this.settings;
            var selection   = cm.getSelection();
            var lang        = this.lang;
            var linkLang    = lang.dialog.link;
            var classPrefix = this.classPrefix;
			var dialogName  = classPrefix + pluginName, dialog;

			cm.focus();

            if (editor.find("." + dialogName).length > 0)
            {
                dialog = editor.find("." + dialogName);
                dialog.find("[data-url]").val("http://");
                dialog.find("[data-title]").val(selection);

                this.dialogShowMask(dialog);
                this.dialogLockScreen();
                dialog.show();
            }
            else
            {
                var dialogHTML = "<div class=\"" + classPrefix + "form\">" + 
                                        "<label>" + linkLang.url + "</label>" + 
                                        "<input type=\"text\" value=\"http://\" data-url />" +
                                        "<div class=\"" + classPrefix + "file-input\">" +
                                        "<input type=\"button\" class='editormd-btn' onclick='be.openDialog(\"浏览\", \"" + settings.be_storage_url +"\");' value=\"浏览\" />" +
                                        "</div>" +
                                        "<br/>" + 
                                        "<label>" + linkLang.urlTitle + "</label>" + 
                                        "<input type=\"text\" value=\"" + selection + "\" data-title />" + 
                                        "<br/>" +
                                    "</div>";

                dialog = this.createDialog({
                    title      : linkLang.title,
                    width      : 465,
                    height     : 230,
                    content    : dialogHTML,
                    mask       : settings.dialogShowMask,
                    drag       : settings.dialogDraggable,
                    lockScreen : settings.dialogLockScreen,
                    maskStyle  : {
                        opacity         : settings.dialogMaskOpacity,
                        backgroundColor : settings.dialogMaskBgColor
                    },
                    buttons    : {
                        enter  : [lang.buttons.enter, function() {
                            var url   = this.find("[data-url]").val();
                            var title = this.find("[data-title]").val();

                            if (url === "http://" || url === "")
                            {
                                alert(linkLang.urlEmpty);
                                return false;
                            }

                            /*if (title === "")
                            {
                                alert(linkLang.titleEmpty);
                                return false;
                            }*/
                            
                            var str = "[" + title + "](" + url + " \"" + title + "\")";
                            
                            if (title == "")
                            {
                                str = "[" + url + "](" + url + ")";
                            }                                

                            cm.replaceSelection(str);

                            this.hide().lockScreen(false).hideMask();

                            return false;
                        }],

                        cancel : [lang.buttons.cancel, function() {                                   
                            this.hide().lockScreen(false).hideMask();

                            return false;
                        }]
                    }
                });

                beLink.dialog = dialog;
			}
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
