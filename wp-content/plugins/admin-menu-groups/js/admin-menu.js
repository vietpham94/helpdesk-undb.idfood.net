jQuery(window).load(function() {

    jQuery("#adminmenumain .menu-top-group").each(function() {
        var topgroup = jQuery(this);
        var topgroupSubmenu = topgroup.find(".wp-submenu");
        //jQuery(this).after("<div class='wp-submenu-group-sub-container'></div>");

        topgroup.find("li[class*='js-id']").each(function() {
            if( ! jQuery(this).attr("id") ) {

                var classNames = this.className.split(/\s+/);
                var newId = "";

                jQuery.each(classNames, function(i, name) {
                    if (name.indexOf('js-id') !== -1) {

                        newId = name.replace("js-id-", "");
                        return false;
                    }
                });

                jQuery(this).attr("id", newId);
            }
        });

        topgroup.find("li.wp-submenu-group-head").each(function() {
            var containerEl = jQuery("<li class='wp-submenu-group-container'></li>");
            var submenuContainer = jQuery("<ul class='wp-submenu-group-sub-container'></ul>");
            var this_submenu = jQuery(this).nextUntil('.wp-submenu-group-head', '.wp-submenu-group-item'); 

            jQuery(this).wrap(containerEl);
            containerEl = jQuery(this).closest(".wp-submenu-group-container");

            var hassubmenu = false;

            if(this_submenu.length > 0) {
                submenuContainer.append(this_submenu);
                containerEl.append(submenuContainer);

                hassubmenu = true;
            } else {
                
                jQuery(this).addClass("nosubmenu");
                jQuery(this).closest("li").addClass("nosubmenu");
            }

            if(this_submenu.find(".current.wp-submenu-group-item").length > 0) {
                jQuery(this).addClass("current");
                jQuery(this).closest("li").addClass("current");
                topgroup.addClass("wp-has-current-submenu").removeClass("wp-not-current-submenu");
                topgroup.find("a.menu-top").addClass("wp-has-current-submenu").removeClass("wp-not-current-submenu");
            }
            
            containerEl.on("click mouseenter", function(event) {

                jQuery(".wp-submenu-group-sub-container").removeClass("show");
                
                if(hassubmenu) {
                    submenuContainer.addClass("show");
                    submenuContainer.removeClass("bottom-pos");

                    var elementinviewport = true;

                    var submenuHeight = submenuContainer.height();
                    var submenuTop = submenuContainer.offset().top;
                    var submenuBottomPos = submenuHeight + submenuTop;

                    var windowHeight = window.innerHeight;
                    var windowScroll = jQuery(window).scrollTop();
                    var windowBottomPos = window.innerHeight + windowScroll;

                    if(submenuBottomPos > windowBottomPos) elementinviewport = false;
                        

                    if(!elementinviewport) {
                        submenuContainer.addClass("bottom-pos");
                    }

                }
                
            });
            jQuery(this).find("a").on("click", function(event) {
                if(hassubmenu) {
                    if(jQuery("html").hasClass("screen-xs") ) {
                        jQuery(this).parent().click();
                        event.preventDefault();
                    }
                }
            })

            containerEl.on("mouseleave", function() {
                submenuContainer.removeClass("show");
            });

            //topgroupSubmenu.append(containerEl);
        });
    });

    jQuery("body").addClass("js-admin-menu-loaded");
});




