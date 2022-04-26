=== Admin Menu Groups ===
Contributors: chaimc
Tags: admin, menu
Requires at least: 4.0.1
Tested up to: 5.7.2
Requires PHP: 5.2.4
Stable tag: 0.1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Create groups in the admin menu
 
== Description ==

Admin Menu Groups allows you to create nested menus in the WordPress admin sidebar navigation and organize all menu
items in groups as needed.
 
A few notes about the sections above:
 
*   Create groups to organize the admin menu
*   Put less frequently used or advanced options in their own group
*   Hide unused items in the admin menu
*   Group related menu items


== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Setup your menu on the Admin Menu Groups settings page
 
== Frequently Asked Questions ==

= How do I organize my admin menu? =
After installing the plugin, navigate to the Admin Menu Groups settings page and reorder, hide or create groups as needed

= I hid the Admin Menu Groups item from the menu, how do I get it back? =
You can access the Admin Menu Groups settings page directly at `https://example.com/wp-admin/options-general.php?page=admin-menu-groups`

You can temporarily show all menu items in their original state by using the query string `?amg_reset`.
e.g. `https://example.com/wp-admin/index.php?amg_reset`

You can delete the entire Admin Menu Groups configuration by using the query string `?amg_hard_reset`.
e.g. `https://example.com/wp-admin/index.php?amg_hard_reset`


== Changelog ==

= 0.1.3 =
* Beta compatibility with newer versions of WordPress and PHP. Recommended to clear settings for the plugin.

= 0.1.2 =
* Resolve warnings in debug mode

= 0.1.1 =
* Initial release.