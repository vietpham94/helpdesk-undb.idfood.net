<?php
/*
Plugin Name: 	Advance HelpDesk Search
Version: 		1.0.0
Description: 	Form search advance and search result page
Author:         DIG Co.,ltd
Author URI:     https://helpdesk-undb.idfood.net/
Plugin URI:     https://helpdesk-undb.idfood.net/
Text Domain: 	advance-search-helpdesk
Requires PHP:   7.0
*/

/**
 * On activation plugin
 */
register_activation_hook(__FILE__, 'on_activation');
function on_activation()
{

}

/**
 * On deactivation plugin
 */
register_deactivation_hook(__FILE__, 'on_deactivation');
function on_deactivation()
{

}

// Include functions
include_once(plugin_dir_path(__FILE__) . 'ash-shortcode.php');
include_once(plugin_dir_path(__FILE__) . 'ash-widgets.php');
include_once(plugin_dir_path(__FILE__) . 'WC_REST_Advance_Search_Helpdesk_Controller.php');