(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined'
        ? module.exports = factory()
        : typeof define === 'function' && define.amd
            ? define(factory)
            : (global = typeof globalThis !== 'undefined' ? globalThis : global || self,
                global.bytemdPluginBeStorage = factory());
})(this, function () {
    'use strict';

    // 全局状态
    window._bytemdBeStorage = {
        editor: null,        // CodeMirror 实例
        codemirror: null,    // CodeMirror 构造函数
        root: null,          // bytemd 根 DOM 元素
        mode: null
    };

    // 将选中的文件插入编辑器
    function insertFiles(ctx, files) {
        if (!files || files.length === 0) return;

        var cm = ctx.editor;
        var text = '';

        for (var i = 0; i < files.length; i++) {
            var f = files[i];

            if (ctx.mode === 'image') {
                var alt = f.name || '';
                if (f.hasOwnProperty('alt')) {
                    alt = f.alt;
                } else if (f.hasOwnProperty('title')) {
                    alt = f.title;
                }
                text += '![' + alt + '](' + f.url + ')\n';
            } else {
                var name = f.name || f.url;
                if (f.hasOwnProperty('title')) {
                    name = f.title;
                }
                text += '[' + name + '](' + f.url + ')\n';
            }
        }

        cm.replaceSelection(text.trim());
        cm.focus();
    }

    // 全局回调函数：图片选择完成（由 Storage.pop 确认后触发）
    window._bytemdBeStorageImageSelected = function (files) {
        var ctx = window._bytemdBeStorage;
        if (files && files.length > 0) {
            ctx.mode = 'image';
            insertFiles(ctx, files);
        }
        if (window.frameElement && window.frameElement.tagName == 'IFRAME') {
            parent.be.closeDialog();
        } else {
            be.closeDialog();
        }
    };

    // 全局回调函数：文件选择完成（由 Storage.pop 确认后触发）
    window._bytemdBeStorageFileSelected = function (files) {
        var ctx = window._bytemdBeStorage;
        if (files && files.length > 0) {
            ctx.mode = 'file';
            insertFiles(ctx, files);
        }
        if (window.frameElement && window.frameElement.tagName == 'IFRAME') {
            parent.be.closeDialog();
        } else {
            be.closeDialog();
        }
    };

    return function (config) {
        config = config || {};

        var imageUrl = config.imageUrl || '';
        var imageUrlIframe = config.imageUrlIframe || imageUrl;
        var fileUrl = config.fileUrl || '';
        var fileUrlIframe = config.fileUrlIframe || fileUrl;
        var insertImageLabel = config.insertImageLabel || '插入图片';
        var insertFileLabel = config.insertFileLabel || '插入文件';

        // 图片图标 SVG
        var imageIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="none" viewBox="0 0 48 48"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 10a2 2 0 0 1 2-2h34a2 2 0 0 1 2 2v28a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V10Z" clip-rule="evenodd"></path><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M14.5 18a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" clip-rule="evenodd"></path><path stroke="currentColor" stroke-linejoin="round" stroke-width="4" d="m15 24 5 4 6-7 17 13v4a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-4l10-10Z"></path></svg>';

        // 文件图标 SVG（IconPark Notes 风格，折角文件+内容线）
        var fileIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="none" viewBox="0 0 48 48"><path stroke="currentColor" stroke-linejoin="round" stroke-width="4" d="M10 4a2 2 0 0 0-2 2v36a2 2 0 0 0 2 2h28a2 2 0 0 0 2-2V14L30 4H10Z"/><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M30 4v10h10"/><path stroke="currentColor" stroke-linecap="round" stroke-width="4" d="M16 20h16M16 28h16"/></svg>';

        return {
            actions: [
                {
                    title: insertImageLabel,
                    icon: imageIcon,
                    handler: {
                        type: 'action',
                        click: function (context) {
                            var ctx = window._bytemdBeStorage;
                            ctx.editor = context.editor;
                            ctx.codemirror = context.codemirror;
                            ctx.root = context.root || null;
                            ctx.mode = 'image';
                            if (window.frameElement && window.frameElement.tagName == 'IFRAME') {
                                parent.be.setActiveIframe(window);
                                parent.be.openDialog('选择图片', imageUrlIframe);
                            } else {
                                be.openDialog('选择图片', imageUrl);
                            }
                        }
                    }
                },
                {
                    title: insertFileLabel,
                    icon: fileIcon,
                    handler: {
                        type: 'action',
                        click: function (context) {
                            var ctx = window._bytemdBeStorage;
                            ctx.editor = context.editor;
                            ctx.codemirror = context.codemirror;
                            ctx.root = context.root || null;
                            ctx.mode = 'file';
                            if (window.frameElement && window.frameElement.tagName == 'IFRAME') {
                                parent.be.setActiveIframe(window);
                                parent.be.openDialog('选择文件', fileUrlIframe);
                            } else {
                                be.openDialog('选择文件', fileUrl);
                            }
                        }
                    }
                }
            ]
        };
    };
});
