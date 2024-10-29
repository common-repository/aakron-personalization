<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Aakron_Personalization
 * @subpackage Aakron_Personalization/admin
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Aakron_Personalization_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->init();   
    }

    /*
     * init() callback to register routes
     * callback function
    */
    public function init(){
        add_action( 'add_meta_boxes', array( $this, 'aakron_design_tool_register_meta_boxes' ) );
        add_action( 'woocommerce_admin_order_item_values', array( $this, 'aakron_design_tool_order_itemline_artwork' ), 10, 3 );
        add_action( 'woocommerce_admin_order_item_headers', array( $this, 'aakron_design_tool_admin_order_item_headers' ), 55 );
        add_filter( 'woocommerce_locate_template', array( $this, 'aakron_design_tool_woo_template_override' ), 1, 3 );
    }


    /**
     * Register meta box(es).
     */
    public function aakron_design_tool_register_meta_boxes() {
        add_meta_box( 'meta-box-id-dynamic-pricing', __( 'AAkronline Pricing' ), array( $this, 'aakran_dynamic_oricing_callback' ), 'product' );
    }
    
    /**
     * Meta box display callback.
     *
     * @param WP_Post $post Current post object.
     */
    public function aakran_dynamic_oricing_callback( $post ) {
        $aakranPricingObj = get_post_meta( $post->ID, 'pricing', TRUE );
        $aakranPricingArr = json_decode($aakranPricingObj, true);
        ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th><label for="from">From</label></th>
                    <th><label for="from">To</label></th>
                    <th><label for="from">Rate</label></th>
                    <th><label for="from">Code</label></th>
                </tr>
                <?php
                    foreach ($aakranPricingArr['decorative'] as $aakranPricing) {
                        if( $aakranPricing['to'] == null ){
                            $aakranPricing['to'] = '*';
                        }
                ?>
                    <tr>
                        <td><?php echo esc_attr($aakranPricing['from']) ;?></td>
                        <td><?php echo esc_attr($aakranPricing['to'] ) ;?></td>
                        <td><?php echo esc_attr($aakranPricing['rate']) ;?></td>
                        <td><?php echo esc_attr($aakranPricing['code']) ;?></td>
                    </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>
    <?php 
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Aakron_Personalization_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Aakron_Personalization_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/aakron-personalization-admin.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Aakron_Personalization_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Aakron_Personalization_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        
        wp_enqueue_script( 'aakron_design_tool_script' );
        
        wp_register_script( 'aakron_design_admin_js', plugin_dir_url( __FILE__ ) . 'js/aakron-personalization-admin.js', array( 'jquery' ), rand(1,99999999999), false  );
        // Localize the script with new data
        $translation_array = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'update_api_nonce' => wp_create_nonce( 'update-api-key' ),
            'clipboard_failed' => esc_html__( 'Copying to clipboard failed. Please press Ctrl/Cmd+C to copy.', 'woocommerce' ),
        );
        wp_localize_script( 
            'aakron_design_admin_js', 
            'aakron_design_admin_obj', 
            $translation_array 
        );

        // Enqueued script with localized data.
        wp_enqueue_script( 'aakron_design_admin_js' );
    }

    /**
     * @return string
     */
    public function aakron_design_tool_api_menu()
    {
        /**
         * top level menu
         */
            // add top level menu page
            add_menu_page(
                'Design Tool',
                'Design Tool',
                'manage_options',
                'aakron_design_tool', array($this, 'aakron_design_tool_options_page_html'),
                'dashicons-image-filter'
            );
            // add sub menu page
            add_submenu_page( 
                'aakron_design_tool', 
                'Registration', 
                'Registration',
                'manage_options', 
                'aakron_design_tool_registration', array($this, 'aakron_design_tool_registration_page_html')
            );
            // add sub menu page
            add_submenu_page( 
                'aakron_design_tool', 
                'Settings', 
                'Settings',
                'manage_options', 
                'aakron_design_tool_settings', array($this, 'aakron_design_tool_options_page_settings')
            );  
    }

    /**
     * register our aakron_design_settings_init to the admin_init action hook
     */

    function aakron_design_settings_init() {
        // register a new setting for "aakron_design_tool" page
        register_setting( 'aakron_design_tool', 'aakron_design_options' );

        // register a new setting for "aakron_design_tool_settings" page
        register_setting( 'aakron_design_tool_settings', 'aakron_design_settings_options' );

        // register a new section in the "aakron_design_tool" page
        add_settings_section(
            'aakron_design_section_email',
            __( 'Email Settings', 'aakron_design_tool' ),
            array($this, 'aakron_design_section_email_cb'),
            'aakron_design_tool'
        );

        // register settings field "aakron_product_design_tool_email"
        add_settings_field('aakron_product_design_tool_email',
            __( 'Email (Production Aakronline) ', 'aakron_design_tool' ),
            array($this,'aakron_product_design_tool_email_cb'),
            'aakron_design_tool',
            'aakron_design_section_email',
            [
                'label_for' => 'aakron_product_design_tool_email',
                'class' => 'aakron_design_row',
                'aakron_design_custom_data' => 'custom',
            ]
        );

         // register a new section in the "aakron_design_tool" page
        add_settings_section(
            'aakron_design_section_social',
            __( 'Social Media Settings', 'aakron_design_tool' ),
            array($this, 'aakron_design_section_social_cb'),
            'aakron_design_tool'
        );


        // register settings field "aakron_product_design_tool_facebook_url"
        add_settings_field('aakron_product_design_tool_facebook_url',
            __( 'Facebook', 'aakron_design_tool' ),
            array($this,'aakron_product_design_tool_facebook_url_cb'),
            'aakron_design_tool',
            'aakron_design_section_social',
            [
                'label_for' => 'aakron_product_design_tool_facebook_url',
                'class' => 'aakron_design_row',
                'aakron_design_custom_data' => 'custom',
            ]
        );

        // register settings field "aakron_product_design_tool_facebook_url"
        add_settings_field('aakron_product_design_tool_linkedin_url',
            __( 'Linkedin', 'aakron_design_tool' ),
            array($this,'aakron_product_design_tool_linkedin_url_cb'),
            'aakron_design_tool',
            'aakron_design_section_social',
            [
                'label_for' => 'aakron_product_design_tool_linkedin_url',
                'class' => 'aakron_design_row',
                'aakron_design_custom_data' => 'custom',
            ]
        );

        // register settings field "aakron_product_design_tool_facebook_url"
        add_settings_field('aakron_product_design_tool_instagram_url',
            __( 'Instagram', 'aakron_design_tool' ),
            array($this,'aakron_product_design_tool_instagram_url_cb'),
            'aakron_design_tool',
            'aakron_design_section_social',
            [
                'label_for' => 'aakron_product_design_tool_instagram_url',
                'class' => 'aakron_design_row',
                'aakron_design_custom_data' => 'custom',
            ]
        );

        // register settings field "aakron_product_design_tool_facebook_url"
        add_settings_field('aakron_product_design_tool_twitter_url',
            __( 'Twitter', 'aakron_design_tool' ),
            array($this,'aakron_product_design_tool_twitter_url_cb'),
            'aakron_design_tool',
            'aakron_design_section_social',
            [
                'label_for' => 'aakron_product_design_tool_twitter_url',
                'class' => 'aakron_design_row',
                'aakron_design_custom_data' => 'custom',
            ]
        );

        // register settings field "aakron_product_design_tool_facebook_url"
        add_settings_field('aakron_product_design_tool_copyright_text',
            __( 'Copyright Text', 'aakron_design_tool' ),
            array($this,'aakron_product_design_tool_copyright_text_cb'),
            'aakron_design_tool',
            'aakron_design_section_social',
            [
                'label_for' => 'aakron_product_design_tool_copyright_text',
                'class' => 'aakron_design_row',
                'aakron_design_custom_data' => 'custom',
            ]
        );

        // register a new section in the "aakron_design_section_registration" page
        add_settings_section(
            'aakron_design_section_registration',
            __( 'Registration', 'aakron_design_tool_registration' ),
            array($this, 'aakron_design_section_registration_cb'),
            'aakron_design_tool_registration'
        );
    }

    /**
     * @param $args
     */
    function aakron_design_section_developers_cb( $args ) {
        ?>
        <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Set up callback APIs.', 'aakron_design_tool' ); ?></p>
        <?php
    }

    /**
     * @param $args
     */
    function aakron_design_section_registration_cb( $args ) {
        ?>
        <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Set up callback APIs.', 'aakron_design_tool_registration' ); ?></p>
        <?php
    }

    /**
     * @param $args
     */
    function aakron_design_product_list_api_cb( $args ){
        $options = get_option( 'aakron_design_options' );
        ?>
        <input style="width:80%;" type="text" name="aakron_design_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr($options[ $args['label_for'] ]); ?>">
        <?php
    }

    /**
     * @param $args
     */
    function aakron_product_design_tool_email_cb( $args ){
        $options = get_option( 'aakron_design_options' );
        ?>
        <input style="width:80%;" type="text" name="aakron_design_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr($options[ $args['label_for'] ]); ?>">
        <?php
    }

    /**
     * @param $args
     */
    function aakron_design_section_social_cb( $args ) {
        ?>
        <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( '', 'aakron_design_tool' ); ?></p>
        <?php
    }

    /**
     * @param $args
     */
    function aakron_design_section_email_cb( $args ) {
        ?>
        <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( '', 'aakron_design_tool' ); ?></p>
        <?php
    }
    

    /**
     * @param $args
     */
    function aakron_product_design_tool_facebook_url_cb( $args ){
        $options = get_option( 'aakron_design_options' );
        ?>
        <input style="width:80%;" type="text" name="aakron_design_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr($options[ $args['label_for'] ]); ?>">
        <?php
    }

    /**
     * @param $args
     */
    function aakron_product_design_tool_linkedin_url_cb( $args ){
        $options = get_option( 'aakron_design_options' );
        ?>
        <input style="width:80%;" type="text" name="aakron_design_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr($options[ $args['label_for'] ]); ?>">
        <?php
    }

    /**
     * @param $args
     */
    function aakron_product_design_tool_instagram_url_cb( $args ){
        $options = get_option( 'aakron_design_options' );
        ?>
        <input style="width:80%;" type="text" name="aakron_design_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr( $options[ $args['label_for'] ] ); ?>">
        <?php
    }

    /**
     * @param $args
     */
    function aakron_product_design_tool_twitter_url_cb( $args ){
        $options = get_option( 'aakron_design_options' );
        ?>
        <input style="width:80%;" type="text" name="aakron_design_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr( $options[ $args['label_for'] ] ); ?>">
        <?php
    }

    /**
     * @param $args
     */
    function aakron_product_design_tool_copyright_text_cb( $args ){
        $options = get_option( 'aakron_design_options' );
        ?>
        <input style="width:80%;" type="text" name="aakron_design_options[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr( $options[ $args['label_for'] ] ); ?>">
        <?php
    }
    

    /**
     * top level menu:
     * callback function "for Design Tool"
     */
    function aakron_design_tool_options_page_html() {
        // check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
    ?>
    <div class="aakron-design-tool-main">
        <h3>AAkron Personalization PlugIn Product Sync from API</h3>
        <div id="select-sync-type">
            <label class="container" id="js_all">All
              <input type="radio" checked="checked" name="radio">
              <span class="checkmark"></span><span style="font-size: 14px;padding: 0 10px;"> (Sync All Products From API.)</span>
            </label>
            <label class="container" id="js_sku">Product SKUs
              <input type="radio" name="radio"><span style="font-size: 14px;padding: 0 10px;"> (Sync Products with Provided SKUs From API.)</span>
              <span class="checkmark"></span>
            </label>
            <input id="sku_list" value="" placeholder="&nbsp;Comma Seperated Sku List of Product for eg. 1234,4567">
            <!-- Image loader -->
            <div id="js_overlay_spinner" style="display: none;">
                <div class="overlay__wrapper">
                    <div class="overlay__spinner">
                          <img src='<?php echo plugin_dir_url( __FILE__ ) . '/images/sync-loader.gif'; ?>'>
                    </div>
                </div>
            </div>
            <!-- Image loader -->
            <span class="error-message"></span>
        </div><br/>
        <input type='button'  name='sync_now' value='Sync Products' class='button sync_now button-primary'>
        <div class='sync-header'></div><div class='sync-log'></div><div id='sync-error' class='sync-error' style='display: none;'></div></div>
        
        <div class="aakron-design-tool-main sync-table">
            <h3>AAkron Personalization PlugIn Product Sync Log</h3>
            <div class="aakron_product_sync_log">
                <?php
                    if ( defined( 'AAKRON_PERSONALIZATION_VERSION' ) ) {
                        $this->version = AAKRON_PERSONALIZATION_VERSION;
                    } else {
                        $this->version = '1.0.0';
                    }
                    $this->plugin_name = 'aakron-personalization';
                    $syncLogs = new Aakron_Design_Api_Sync_Products( $this->plugin_name, $this->version );
                    $result = $syncLogs->syncLogsTbale();
                    echo json_encode($result);
                    die();
                ?>
            </div>
        </div>
    <?php
    }

    /**
     * top level menu:
     * callback function "for Design Tool Settings"
     */
    function aakron_design_tool_options_page_settings() {
        // check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $options        = get_option( 'aakron_design_options' );
        if ( isset( $_GET['settings-updated'] ) ) {
            add_settings_error( 'aakron_design_messages', 'aakron_design_message', __( 'Settings Saved', 'aakron_design' ), 'updated' );
        }
        // show error/update messages
        settings_errors( 'aakron_design_messages' );
        ?>
        <div class="aakron-design-tool-main setings-pages">
            <h3><?php echo esc_html( get_admin_page_title() ); ?></h3>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'aakron_design_tool' );
                do_settings_sections( 'aakron_design_tool' );
                submit_button( 'Save Settings' );
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * top level menu:
     * callback function for "aakron Design Tool Registration"
     */
    function aakron_design_tool_registration_page_html() {
        // check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        
        //echo "<h3>aakron Design Tool Registration</h3>";
        if ( defined( 'AAKRON_PERSONALIZATION_VERSION' ) ) {
            $this->version = AAKRON_PERSONALIZATION_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'aakron-personalization';
        $registrationForm = new Aakron_Personalization_Registration_Form( $this->plugin_name, $this->version );
        $registrationFormHtml = $registrationForm->registrationFormHtml();
    }

    /*
     * Product Data Sync
     * callback function
    */
    public function aakron_design_sync_product_call_back(){

        if ( defined( 'AAKRON_PERSONALIZATION_VERSION' ) ) {
            $this->version = AAKRON_PERSONALIZATION_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $aakronDesignToolUser = get_users( [ 'role__in' => [ 'aakron_design_tool_user' ] ] );
        if( get_option('aakron_design_tool_access_toekn') !== false ){
            $userDesignToolAccessToken  = get_option( 'aakron_design_tool_access_toekn' );
        }

        if( !empty($aakronDesignToolUser) && !empty($userDesignToolAccessToken) ){
            $this->plugin_name = 'aakron-personalization';
            $Sync_Products = new Aakron_Design_Api_Sync_Products( $this->plugin_name, $this->version );
            $result = $Sync_Products->syncProducts();
            echo json_encode($result);
        }else{
            $data_response = "<p style='font-size: 14px; line-height: 1;'>Please Register &Activate you account first in order to Import Products from API.</p>";
            $status = 0;
            $result['type'] = ($status == 0 ? "failed" : "success");
            $result['data'] = $data_response;
            echo json_encode($result);
        }        
        die();
    }

    /*
     * user token validation
     * callback function
    */
    public function aakron_design_tool_verify_user_callback(){
        $options                    = get_option( 'aakron_design_options' );
        $request_uri                = 'https://designtoolapi.aakronline.com';
        $requestUri                 = $request_uri.'/api/registration/validateToken';
        if( isset($_POST['userAccessToken']) ){
            $userDesignToolAccessToken      = sanitize_text_field( $_POST['userAccessToken']);
            if( !empty($userDesignToolAccessToken) ){
                $argsApi = [
                    'headers' => array( 'Authorization' => 'Bearer ' . $userDesignToolAccessToken ),
                    'timeout'     => 120,
                    'redirection' => 10,
                    'blocking'    => true,
                    'httpversion' => '1.0',
                    'sslverify'   => false,
                ];
                $response = wp_remote_get($requestUri,$argsApi);
                $tokenValidateRepsponseJson = wp_remote_retrieve_body( $response );
                $tokenValidateRepsponseArr = json_decode($tokenValidateRepsponseJson, TRUE);
                if($tokenValidateRepsponseArr['response']['responseCode'] == 200){
                    $activationStatus = $tokenValidateRepsponseArr['response']['data']['activationStatus'];
                    $option_design_tool_status       = 'is_design_tool_active' ;
                    $option_design_tool_access_token = 'aakron_design_tool_access_toekn' ;
                    if ( get_option( $option_design_tool_status ) !== false && get_option( $option_design_tool_access_token ) !== false ) {
                        // The option already exists, so update it.
                        // update option in wp "is_design_tool_active" & "aakron_design_tool_access_toekn"
                        update_option( $option_design_tool_status, $activationStatus );
                        update_option( $option_design_tool_access_token, $userDesignToolAccessToken );      
                    } else { 
                        // The option hasn't been created yet, so add it with $autoload set to 'no'.
                        $deprecated = null;
                        $autoload = 'no';
                        // add option in wp "is_design_tool_active" & "aakron_design_tool_access_toekn"
                        add_option( $option_design_tool_status, $activationStatus, $deprecated, $autoload );
                        add_option( $option_design_tool_access_token, $userDesignToolAccessToken, $deprecated, $autoload );
                    }

                    $data_response = "<div class='validate-success'><p> Design tool user Access Token Vlidated Successfully.</p></div>";
                    $status = 1;
                    $result['type'] = ($status == 0 ? "failed" : "success");
                    $result['data'] = $data_response;
                    $resultJson = json_encode($result);
                    echo $resultJson;
                }else{
                    $data_response = "<div class='validate-error'><p> Design tool user Access Token Not Valid.</p></div>";
                    $status = 0;
                    $result['type'] = ($status == 0 ? "failed" : "success");
                    $result['data'] = $data_response;
                    $resultJson = json_encode($result);
                    echo $resultJson;
                }
            }
        }
        die;
    }

    /*
     * user token remove
     * callback function
    */

    public function aakron_design_tool_remove_user_token_callback(){
        $option_design_tool_status       = 'is_design_tool_active' ;
        $option_design_tool_access_token = 'aakron_design_tool_access_toekn' ;
        if ( get_option( $option_design_tool_status ) !== false && get_option( $option_design_tool_access_token ) !== false ) {
            // deletee wp options 
            delete_option( $option_design_tool_status ); 
            delete_option( $option_design_tool_access_token );
            $data_response = "<p style='font-size: 14px; line-height: 1;'> Design tool user Access Token Removed Successfully.</p>";
            $status = 1;
            $result['type'] = ($status == 0 ? "failed" : "success");
            $result['data'] = $data_response;
            $resultJson = json_encode($result);
            echo $resultJson; 
        }
        die;
    }

    /*
     * user email validation
     * callback function
    */
    public function aakron_design_tool_user_email_validate_callback(){
        if( isset($_POST['emailValue']) ){
            $emailValue = sanitize_email($_POST['emailValue']);
            $request_uri = 'https://designtoolapi.aakronline.com/api/registration/checkUserExists/'.$emailValue;

            $argsApi = [
                'headers'     => [
                    'Content-Type' => 'application/json',
                ],
                'timeout'     => 60,
                'redirection' => 10,
                'blocking'    => true,
                'httpversion' => '1.0',
                'sslverify'   => false,
            ];
            $response = wp_remote_get($request_uri,$argsApi);
            $emailValidateJson = wp_remote_retrieve_body( $response );
            $emailValidateRepsponseArr = json_decode($emailValidateJson, TRUE);

            if($emailValidateRepsponseArr['response']['responseCode'] == 200){
                $data_response = "<p style='font-size: 14px; line-height: 1;'> Email validated Successfully.</p>";
                $status = 1;
                $result['type'] = ($status == 0 ? "failed" : "success");
                $result['data'] = $data_response;
                $resultJson = json_encode($result);
                echo $resultJson;  
            }else{
                $data_response = "<p style='font-size: 14px; line-height: 1;'> Email already exists.</p>";
                $status = 0;
                $result['type'] = ($status == 0 ? "failed" : "success");
                $result['data'] = $data_response;
                $resultJson = json_encode($result);
                echo $resultJson;
            }
        }
        die;
    }

    /*
     * admin order item line headers
     * callback function
    */
    public function aakron_design_tool_admin_order_item_headers(){
        // display the column name
        echo '<th>Artwork</th>';
        echo '<th>Color</th>';
        echo '<th>Imprint Location</th>';
    }

    /*
     * admin order item line values
     * callback function
    */
    public function aakron_design_tool_order_itemline_artwork($_product, $item, $item_id = null) {
        // Only "line" items and backend order pages
        if( ! ( is_admin() && $item->is_type('line_item') ) ) return;
        
        // get the post meta value from the associated product
        $userArtwrokImages  = $item->get_meta( 'aakron_design_artwork_url_value', true );
        $userArtworkUrl     = $userArtwrokImages[0];
        $userArtworkColor   = $userArtwrokImages[2];
        $userArtworkImprint = $userArtwrokImages[3];

        // display the value
        echo '<td><img style="display:block;width:150px;height:150px;" src="'.esc_url_raw($userArtworkUrl).'"></td>';
        echo '<td>'.esc_html($userArtworkColor).'</td>';
        echo '<td>'.esc_html($userArtworkImprint).'</td>';
    }

    /*
     * Woocommerce Template Override
     * callback function
    */
    public function aakron_design_tool_woo_template_override( $template, $template_name, $template_path ) {
         global $woocommerce;
         $_template = $template;
         if ( ! $template_path ) 
            $template_path = $woocommerce->template_url;
     
         $plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) )  . '/woocommerce/';
     
        // Look within passed path within the theme - this is priority
        $template = locate_template(
        array(
          $template_path . $template_name,
          $template_name
        )
       );
     
       if( ! $template && file_exists( $plugin_path . $template_name ) )
        $template = $plugin_path . $template_name;
     
       if ( ! $template )
        $template = $_template;

       return $template;
    }
}