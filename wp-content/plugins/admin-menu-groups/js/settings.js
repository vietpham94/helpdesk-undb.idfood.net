function getMenuOrder() {

    var adminMenu = [];

    jQuery("#AdminMenuGroupsOrderSort > li").each(function() {
        var thisSlug = jQuery(this).data("slug");
        
        var subItems = [];

        jQuery(this).find("li").each(function() {
            var thisSubSlug = jQuery(this).attr("data-slug");
            subItems.push(thisSubSlug);
        })

        var thisItem = {};
        thisItem.slug = thisSlug;
        thisItem.subItems = subItems;

        adminMenu.push(thisItem);
    });

    return adminMenu;

}
function getHiddenItems() {
    var adminMenuHidden = [];

    jQuery("#AdminMenuGroupsOrderSort li.flag_hidden").each(function() {
        
        var slug = jQuery(this).data("slug");

        adminMenuHidden.push(slug);
    });

    return adminMenuHidden;
}

function getMenuGroups() {

    var adminMenuGroups = [];

    jQuery("#AdminMenuGroupsOrderSort li.menu-top-native-group").each(function() {
        
        var thisItem = {};
        thisItem.icon = jQuery(this).find(".icon").val();
        thisItem.name = jQuery(this).find(".menu-item-title-text").val();
        thisItem.slug = jQuery(this).data("slug");

        adminMenuGroups.push(thisItem);
    });

    return adminMenuGroups;

}


function getSettings() {
    var settings = {};

    settings.order = getMenuOrder();
    settings.groups = getMenuGroups();
    settings.hidden = getHiddenItems();

    var settingsString = JSON.stringify(settings);
    jQuery("#AdminMenuGroupsOrderInput").val(settingsString);
}

jQuery(window).load(function() {
    

    function applySortable() {
        jQuery(".ui-sortable.admin-menu").sortable({
            connectWith: ".ui-sortable.admin-menu",
            placeholder: "ui-state-highlight",
            stop: function(ev, ui) {

                var item = ui.item;

                if(item.hasClass("menu-top-native-group")) {
                    if(item.closest('.admin-menu-submenu').length) {
                        jQuery(this).sortable('cancel');
                    }
                }
            },

            over: function(ev, ui) {
                var item = ui.item;

                

                if(item.hasClass("menu-top-native-group")) {

                    ui.placeholder.addClass("is-group-drag");
                } else {

                    ui.placeholder.removeClass("is-group-drag");
                }
            },
            out: function(ev, ui) {
                ui.placeholder.removeClass("is-group-drag");
            },

            update: function(event, ui) {
                

                getSettings()
            },
        }) //.disableSelection();
    }

    applySortable();

    jQuery("#AdminGroupsAdd").find("button.add").click(function() {

        var addForm = jQuery(this).closest("#AdminGroupsAdd");
        var icon = addForm.find("input[name='icon']").val();
        var name = addForm.find("input[name='name']").val();
        var unique = Math.random() * (999999 - 100000) + 100000;
        unique = (unique+"").replace(".", "");
        var slug = ("~" + name.toLowerCase() + "-").replace(" ", "_") + unique;

        

        jQuery("#AdminMenuGroupsOrderSort").append("<li data-slug='"+slug+"' class='menu-top-native-group'>       <div class='menu-item-bar'>            <div class='menu-item-handle'>                <span class='item-title'><span class='menu-item-title'><div class='icon-picker wp-menu-image'><input id='dashicons_picker_icon_"+unique+"' class='icon' type='hidden' name='icon' value='"+icon+"' /><div id='dashicons_picker_preview_"+unique+"' class='dashicons "+icon+"'> </div><input id='dashicons_picker_button_"+unique+"' class='button dashicons-picker' type='button' data-preview='#dashicons_picker_preview_"+unique+"' value='Change' title='Change Icon' data-target='#dashicons_picker_icon_"+unique+"' /></div><input type='text' class='menu-item-title-text' value='"+name+"'></span> <span class='is-submenu' style='display: none;'>sub item</span></span>                <span class='item-controls'><span class='item-type'></span><button type='button' class='button delete'><div class='dashicons dashicons-trash'> </div></button> <button type='button' class='button hide'><div class='dashicons dashicons-hidden'> </div></button>              </span></div></div><ul class='ui-sortable admin-menu admin-menu-submenu'></ul></li>                            ");

        jQuery("#AdminMenuGroupsOrderSort").find("button.delete").click(function() {
            deleteCallback(jQuery(this));
        });
        jQuery("#AdminMenuGroupsOrderSort").find("button.hide").click(function() {
            hideCallback(jQuery(this));
        });

        jQuery( '#dashicons_picker_button_'+unique ).dashiconsPicker();

        addForm.find("input").val("");
        addForm.find(".dashicons").attr("class", "").addClass("dashicons");

        applySortable();

        getSettings()
    });

    function hideCallback(item) {
        var thisItem = item.closest("li");

        thisItem.addClass("flag_hidden");
        item.removeClass("hide").addClass("show");
        item.find(".dashicons").removeClass("dashicons-hidden").addClass("dashicons-visibility");

        item.click(function() {
            showCallback(jQuery(this));
        });
        
        getSettings();
    }

    function showCallback(item) {
        var thisItem = item.closest("li");

        thisItem.removeClass("flag_hidden");
        item.removeClass("show").addClass("hide");
        item.find(".dashicons").addClass("dashicons-hidden").removeClass("dashicons-visibility");

        item.click(function() {
            hideCallback(jQuery(this));
        });

        getSettings();
    }


    function deleteCallback(item) {
        var thisItem = item.closest("li");
        var thisSlug = thisItem.data("slug");

        thisItem.remove();

        var thisSubItems = thisItem.find(".admin-menu-submenu li");

        jQuery("#AdminMenuGroupsOrderSort").append(thisSubItems);
        thisItem.remove();
        
        getSettings();
    }
    jQuery("#AdminMenuGroupsOrderSort").find("button.hide").click(function() {
        hideCallback(jQuery(this));
    });
    jQuery("#AdminMenuGroupsOrderSort").find("button.show").click(function() {
        showCallback(jQuery(this));
    });

    jQuery("#AdminMenuGroupsOrderSort").find("button.delete").click(function() {
        deleteCallback(jQuery(this));
    });
    jQuery("#AdminMenuGroupsOrderSort .menu-item-title-text").on("blur keyup paste input", function() {        
        getSettings();
    });

    jQuery("#submit").click(function() {
        getSettings();
    });

    getSettings();
});

