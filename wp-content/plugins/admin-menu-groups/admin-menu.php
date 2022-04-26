<?php


add_filter( 'parent_file', 'amg_organize_menu', 10, 1 );


function amg_organize_menu($value) {

    // Since WordPress has no hooks or filters to edit the admin menu we need to edit global variables directly
    // The parent_file filter is the best place to do this as it is called a single time right before
    // the WordPress admin menu is generated
    global $menu;
    global $submenu;
    global $self, $parent_file, $submenu_file, $plugin_page, $typenow;

    // Allow emegency hard reset (deletes all settings file)
    if(isset($_GET['amg_hard_reset'])) {
        amg_delete_options();
        return $parent_file;
    }

    // Allow emegency reset (e.g. if settings menu is hidden)
    if(isset($_GET['amg_reset'])) return $parent_file;

    // We'll need these for reference of the original menus once we've manipulated the menus already
    $original_menu = $menu;
    $original_submenu = $submenu;


    $groups_to_add = amg_get_groups_to_add();
    $hidden_items = amg_get_hidden_list();

    if(!is_array($hidden_items)) $hidden_items = array();

    amg_add_groups_to_menu($groups_to_add, $hidden_items);

    $groups = amg_get_groups_list();
    $groups = amg_rename_group_keys_with_slug($groups);


    amg_add_class_to_groups($groups);

    $submenu_item_positions = amg_get_sub_positions();

    $orphaned_items = [];

    // Create flat array of all menu item slugs in groups
    // So we don't have to loop through each group for each item to check if it is in the group
    $menu_group_slugs = [];
    foreach($groups as $group_index => &$group) {
        foreach($group as $group_item) {
            $menu_group_slugs[$group_item] = $group_index;
        }
    }


    // Loop through all items in the menu
    foreach($original_menu as $item_key => $item) {

        $slug = $item[2];

        $in_group = false;
        $this_group_slug = "";

        $admin_is_parent = false;
        $this_submenu = null;

        // Check if this item needs to be moved to a group
        if(isset( $menu_group_slugs[$slug])) {
            $in_group = true;

            // Store the group slug so that we know which group to move it into in the next stage
            $this_group_slug = $menu_group_slugs[$slug];
        }

        // Hide item if needed
        if(in_array($slug, $hidden_items)) {
            $menu[$item_key][4] .= " flag_hidden";
            $item[4] .= " flag_hidden";
        }


        if($in_group) {

            $this_position = $submenu_item_positions[$slug];

            if(!isset($item[6])) $item[6] = "";
            $img = amg_get_menu_item_image($item[6]);

            $item[0] = $img . $item[0];


            // Replace the class for this menu item
            // Needed since we don't want this now submenu item to be styled as a top menu item
            $item[4] = amg_get_menu_group_head_classes($item[4]);
            

            // Add a class with the id to set with js
            if(isset($item[5])) {
                $this_id = $item[5];
                $item[4] .= " js-id-$this_id";
            }

            if ( ( $parent_file && $item[2] == $parent_file ) || ( empty( $typenow ) && $self == $item[2] ) ) {
                $is_current_item = true;
                $item[4] .= " current";
            } else {
                $is_current_item = false;
            }


            // See if we have any submenu items to bring with
            if($slug && isset($original_submenu[$slug])) {
                $this_submenu = $original_submenu[$slug];
            }

            if( !$this_submenu ) {
                $item[4] .= " has-no-submenu";
            }

            // Add the original top level menu item to the submenu under the correct group
            $submenu[$this_group_slug][$this_position] = $item;
    
    
    
            if($this_submenu) {
                $i = 1;

                $submenu_count = count($this_submenu);

                // Add all the original submenu items under the newly created submenu item
                foreach($this_submenu as $subkey => $submenu_item) {

                    $i++;
                    $sub_item_position = $this_position . "." .strval(str_pad($i, 3, "0", STR_PAD_LEFT));

                    
                    if(!isset($menu_file) or !$menu_file) $menu_file = null;
                    $class           = array();

                    // Handle current for post_type=post|page|foo pages, which won't match $self.
                    $self_type = ! empty( $typenow ) ? $self . '?post_type=' . $typenow : 'nothing';
                    if ( isset( $submenu_file ) ) {
                        if ( $submenu_file == $submenu_item[2] ) {
                            $class[]          = 'current';
                        }
                        // If plugin_page is set the parent must either match the current page or not physically exist.
                        // This allows plugin pages with the same hook to exist under different parents.
                    } elseif (
                        ( ! isset( $plugin_page ) && $self == $submenu_item[2] ) ||
                        ( isset( $plugin_page ) && $plugin_page == $submenu_item[2] && ( $item[2] == $self_type || $item[2] == $self || file_exists( $menu_file ) === false ) )
                    ) {
                        $class[]          = 'current';
                    }
                    if ( ! empty( $submenu_item[4] ) ) {
                        $class[] = esc_attr( $submenu_item[4] );
                    }

                    if($is_current_item) {
                        $class[] = "wp-submenu-group-item-current";
                    }

                    if($i == 2) {
                        $class[] = "wp-submenu-group-item-first";
                    }
                    if($i == $submenu_count + 1) {
                       $class[] = "wp-submenu-group-item-last";
                    }

                    $class[] = "wp-submenu-group-item";

                    $submenu_class = $class ? ' ' . join( ' ', $class ) : '';


                    $sub_item_url = amg_get_sub_item_url($item, $submenu_item, $admin_is_parent);
                    $submenu_item[2] = $sub_item_url;
                                        
                    $submenu_item[4] = $submenu_class;

                    // Add the submenu item to the menu
                    $submenu[$this_group_slug][$sub_item_position] = $submenu_item;


                    // Remove the original submenu item
                    unset($submenu[$slug][$subkey]);
                }

                ksort($submenu[$this_group_slug]);
                
            }

            // Remove the menu item from the top level menu (it is now a submenu item)
            unset($menu[$item_key]);

        } else {

            // If the item is not in a group and not a group itself
            // Item is orphaned and hasn't been sorted yet
            // Add to list of items to append to group
            if(!array_key_exists($slug, $groups)) {
                $orphaned_items[] = $menu[$item_key];
                unset($menu[$item_key]);
            }
        }

   }


   // Sort menu according to settings
   $menu = amg_sort_menu($menu);


   // Add any orphaned items to end of menu
   $position = 1000;

   foreach($orphaned_items as $orphaned_item) {
       $menu[$position] = $orphaned_item;

       $position++;
   }


   get_admin_page_parent();
   return $parent_file;
    
}

function amg_get_group_slug_prefix() {
    return "admin.php?page=groups&group=";
}

function amg_get_sub_positions() {
    $item_positions = array();    

    $order = amg_get_menu_option()['order'];

    $i = 0;

    if(!$order) $order = array();

    foreach($order as $item) {

        if(isset($item['subItems'])) {
            $this_items = $item['subItems'];

            foreach($this_items as $sub_item) {
                $i++;
                $item_positions[$sub_item] = $i;
            }

            
        }
    }

    return $item_positions;
}

function amg_sort_menu($menu) {

    $new_menu = array();    

    $order = amg_get_menu_option()['order'];

    if(!$order) $order = array();

    $menu_by_slug = array();

    foreach($menu as $menu_item) {
        $menu_by_slug[$menu_item[2]] = $menu_item;
    }

    foreach($order as $menu_item) {
        $this_slug = str_replace("~", amg_get_group_slug_prefix(), $menu_item['slug']);

        if(isset($menu_by_slug[$this_slug])) {
            $new_menu[] =  $menu_by_slug[$this_slug];
        }

    }

    return $new_menu;
}


function amg_get_sub_item_url($item, $submenu_item, $admin_is_parent) {

    // Some submenu items have a short slug instead of a url
    // The menu works out the url for these items when it is generated
    // However, since the submenu items now have a different parent item (the group which it is under)
    // WordPress does not work out the url correctly
    // We therefore move the logic to determine the url for a submenu item directly into here
    // and pass the already worked out url
    $menu_file = $item[2];
    if ( false !== ( $pos = strpos( $menu_file, '?' ) ) ) {
        $menu_file = substr( $menu_file, 0, $pos );
    }

    $menu_hook = get_plugin_page_hook( $submenu_item[2], $item[2] );
    $sub_file  = $submenu_item[2];
    if ( false !== ( $pos = strpos( $sub_file, '?' ) ) ) {
        $sub_file = substr( $sub_file, 0, $pos );
    }

    if ( ! empty( $menu_hook ) || ( ( 'index.php' != $submenu_item[2] ) && file_exists( WP_PLUGIN_DIR . "/$sub_file" ) && ! file_exists( ABSPATH . "/wp-admin/$sub_file" ) ) ) {
        // If admin.php is the current page or if the parent exists as a file in the plugins or admin dir
        if ( ( ! $admin_is_parent && file_exists( WP_PLUGIN_DIR . "/$menu_file" ) && ! is_dir( WP_PLUGIN_DIR . "/{$item[2]}" ) ) || file_exists( $menu_file ) ) {
            $sub_item_url = add_query_arg( array( 'page' => $submenu_item[2] ), $item[2] );
        } else {
            $sub_item_url = add_query_arg( array( 'page' => $submenu_item[2] ), 'admin.php' );
        }
        $sub_item_url = esc_url( $sub_item_url );
    } else {
        $sub_item_url = $submenu_item[2];
    }

    return $sub_item_url;
}

function amg_get_menu_group_head_classes($class) {
    $class = str_replace("menu-top", "", $class);
    $class = str_replace("menu-top-first", "", $class);
    $class = str_replace("-first", "", $class);
    $class = str_replace("-last", "", $class);
    $class .= " wp-submenu-group-head";

    return $class;
}

function amg_get_menu_item_image($img_info) {
    // Add the item icon to the group item
    $img       = $img_style = '';
    $img_class = ' dashicons-before';

    if ( ! empty( $img_info ) ) {
        $img = '<img src="' . $img_info . '" alt="" />';
        if ( 'none' === $img_info || 'div' === $img_info ) {
            $img = '<br />';
        } elseif ( 0 === strpos( $img_info, 'data:image/svg+xml;base64,' ) ) {
            $img       = '<br />';
            $img_style = ' style="background-image:url(\'' . esc_attr( $img_info ) . '\')"';
            $img_class = ' svg';
        } elseif ( 0 === strpos( $img_info, 'dashicons-' ) ) {
            $img       = '<br />';
            $img_class = ' dashicons-before ' . sanitize_html_class( $img_info );
        }
    }

    
    return "<div class='wp-menu-image{$img_class}'$img_style>$img</div>";
}


function amg_rename_group_keys_with_slug($groups) {
    foreach($groups as $groupkey => $group) {
        $groupkeynew = str_replace("~", amg_get_group_slug_prefix(), $groupkey);

        if($groupkeynew != $groupkey) {
            $groups[$groupkeynew] = $group;
            unset($groups[$groupkey]);
        }
    }

    return $groups;
}

function amg_add_class_to_groups($groups) {
    global $menu;
    global $submenu;

    foreach($groups as $group_index => $group) {

        // Only need to add class to already exisiting items
        if(strpos($group_index, amg_get_group_slug_prefix()) !== false) continue;

        foreach($menu as &$menu_item) {
            // Add the group class so that the submenus are processed
            if($menu_item[2] == $group_index) {
                $menu_item[4] .= " menu-top-group";
            }
        }
    }
}

function amg_add_groups_to_menu($groups, $hidden_items) {
    global $menu;
    global $submenu;

    foreach($groups as $group_index => &$group) {

        $hidden_class = "";

        $group['slug'] = str_replace("~", amg_get_group_slug_prefix(), $group_index);

        if(in_array($group['slug'], $hidden_items)) {
            $hidden_class = " flag_hidden";
        }

        $menu_item = [
            $group['name'],
            "read",
            $group['slug'],
            "",
            "menu-top menu-top-group menu-top-native-group $hidden_class",
            "menu-group-$group_index",
            $group['icon'],
        ];


        $menu[] = $menu_item;

        // Also create a submenu holder for all the submenu items of this group
        $submenu[$group['slug']] = [];

    }

}

function amg_delete_options() {
    delete_option( 'amg_options' );
}

function amg_get_default_option() {
    return [
        "groups" => [],
        "hidden" => [],
        "order" => [],
    ];
}

function amg_get_menu_option() {
    $options = get_option( 'amg_options' );

    if(!is_array($options)) {
        return amg_get_default_option();
    }

    $field_menu_order = $options['amg_field_menu_order'];

    if($field_menu_order) {
        $decoded_options = json_decode($field_menu_order, true);

        if($decoded_options && is_array($decoded_options)) {
           return $decoded_options; 
        }
    }

    return amg_get_default_option();
}

function amg_get_groups_to_add() {
    $groups = array();

    $grouplist = amg_get_menu_option()['groups'];
    if(!is_array($grouplist)) return $groups;


    foreach($grouplist as $group) {
        $slug = $group['slug'];
        $groups[$slug] = $group;
    }

    return $groups;
}

function amg_get_hidden_list() {
    $hidden = amg_get_menu_option()['hidden'];

    return $hidden;
}

function amg_get_groups_list() {
    $order = amg_get_menu_option()['order'];

    $groups = array();

    if(!$order) $order = array();

    foreach($order as $item) {
        $this_slug = $item['slug'];

        $this_slug = str_replace(amg_get_group_slug_prefix(), '~', $this_slug);

        if(isset($item['subItems'])) {
            $this_items = $item['subItems'];

            $this_sub_items = array();

            foreach($this_items as $sub_item) {
                $this_sub_items[] = $sub_item;
            }

            $groups[$this_slug] = $this_sub_items;
        }
        
        
    }

    return $groups;
}

add_action( 'admin_enqueue_scripts', 'amg_admin_scripts' );

function amg_admin_scripts() {
    // Scripts for editing the actual menu - include on all pages
    wp_enqueue_style( 'amg_admin_menu_style', plugins_url('css/admin-menu.css', __FILE__) );
    wp_enqueue_script( 'amg_admin_menu_script', plugins_url('js/admin-menu.js', __FILE__) );    
}