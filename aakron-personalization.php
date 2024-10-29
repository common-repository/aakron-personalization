<?php

/**
 *
 * @since             1.0.0
 * @package           Aakron_Personalization
 *
 * Plugin Name:       AAkron Personalization
 * Plugin URI:        https://www.flowz.com/
 * Description:       This Plugin is used to connect wordpress to our product design tool.
 * Version:           1.0.0
 * Author:            Flowz Digital
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       aakron-personalization
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'AAKRON_PERSONALIZATION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-aakron-personalization-activator.php
 */
function activate_aakron_personalization() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aakron-personalization-activator.php';
	Aakron_Personalization_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-aakron-personalization-deactivator.php
 */
function deactivate_aakron_personalization() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aakron-personalization-deactivator.php';
	Aakron_Personalization_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_aakron_personalization' );
register_deactivation_hook( __FILE__, 'deactivate_aakron_personalization' );

function aakron_personalization_add_roles_on_plugin_activation() {
   $roleCapabilities = get_role("shop_manager");
   add_role( 
   	'aakron_design_tool_user', 
   	'Aakron Design Tool User',
   	$roleCapabilities
   );

}
register_activation_hook( __FILE__, 'aakron_personalization_add_roles_on_plugin_activation' );

/*
* The function to create table in databse for log data
* table product_sync_log
*/
function aakron_personalization_sync_log_table_create() {
     global $wpdb;
     $table_name = $wpdb->prefix . 'product_sync_log';
     $wpdb_collate = $wpdb->collate;
     $sql =
         "CREATE TABLE {$table_name} (
         id mediumint(8) unsigned NOT NULL auto_increment ,
         created_date varchar(255) NULL,
         file varchar(255) NULL,
         download_url varchar(255) NULL,
         PRIMARY KEY  (id),
         KEY created_date (created_date),
         KEY file (file),
         KEY download_url (download_url)
         )
         COLLATE {$wpdb_collate}";
 
     require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
     dbDelta( $sql );
}
register_activation_hook( __FILE__, 'aakron_personalization_sync_log_table_create' );

/*
* The function to add purchase order number option
* aakron_add_purchase_order_number
*/
function aakron_add_purchase_order_number(){
  // add option in wp "aakron_purchase_order_number" & "aakron_design_tool_access_toekn"
  // The option hasn't been created yet, so add it with $autoload set to 'no'.
  $deprecated = null;
  $autoload = 'no';

  $aakron_purchase_order_number       = 'aakron_purchase_order_number';
  $purchaseOrderNumber = get_option($aakron_purchase_order_number);
  if( $purchaseOrderNumber == false ){
    $aakron_purchase_order_number_value = 8000001;
    add_option( $aakron_purchase_order_number, $aakron_purchase_order_number_value, $deprecated, $autoload );
  }
}
register_activation_hook( __FILE__, 'aakron_add_purchase_order_number' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-aakron-personalization-connector.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_aakron_personalization() {

	$plugin = new Aakron_Personalization();
	$plugin->run();

}
run_aakron_personalization();