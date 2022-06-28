
var DrawerMenu = {

    init: function () {
        $(".drawer-menu-lv1-item-with-dropdown").click(function () {
            $(this).addClass("js-open-drawer-menu-lv1");
        });

        $(".drawer-menu-lv1-dropdown-title").click(function () {
            $(this).closest(".drawer-menu-lv1-item-with-dropdown").removeClass("js-open-drawer-menu-lv1");
            return false;
        });
    },

    toggle: function () {
        if ($("html").hasClass("js-open-drawer-menu")) {
            $("html").removeClass("js-open-drawer").removeClass("js-open-drawer-menu");
        } else {
            $("html").addClass("js-open-drawer").addClass("js-open-drawer-menu");
        }
        return false;
    },

    show: function () {
        $("html").addClass("js-open-drawer").addClass("js-open-drawer-menu");
        return false;
    },

    hide: function () {
        $("html").removeClass("js-open-drawer").removeClass("js-open-drawer-menu");
        return false;
    }
};

$(function () {
    DrawerMenu.init();
});

