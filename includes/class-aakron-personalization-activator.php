<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Aakron_Personalization
 * @subpackage Aakron_Personalization/includes
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Aakron_Personalization_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if( !class_exists( 'WooCommerce' ) ) {
	        deactivate_plugins( plugin_basename( __FILE__ ) );
	        wp_die( __( 'Please install and Activate WooCommerce. <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">Click Here</a>', 'woocommerce-aakron-design-tool' ), 'Plugin dependency check', array( 'back_link' => true ) );
	    }

	    if( !class_exists( 'WC_Dynamic_Pricing' ) ) {
	        deactivate_plugins( plugin_basename( __FILE__ ) );
	        wp_die( __( 'Please install and Activate WooCommerce Dynamic Pricing. <a href="https://docs.woocommerce.com/document/woocommerce-dynamic-pricing/" target="_blank">Click Here</a>', 'woocommerce-aakron-design-tool' ), 'Plugin dependency check', array( 'back_link' => true ) );
	    }
	}

}
