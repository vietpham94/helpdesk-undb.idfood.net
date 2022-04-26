<?php

add_action( 'admin_enqueue_scripts', 'amg_admin_settings_scripts' );

function amg_admin_settings_scripts($hook) {

    if($hook != 'settings_page_admin-menu-groups') {
            return;
    }

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-draggable' );
    wp_enqueue_script( 'jquery-ui-droppable' );
    wp_enqueue_script( 'jquery-ui-mouse' );
    wp_enqueue_script( 'jquery-ui-widget' );
    wp_enqueue_script( 'jquery-ui-sortable' );
    
    $css = plugin_dir_url( __FILE__ ) . 'css/dashicons-picker.css';
    wp_enqueue_style( 'dashicons-picker', $css, array( 'dashicons' ), '1.0' );

	$js = plugin_dir_url( __FILE__ ) . 'js/dashicons-picker.js';
    wp_enqueue_script( 'dashicons-picker', $js, array( 'jquery' ), '1.0' );
    
    wp_enqueue_script( 'amg_settings_script', plugins_url('js/settings.js', __FILE__) );
    wp_enqueue_style( 'amg_settings_style', plugins_url('css/settings.css', __FILE__) );
    
}

add_action( 'admin_init', 'amg_settings_init' );

function amg_settings_init() {
    // register a new setting for "wporg" page
    register_setting( 'amg', 'amg_options' );
    
    // register a new section in the "wporg" page
    add_settings_section(
        'amg_section_edit_menu_order',
        __( 'Edit Menu Order', 'amg' ),
        'amg_section_edit_menu_order_cb',
        'amg'
    );
    
    add_settings_field(
        'amg_field_menu_order', // as of WP 4.6 this value is used only internally
        // use $args' label_for to populate the id inside the callback
        __( 'Menu Order', 'amg' ),
        'amg_field_menu_order_cb',
        'amg',
        'amg_section_edit_menu_order',
        [
            'label_for' => 'amg_field_menu_order',
            'class' => 'amg_row',
            'amg_custom_data' => 'custom',
        ]
    );

}


function amg_section_edit_menu_order_cb( $args ) {

}

function amg_field_menu_order_cb( $args ) {
    global $menu, $submenu;

    // get the value of the setting we've registered with register_setting()
    $options = get_option( 'amg_options' );
    // output the field
    ?>
    <input type='hidden'
    data-custom="<?php echo esc_attr( $args['amg_custom_data'] ); ?>"
    name="amg_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
    value="<?php echo esc_attr( $options[$args['label_for']] ); ?>"
    id='AdminMenuGroupsOrderInput'
    >

    <div class='admin-menu-groups'>
        <ul class='ui-sortable admin-menu admin-menu-top' id='AdminMenuGroupsOrderSort'>
        <?php 

            $i = 0;

            foreach($menu as $menu_key => $menu_item) {

                $i++;

                $title = preg_replace("/<span.*\<\/span>/", "", $menu_item[0]);
                if(!isset($menu_item[6])) $menu_item[6] = "";
                $img = amg_get_menu_item_image($menu_item[6]);

                $class = $menu_item[4];
                if(strpos($class, "menu-top-native-group") === false) {
                    $is_group = false;
                } else {
                    $is_group = true;
                }

                if(strpos($class, "flag_hidden") === false) {
                    $is_hidden = false;
                } else {
                    $is_hidden = true;
                }
                
                ?>
                <li data-slug='<?php echo $menu_item[2]; ?>' class='<?php echo $menu_item[4]; ?>'>
                <div class="menu-item-bar">
                    <div class="menu-item-handle">
                        <span class="item-title"><span class="menu-item-title">
                        <?php if($is_group): ?>
                            <div class='icon-picker wp-menu-image'>
                                <input id="dashicons_picker_icon_<?php echo $i; ?>" class='icon' type="hidden" name='icon' value='<?php echo $menu_item[6]; ?>' />
                                <div id='dashicons_picker_preview_<?php echo $i; ?>' class='dashicons <?php echo $menu_item[6]; ?>'> </div>
                                
                                <input class="button dashicons-picker" type="button" data-preview='#dashicons_picker_preview_<?php echo $i; ?>'  value='Change' title='Change Icon' data-target="#dashicons_picker_icon_<?php echo $i; ?>" />
                            </div>
                            <input class='menu-item-title-text' type='text' value='<?php echo $title; ?>'>

                        <?php else: ?>
                            <?php echo $img; ?>
                            <span class='menu-item-title-text'><?php echo $title; ?></span>
                        <?php endif; ?>
                        </span>
                        <span class="item-controls">
                            <span class="item-type"></span>
                            <?php if($is_group): ?>
                                <button type='button' class='button delete'><div class='dashicons dashicons-trash'> </div></button>
                            <?php endif; ?>

                            <button type='button' class='button <?php if($is_hidden): ?>show<?php else: ?>hide<?php endif; ?>'><div class='dashicons dashicons-<?php if($is_hidden): ?>visibility<?php else: ?>hidden<?php endif; ?>'> </div></button>
                            
                        </span>
                    </div>
                </div>


                <ul class='ui-sortable admin-menu admin-menu-submenu'>


                <?php

                if(isset($submenu[$menu_item[2]])) {
                    $this_submenu = $submenu[$menu_item[2]];

                    foreach($this_submenu as $submenu_item_key => $submenu_item) {
                        if(!isset($submenu_item[4]) || strpos($submenu_item[4], "wp-submenu-group-head") === FALSE) continue;

                        $class = $submenu_item[4];

                        if(strpos($class, "flag_hidden") === false) {
                            $is_hidden = false;
                        } else {
                            $is_hidden = true;
                        }
                        
                        $title = strip_tags(preg_replace("/<span.*\<\/span>/", "", $submenu_item[0]));
                        if(!isset($submenu_item[6])) $submenu_item[6] = "";
                        $img = amg_get_menu_item_image($submenu_item[6]);
                        ?>
                        <li data-slug='<?php echo $submenu_item[2]; ?>' class='<?php echo $class; ?>'>
                        <div class="menu-item-bar">
                            <div class="menu-item-handle">
                                <span class="item-title"><span class="menu-item-title"><?php echo $img; ?><?php echo $title; ?></span> </span>
                                <span class="item-controls">
                                    <span class="item-type"></span>
                                    <button type='button' class='button <?php if($is_hidden): ?>show<?php else: ?>hide<?php endif; ?>'><div class='dashicons dashicons-<?php if($is_hidden): ?>visibility<?php else: ?>hidden<?php endif; ?>'> </div></button>
                                </span>
                            </div>
                        </div>
                        
                        <ul class='ui-sortable admin-menu admin-menu-submenu'>

                        </ul>
                        <?php

                        echo "</li>";
                    }

                }
                ?>

                </ul>

                </li>
                <?php
            }   
        ?>
        </ul>
        <div id='AdminGroupsAdd'>
                <div class="menu-item-bar">
                    <div class="menu-item-handle">
                        <span class="item-title"><span class="menu-item-title">
                            <div class='icon-picker wp-menu-image'>
                                <input id="dashicons_picker_icon_add" class='icon' type="hidden" name='icon' value='dashicons-menu' />
                                <div id='dashicons_picker_preview_add' class='dashicons dashicons-menu'> </div>
                                
                                <button class="button dashicons-picker" type="button" data-preview='#dashicons_picker_preview_add' data-target="#dashicons_picker_icon_add" >Select Icon</button>
                            </div>
                            <input type='text' name='name' class='menu-item-title-text'></span> </span>
                        <span class="item-controls">
                            <span class="item-type"></span>
                            <button type='button' class='button add button-primary'>Add Group</button>
                        </span>
                    </div>
                </div>
        </div>
    </div>
    <?php 
}


add_action('admin_menu', 'amg_create_menu');

function amg_create_menu() {
    add_options_page( 'Admin Menu Groups', 'Admin Menu Groups', 'manage_options', 'admin-menu-groups', 'amg_settings_menu'); 
}

function amg_settings_menu() {
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
        // add settings saved message with the class of "updated"
        add_settings_error( 'amg_messages', 'amg_message', __( 'Settings Saved', 'amg' ), 'updated' );
    }
    
    // show error/update messages
    //settings_errors( 'amg_messages' );
    ?>
    <div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <form action="options.php" method="post" class='admin-menu-settings-form'>
    <?php
    // output security fields for the registered setting "wporg"
    settings_fields( 'amg' );
    // output setting sections and their fields
    // (sections are registered for "wporg", each field is registered to a specific section)
    do_settings_sections( 'amg' );
    // output save settings button
    submit_button( 'Save Settings' );
    ?>
    </form>
    </div>
    <?php
   
}