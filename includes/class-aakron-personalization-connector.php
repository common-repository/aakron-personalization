<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Aakron_Personalization
 * @subpackage Aakron_Personalization/includes
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Aakron_Personalization {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Aakron_Personalization_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'AAKRON_PERSONALIZATION_VERSION' ) ) {
			$this->version = AAKRON_PERSONALIZATION_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'aakron-personalization';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Aakron_Personalization_Loader. Orchestrates the hooks of the plugin.
	 * - Aakron_Personalization_i18n. Defines internationalization functionality.
	 * - Aakron_Personalization_Admin. Defines all hooks for the admin area.
	 * - Aakron_Personalization_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aakron-personalization-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aakron-personalization-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-aakron-personalization-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-aakron-personalization-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-aakron-personalization-registration-form.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'sync/class-aakron-personalization-api-sync-products.php';

		$this->loader = new Aakron_Personalization_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Aakron_Personalization_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Aakron_Personalization_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Aakron_Personalization_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'aakron_design_tool_api_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'aakron_design_settings_init' );
		
		// load "aakron_design_sync_product" ajax action
		$this->loader->add_action( 'wp_ajax_aakron_design_sync_product', $plugin_admin, 'aakron_design_sync_product_call_back' );
        $this->loader->add_action( 'wp_ajax_nopriv_aakron_design_sync_product', $plugin_admin, 'aakron_design_sync_product_call_back' );

        $this->loader->add_action( 'wp_ajax_aakron_design_tool_verify_user', $plugin_admin, 'aakron_design_tool_verify_user_callback' );
        $this->loader->add_action( 'wp_ajax_nopriv_aakron_design_tool_verify_user', $plugin_admin, 'aakron_design_tool_verify_user_callback' );

        $this->loader->add_action( 'wp_ajax_aakron_design_tool_remove_user_token', $plugin_admin, 'aakron_design_tool_remove_user_token_callback' );
        $this->loader->add_action( 'wp_ajax_nopriv_aakron_design_tool_remove_user_token', $plugin_admin, 'aakron_design_tool_remove_user_token_callback' );

        $this->loader->add_action( 'wp_ajax_aakron_design_tool_user_email_validate', $plugin_admin, 'aakron_design_tool_user_email_validate_callback' );
        $this->loader->add_action( 'wp_ajax_nopriv_aakron_design_tool_user_email_validate', $plugin_admin, 'aakron_design_tool_user_email_validate_callback' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Aakron_Personalization_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// load "aakron_design_tool_validate_token" ajax action
		$this->loader->add_action( 'wp_ajax_aakron_design_tool_validate_token', $plugin_public, 'aakron_design_tool_validate_token_call_back' );
        $this->loader->add_action( 'wp_ajax_nopriv_aakron_design_tool_validate_token', $plugin_public, 'aakron_design_tool_validate_token_call_back' );

        // load "aakron_design_tool_validate_token" ajax action
        $this->loader->add_action( 'wp_ajax_aakron_design_tool_add_custom_data_to_cart', $plugin_public, 'aakron_design_tool_add_custom_data_to_cart_callback' );
        $this->loader->add_action( 'wp_ajax_nopriv_aakron_design_tool_add_custom_data_to_cart', $plugin_public, 'aakron_design_tool_add_custom_data_to_cart_callback' );

	}
    
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Aakron_Personalization_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}