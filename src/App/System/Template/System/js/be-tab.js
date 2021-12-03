
$(function () {
    $(".be-tab").each(function () {
        var $tab = $(this);
        var $tabNavItem = $(".be-tab-nav a", $tab);
        $tabNavItem.click(function () {
            $tabNavItem.removeClass("be-tab-nav-active");
            var $this = $(this);
            $this.addClass("be-tab-nav-active");

            $(".be-tab-pane", $tab).removeClass("be-tab-pane-active");
            $($this.data("be-target")).addClass("be-tab-pane-active");
            return false;
        });

        var $tabNavItemActive = $(".be-tab-nav .be-tab-nav-active", $tab);
        if (!$tabNavItemActive.length) {
            $tabNavItemActive = $(".be-tab-nav li:first-child", $tab);
            $tabNavItemActive.addClass("be-tab-nav-active");
        }

        if ($tabNavItemActive) {
            $tabNavItemActive.trigger("click");
        }
    })
})

