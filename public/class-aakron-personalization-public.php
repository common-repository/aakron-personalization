<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Aakron_Personalization
 * @subpackage Aakron_Personalization/public
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Aakron_Personalization_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->init();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/aakron-personalization-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		//wp_enqueue_script( 'aakron_design_public_js', $this->plugin_name, plugin_dir_url( __FILE__ ) . '', array( 'jquery' ), $this->version, false );
		if( is_product() ){
			wp_register_script( 'aakron_design_public_js', plugin_dir_url( __FILE__ ) . 'js/aakron-personalization-public.js', array( 'jquery' ), rand(1,99999999999), false  );
		}
		
		// Localize the script with new data
        $translation_array = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'pluginsUrl' => plugin_dir_url( __FILE__ ),
        );

		wp_localize_script( 
            'aakron_design_public_js', 
            'aakron_design_public_obj', 
            $translation_array 
        );

        // Enqueued script with localized data.
        wp_enqueue_script( 'aakron_design_public_js' );

	}


	/*
     * init() callback to register routes
     * callback function
    */
	public function init(){
		remove_action( 'woocommerce_single_product_summary', array( $this, 'woocommerce_template_single_price' ), 999999, 10 );
		//add_action( 'wp_footer', array( $this, 'aakron_design_tool_add_jscript_checkout_footer' ), 9999 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'aakron_design_tool_product_detail_dynamic_pricing' ), 10 );
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'aakron_design_tool_start_button' ),55 );
		

		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'aakron_design_tool_add_custom_item_data' ), 1, 2 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'aakron_design_tool_get_cart_items_from_session' ), 1, 3 );
		add_filter( 'woocommerce_cart_item_name', array( $this, 'aakron_design_tool_custom__data_option_from_session_into_cart' ), 1, 3 );
		add_filter( 'woocommerce_checkout_create_order_line_item', array( $this, 'aakron_design_tool_add_custom_order_line_item_meta' ), 10, 4 );
        add_filter( 'wp_mail_content_type', array( $this, 'aakron_design_tool_set_html_mail_content_type' ), 9999, 3 );
        add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'aakron_design_tool_add_to_cart_validation' ), 10, 5 );

        // checkout field customisation
        //add_filter( 'gettext', array( $this, 'aakron_design_tool_checkout_shipping_title' ), 9999, 3 );
        add_filter( 'woocommerce_checkout_fields', array( $this, 'aakron_design_tool_rename_wc_checkout_fields' ), 9999 );
        add_filter( 'woocommerce_checkout_fields', array( $this, 'aakron_design_tool_add_field_and_reorder_fields' ) );
        add_filter( 'woocommerce_checkout_process', array( $this, 'aakron_design_tool_custom_checkout_field_process' ) );
        add_filter( 'woocommerce_checkout_create_order', array( $this, 'aakron_design_tool_save_order_custom_meta_data' ), 10, 2 );
        add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true' );

        add_filter( 'woocommerce_checkout_get_value', array( $this, 'aakron_design_tool_clear_shipping_fields_values' ), 9999, 2 );
        //add_filter( 'woocommerce_checkout_get_value', array( $this, 'aakron_design_tool_clear_billing_fields_values' ), 9999, 2 );
        add_action( 'init', array( $this, 'aakron_design_tool_disable_mail_emojis' ) );
        add_filter( 'woocommerce_get_price_html', array( $this, 'aakron_design_tool_alter_price_display' ), 9999, 2 );
        add_action( 'woocommerce_thankyou', array( $this, 'aakron_design_tool_order_email_notification_to_production' ), 10, 5 );
		add_filter( 'woocommerce_thankyou', array( $this, 'aakron_design_tool_add_purchase_order_number' ), 10, 1 );   
	}

	public function aakron_design_tool_alter_price_display( $price_html, $product ) {
	    global $product, $post;
		$isDynamicPricing = 0;
		$low_pricing_rules_set = null;
		$pricing_rule_sets = get_post_meta($post->ID, '_pricing_rules', true);
	    if ($pricing_rule_sets && is_array($pricing_rule_sets) && sizeof($pricing_rule_sets) > 0){
	        $pricing_rule_sets = array_shift($pricing_rule_sets);
	        $low_pricing_rules_set = end($pricing_rule_sets['rules']);
	    }
		$aakronLowestPrice = $low_pricing_rules_set['amount'];
		return '$'.number_format((float)$aakronLowestPrice, 2, '.', '');
	}
	
	public function aakron_design_tool_clear_shipping_fields_values( $value, $input ) {
	    $keys = ['first_name','last_name','company','address_1','address_2','city','postcode','country','state'];
	    $key  = str_replace('shipping_', '', $input);
	    if( in_array($key, $keys) && is_checkout() ) {
	        $value = '';
	    }
	    return $value;
	}

	/*public function aakron_design_tool_clear_billing_fields_values( $value, $input ) {
	    $keys = ['first_name','last_name','company','address_1','address_2','city','postcode','country','state'];
	    $key  = str_replace('billing_', '', $input);
	    if( in_array($key, $keys) && is_checkout() ) {
	        $value = '';
	    }
	    return $value;
	}*/

	//Change the Billing Details checkout label to Contact Information
	/*public function aakron_design_tool_checkout_shipping_title( $translated_text, $text, $domain ) {
		switch ( $translated_text ) {
			case 'Billing details' :
				$translated_text = __( 'Shipping Details', 'woocommerce' );
				break;
		}
		return $translated_text;
	}*/

	/*public function aakron_design_tool_add_jscript_checkout_footer() {
	   if ( is_checkout() ) {
	      	echo "<script>
	      	 	jQuery(document).ready(function($){
	      	 		$('.woocommerce-billing-fields .woocommerce-input-wrapper input').attr('tabindex', '-1');
					$('.woocommerce-billing-fields .woocommerce-input-wrapper select').attr('tabindex', '-1');
					setTimeout(function(){
						$('.woocommerce-billing-fields .woocommerce-billing-fields__field-wrapper').append('<p></p>').addClass('form-row form-row-wide');
					}, 3000);
	      	 	});
	      	</script>";
	   }
	}*/


	// Change placeholder and label text
	public function aakron_design_tool_rename_wc_checkout_fields( $fields ) {
		// billing
	  	/*$fields['billing']['billing_company']['required'] 		= 'true';
	  	$fields['billing']['billing_state']['required']   		= 'true';*/
	  	// priorities
	  	/*$fields['billing']['billing_state']['priority']   		= 65;
	  	$fields['billing']['billing_state']['priority']   		= 65;*/

	  	// shipping
	  	$fields['shipping']['shipping_first_name']['label'] 	= 'Name';
	  	$fields['shipping']['shipping_state']['required']  	 	= 'true';
	  	$fields['shipping']['shipping_address_1']['label'] 		= "Shipping Address";	
	  	$fields['shipping']['shipping_postcode']['label'] 		= 'Zip Code';
	  	// priorities
	  	$fields['shipping']['shipping_city']['priority']   		= 80;
	  	$fields['shipping']['shipping_state']['priority']   	= 70;
	  	$fields['shipping']['shipping_last_name']['priority']   = 20;
	  	$fields['shipping']['shipping_email']['priority']   	= 15;
	  	$fields['shipping']['shipping_country']['priority']   	= 65;

	  	/*$purchaseOrderNumber = get_option('aakron_purchase_order_number');
		if( $purchaseOrderNumber !== false ){
			$fields['shipping']['shipping_po_number']['default'] = $purchaseOrderNumber;
			$fields['shipping']['shipping_po_number']['custom_attributes'] = array('readonly'=>'readonly');
		}*/

	  	// set billing field values
	  	/*$fields['billing']['billing_country']['type'] 		= 'text';
	  	$fields['billing']['billing_state']['label'] 		= 'State';
	  	$fields['billing']['billing_state']['type'] 		= 'text';*/

	  	$aakronDesignToolUser = get_users( [ 'role__in' => [ 'aakron_design_tool_user' ] ] );
        if( !empty($aakronDesignToolUser) ){
            $userDataObj =  $aakronDesignToolUser[0]->data;
            $userId =  $userDataObj->ID;
            $userInformation = get_userdata($userId);
         
            $firstName      = $userInformation->first_name;
            $lastName       = $userInformation->last_name;
            $userEmail      = $userInformation->user_email;
            $companyName    = $userInformation->billing_company;
            $userAllMeta    = get_user_meta($userId);
            $account        = $userAllMeta['billing_account'][0];
            $website        = $userInformation->user_url;
            $password       = $userInformation->user_pass;
            $billingCountry    = $userInformation->billing_country;
            $billingAddressOne  = $userInformation->billing_address_1;
            $billingAddressTwo  = $userInformation->billing_address_2;
            $billingCity      = $userInformation->billing_city;
            $billingState      = $userInformation->billing_state;
            $billingPoNumber      = $userInformation->billing_postcode;
            $billingPhone      = $userInformation->billing_phone;

            /*$fields['billing']['billing_first_name']['default'] = $firstName;
		  	$fields['billing']['billing_last_name']['default'] 	= $lastName;
		  	$fields['billing']['billing_email']['default'] 		= $userEmail;
		  	$fields['billing']['billing_account']['default'] 	= $account;
		  	$fields['billing']['billing_company']['default'] 	= $companyName;
		  	$fields['billing']['billing_country']['default'] 	= $billingCountry;
		  	$fields['billing']['billing_address_1']['default'] 	= $billingAddressOne;
		  	$fields['billing']['billing_address_2']['default'] 	= $billingAddressTwo;
		  	$fields['billing']['billing_city']['default'] 		= $billingCity;
		  	$fields['billing']['billing_state']['default'] 		= $billingState;
		  	$fields['billing']['billing_phone']['default'] 		= $billingPhone;*/
			
			
			/*$fields['billing']['billing_first_name']['type'] 	= 'hidden';
		  	$fields['billing']['billing_last_name']['type'] 	= 'hidden';
		  	$fields['billing']['billing_account']['type'] 		= 'hidden';
		  	$fields['billing']['billing_company']['type'] 		= 'hidden';
		  	$fields['billing']['billing_country']['type'] 		= 'hidden';
		  	$fields['billing']['billing_address_1']['type'] 	= 'hidden';
		  	$fields['billing']['billing_address_2']['type'] 	= 'hidden';
		  	$fields['billing']['billing_city']['type'] 			= 'hidden';
		  	$fields['billing']['billing_state']['type'] 		= 'hidden';
		  	$fields['billing']['billing_phone']['type'] 		= 'hidden';
			$fields['billing']['billing_email']['type'] 		= 'hidden';*/

			/*$fields['billing']['billing_first_name']['custom_attributes'] 	= array('readonly'=>'readonly');
		  	$fields['billing']['billing_last_name']['custom_attributes'] 	= array('readonly'=>'readonly');
		  	$fields['billing']['billing_account']['custom_attributes'] 		= array('readonly'=>'readonly');
		  	$fields['billing']['billing_company']['custom_attributes'] 		= array('readonly'=>'readonly');
		  	$fields['billing']['billing_country']['custom_attributes'] 		= array('readonly'=>'readonly');
		  	$fields['billing']['billing_address_1']['custom_attributes'] 	= array('readonly'=>'readonly');
		  	$fields['billing']['billing_address_2']['custom_attributes'] 	= array('readonly'=>'readonly');
		  	$fields['billing']['billing_city']['custom_attributes'] 		= array('readonly'=>'readonly');
		  	$fields['billing']['billing_state']['custom_attributes'] 		= array('readonly'=>'readonly');
		  	$fields['billing']['billing_phone']['custom_attributes'] 		= array('readonly'=>'readonly');
			$fields['billing']['billing_email']['custom_attributes'] 		= array('readonly'=>'readonly');*/
        }

	  	// Billing fields
		unset( $fields['billing']['billing_postcode'] );
		unset( $fields['billing']['billing_po_number'] );

		/* Unset on 06-09-2021 */
		//unset( $fields['billing']['billing_first_name'] );
		//unset( $fields['billing']['billing_last_name'] );
		unset( $fields['billing']['billing_account'] );
		unset( $fields['billing']['billing_company'] );
		//unset( $fields['billing']['billing_country'] );
		//unset( $fields['billing']['billing_address_1'] );
		//unset( $fields['billing']['billing_address_2'] );
		unset( $fields['billing']['billing_city'] );
		unset( $fields['billing']['billing_state'] );
		unset( $fields['billing']['billing_phone'] );
		//unset( $fields['billing']['billing_email'] );

		//unset( $fields['billing']['billing_country'] );
		//unset( $fields['billing']['billing_state'] );
		// Shipping fields
		unset( $fields['shipping']['shipping_company'] );
		unset( $fields['shipping']['shipping_last_name'] );
		unset( $fields['shipping']['shipping_address_1'] );
		unset( $fields['shipping']['shipping_po_number'] );
	    return $fields;
	}
   
	public function aakron_design_tool_add_field_and_reorder_fields( $fields ) {
	   
	    // Add New Fields
	        
	    $fields['billing']['billing_account'] = array(
		    'label'     => 'Account / Suffix',
		    'placeholder'   => '',
		    'priority' => 22,
		    'required'  => true,
		    'clear'     => true,
	    );


	    $fields['shipping']['shipping_email'] = array(
			'label'     => __('Email', 'woocommerce'),
			'required'  => true,
			'priority' => 30,
			'class'     => array('form-row-wide'),
			'clear'     => true
		);

	    $fields['shipping']['shipping_po_number'] = array(
		    'label'     => 'Purchase Order Number',
		    'placeholder'   => '',
		    'priority' => 95,
		    'required'  => true,
		    'clear'     => true,
	    );

	    $fields['shipping']['shipping_address_ship'] = array(
		    'label'     => 'Shipping Address',
		    'placeholder'   => 'House number and street name',
		    'priority' => 20,
		    'required'  => true,
		    'clear'     => true,
	    );
	      
	    return $fields;
	}

	// Save the custom checkout field in the order meta
	
	public function aakron_design_tool_save_order_custom_meta_data( $order, $data ) {
	    if ( isset($_POST['billing_account']) ){
	        $order->update_meta_data('billing_account', sanitize_text_field( $_POST['billing_account'] ) );
	    }
	    if ( isset($_POST['shipping_po_number']) ){
	        $order->update_meta_data('shipping_po_number', sanitize_text_field( $_POST['shipping_po_number'] ) );
	    }
	    if ( isset($_POST['shipping_address_ship']) ){
	        $order->update_meta_data('shipping_address_ship', sanitize_text_field( $_POST['shipping_address_ship'] ) );
	    }
	}

	// add purchase order number
	public function aakron_design_tool_add_purchase_order_number(){
		$aakron_purchase_order_number = 'aakron_purchase_order_number';
		$purchaseOrderNumber = get_option($aakron_purchase_order_number);
		if( $purchaseOrderNumber !== false ){
			$purchaseOrderNumber++;
			update_option( $aakron_purchase_order_number, $purchaseOrderNumber );
		}
	}

	/*
     * Woocommerce before add to cart hook
     * callback function
    */

	public function aakron_design_tool_start_button(){
		global $product;
		$product_id 	  			= $product->get_id();
		$productSku 	  			= $product->get_sku();
		$virtualProductId 			= get_post_meta( $product_id, 'product_id', true );
		$userDesignToolStatus       = get_option( 'is_design_tool_active' );
        $userDesignToolAccessToken  = get_option( 'aakron_design_tool_access_toekn' );
        $aakronDesignToolUser 		= get_users( [ 'role__in' => [ 'aakron_design_tool_user' ] ] );
        $options                    = get_option( 'aakron_design_options' );
        /*$request_uri  				= 'https://fdt-design.staging.flowz.com'; */
        $request_uri  				= 'https://designtool.aakronline.com';
        
        $pluginDirUrl 				= plugin_dir_url( __DIR__ );

        if( !empty($aakronDesignToolUser) ){
            $userDataObj =  $aakronDesignToolUser[0]->data;
            $userId =  $userDataObj->ID;
        	if( !empty($userDesignToolStatus ) && $userDesignToolStatus == 'APPROVED'  ){
        ?>
            <div id="js_design_tool_div">
            <?php if( !empty($userDesignToolAccessToken) ){ ?>
            	<input id="js_design_tool_site_uri" type="hidden" value="<?php echo esc_attr($pluginDirUrl); ?>">
            	<input id="js_design_tool_product_uri" type="hidden" value="<?php echo esc_attr($request_uri); ?>">
            	<input id="js_design_tool_product_sku" type="hidden" value="<?php echo esc_attr($productSku); ?>">
            	<input id="js_design_tool_virtual_id" type="hidden" value="<?php echo esc_attr($virtualProductId); ?>">
				<input id="js_design_tool_validate_token" type="hidden" value="<?php echo esc_attr($userDesignToolAccessToken); ?>">
			<?php } ?>
			<button type="button" id="aakron_artwork_design" name="aakron_start_design" class="button alt">Start Design</button>
				<div id="js_design_tool_frontend_area" style="display:none; width:150px">
					<img data-title="Font" data-width="500" data-height="500" src="">
					<input id="js_artwork_only_image" type="hidden" value="">
					<!-- COLOR AND IMPRINT LOCATION VAUE-->  
					<input id="js_artwork_only_color" type="hidden" value="Red">
					<input id="js_artwork_only_imprint_location" type="hidden" value="Left">
					<!-- COLOR AND IMPRINT LOCATION VAUE-->  
					<!-- <img id="js_artwork_only_image" data-title="Font" data-width="500" data-height="500" style="display: none;" src=""> -->
					<button type="button" id="aakron_artwork_save_design" name="aakron_start_design" class="button alt" style="display: none;">Save Design</button>
				</div>
			</div>
	<?php
			}
        }
	}

	/*
     * add custom data to session 
     * callback function
    */
	public function aakron_design_tool_add_custom_data_to_cart_callback(){
		if( isset($_POST['userArworkImages']) ){
			$userArworkImages = $this->aakron_design_tool_wporg_recursive_sanitize_text_field($_POST['userArworkImages']);
		}
		// CUSTOM ARTWORK URL INTO SESSION
	    //echo "Id".$product_id = $_POST['id']; //This is product ID
	    session_start();
	    $_SESSION['aakron_design_artwork_url'] = $userArworkImages;
	    die;
	}

	/**
	 * Validate our custom text input field value
	 */
	function aakron_design_tool_add_to_cart_validation( $passed, $product_id, $quantity, $variation_id=null ) {
		session_start();
		if (isset($_SESSION['aakron_design_artwork_url'])) {
			$artworkUrl      = sanitize_text_field($_SESSION['aakron_design_artwork_url'][0]);
			$artworkEps      = sanitize_text_field($_SESSION['aakron_design_artwork_url'][1]);
			$artworkColor    = sanitize_text_field($_SESSION['aakron_design_artwork_url'][2]);
			$artworImprint   = sanitize_text_field($_SESSION['aakron_design_artwork_url'][3]);

		 	if( empty($artworkUrl) ) {
			 	$passed = false;
			 	wc_add_notice( __( 'You must have to design your Product.', 'plugin-republic' ), 'error' );
		 	}
		 	return $passed;
		}
		die;
	}
	
	/*
     * Add custom data from session into cart item data 
     * callback function
    */
    public function aakron_design_tool_add_custom_item_data($cart_item_data,$product_id){
        /*Here, We are adding item in WooCommerce session with, wdm_user_custom_data_value name*/
				global $woocommerce;
				if(!isset($_SESSION)) { 
						session_start(); 
				}
        //session_start();    
        if (isset($_SESSION['aakron_design_artwork_url'])) {
            $option = $this->aakron_design_tool_wporg_recursive_sanitize_text_field($_SESSION['aakron_design_artwork_url']); 
            $new_value = array('aakron_design_artwork_url_value' => $option);
        }

        if(empty($option)){
            return $cart_item_data;
        }
        else{    
            if(empty($cart_item_data)){
                return $new_value;
            }else{
                return array_merge($cart_item_data,$new_value);
            }
        }
       	unset($_SESSION['aakron_design_artwork_url']); //Unset our custom session variable, as it is no longer needed.
    }

    /*
     * Add custom data to order item line data 
     * callback function
    */
    public function aakron_design_tool_add_custom_order_line_item_meta($item, $cart_item_key, $values, $order){
	    if(array_key_exists('aakron_design_artwork_url_value', $values)){
	        $item->add_meta_data('aakron_design_artwork_url_value',$values['aakron_design_artwork_url_value']);
	    }
	}
	
	/*
     * get cart item custom data frm session
     * callback function
    */
    public function aakron_design_tool_get_cart_items_from_session($item,$values,$key){
        if (array_key_exists( 'aakron_design_artwork_url_value', $values ) ){
       		$item['aakron_design_artwork_url_value'] = $values['aakron_design_artwork_url_value'];
        }  
        return $item;
    }

    /*
     * get custom data from cart item data on cart page
     * callback function
    */
    public function aakron_design_tool_custom__data_option_from_session_into_cart($product_name, $values, $cart_item_key ){
    	if (array_key_exists( 'aakron_design_artwork_url_value', $values ) ){
    		$userArtwrokImagesCart = $values['aakron_design_artwork_url_value'];
    		//echo $userArtwrokImagesPdf  = $userArtwrokImagesCart[1];
    		//code to add custom data on Cart & checkout Page  
	        if(count($values['aakron_design_artwork_url_value']) > 0){
	            $return_string  = $product_name . "</a><dl class='variation'>";
	            $return_string .= '<img src="'.$userArtwrokImagesCart[0].'">';
	            return $return_string;
	        }else{
	            return $product_name;
	        }
    	}
    }

    /*
     * display product dynamic pricing on product detail page
     * callback function
    */
	public function aakron_design_tool_product_detail_dynamic_pricing(){
		global $product, $post;
			$isDynamicPricing = 0;
			    $pricing_rule_sets = get_post_meta($post->ID, '_pricing_rules', true);
			    if ($pricing_rule_sets && is_array($pricing_rule_sets) && sizeof($pricing_rule_sets) > 0){
			        $pricing_rule_sets = array_shift($pricing_rule_sets);
			        if ($pricing_rule_sets['rules'][1]['from'] !="") { ?>
			        <div class="dynamic-price-quantity">
		    	        <span style="font-weight: 700;margin: 5px 0;">Quantity - Price Grid</span>
		                <table border="1" style="margin-top:10px;">
		                	<tr>
		                		<td>Quantity</td>
		                		<?php
		                		    $count = 0;
		                		    foreach($pricing_rule_sets['rules'] as $priceKey=>$pricing){
		                		        $pricing_rule_sets['rules'][$priceKey]['amount'] = $pricing['amount'];
		                		        if($count == 0){
		                		            $product->dynamic_min_price = $pricing['amount'];
		                		            $product->dynamic_min_quantity = $pricing['from'];
		                		        }
		                		        $count++;
		                		?>
		                			<td><?php echo esc_attr($pricing['from']).' - '.esc_attr($pricing['to']); ?></td>
		                		<?php } ?>
		                	</tr>
		                	<tr>
		                		<td>Price</td>
		                		<?php foreach($pricing_rule_sets['rules'] as $priceKey => $pricing){ ?>
		                			<td>
		                				<?php 
		                					echo get_woocommerce_currency_symbol();
		                					echo number_format($pricing['amount'], 2);
		                				?>
		                			</td>
		                		<?php } ?>
		                	</tr>
		                </table>
		        	</div>
		<?php 
		           $isDynamicPricing = 1;
		        }
		    }
		?>
	<?php
	}

	/*
     * order email notification to "AAKRONLINE PRODUCTION TEAM"
     * callback function
    */
	public function aakron_design_tool_order_email_notification_to_production( $order_id ){
		global $woocommerce;
	   	$order   = new WC_Order( $order_id );
	   	$options                = get_option( 'aakron_design_options' );
        $toEmail  				= $options['aakron_product_design_tool_email'];
        $subject 				= 'AakronLine Artwork';
		$headers = "From: info@aakronline.com" . "\r\n" .
			"CC: ". "\r\n" .
			"BCC: jignasha@flowz.com";
      	$message_body = '';
	   	$count = 0;
	   	$message_body .=  $this->messageBodyHtml($order_id); // get message body html

      	// order email function with subject , message boy and headers
     	$userMail = wp_mail( $toEmail, $subject, $message_body, $headers);
	}

	/*
     * order email message body html create
     * callback function
    */
		public function messageBodyHtml($order_id){
		// Get and Loop Over Order Items
		$order      = new WC_Order( $order_id );
		$aakronDesignToolUser = get_users( [ 'role__in' => [ 'aakron_design_tool_user' ] ] );
        if( !empty($aakronDesignToolUser) ){
            $userDataObj =  $aakronDesignToolUser[0]->data;
            $userId =  $userDataObj->ID;
            $userInformation = get_userdata($userId);
            //echo "<pre>";print_r($userInformation);echo "</pre>";
            $billingCompanyName    	= $userInformation->billing_company;
            $userAllMeta    		= get_user_meta($userId);
            $billingAccount        	= $userAllMeta['billing_account'][0];
            $billingFirstName     	= $userInformation->first_name;
            $billingLastName       	= $userInformation->last_name;
            $billingFullName 		= $billingFirstName." ".$billingLastName;
            $billingEmail          	= $userInformation->user_email;
            $billingPhone      		= $userInformation->billing_phone;
            $billingAddressOne  	= $userInformation->billing_address_1;
            $billingAddressTwo  	= $userInformation->billing_address_2;
            $billingCity      		= $userInformation->billing_city;
            $billingCountry      	= $userInformation->billing_country;
            $billingState      		= $userInformation->billing_state;
            $billingAddress         = $billingAddressOne.'<br/>'.$billingAddressTwo.'<br/>'.$billingState.'<br/>'.$billingCountry;     
        }

		$shipping_first_name = $order->get_shipping_first_name();
		$address 			 = $order->get_shipping_last_name();
		//$shipping_address_1  = $order->get_shipping_address_1();
		$shipping_address_ship  = get_post_meta( $order_id, 'shipping_address_ship', true );
		$shipping_address_2  = $order->get_shipping_address_2();
		$shipping_country	 = isset(WC()->countries->countries[ $order->get_shipping_country() ]) ? WC()->countries->countries[ $order->get_shipping_country() ] : ''; 
		$states = WC()->countries->get_states( $order->get_shipping_country() );
		$shipping_state	 	 = ! empty( $states[ $order->get_shipping_state() ] ) ? $states[ $order->get_shipping_state() ] : '';
		$shippingAddress 	 = $shipping_address_ship.'<br/>'.$shipping_address_2.'<br/>'.$shipping_state.'<br/>'.$shipping_country;
		$shippingZipCOde 	 = $order->get_shipping_postcode();
		//$shipping_po_number  = get_post_meta( $order_id, 'shipping_po_number', true );
		
		$shipping_po_number = null;
		if( get_option('aakron_purchase_order_number') !== false ){
			$shipping_po_number = get_option('aakron_purchase_order_number');
		}

		$shipingMethod 		 = $order->get_shipping_method();
		$shiDate 			 = $this->getShipDate();
		$postCode 			 = $order->get_shipping_postcode();
		$orderTotal 	     = $order->get_total();
		
		$tableHtml  = '';
		$tableHtml .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#fff"><tbody><tr><td align="center" valign="top">';
			$tableHtml .= '<table width="650" border="0" cellspacing="0" cellpadding="0" class=""> <tbody><tr> <td class="td container" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;width:650px;font-size: 14px;line-height: initial;margin:0;font-weight:normal;padding:0px 0px 0px 0px;">';
			
			// Header 
			$tableHtml .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:15px ;"><tbody><tr>';	
			$tableHtml .= '<td class="" style=" padding:10px; margin:0; font-weight:normal; vertical-align:top; font-size:30pt;   text-align:left;" >
						<img style="width:200px;" src="'.plugin_dir_url( __FILE__ ).'images/logo.png">
					</td>';

			$tableHtml .= '
					<td style=" color:#535e7d;font-family:"Roboto", Arial,sans-serif;font-size:14px;line-height:28px; text-align:right;"><strong style="font-size:16px;">PO Number</strong><br>'.$shipping_po_number.'</td>
				';
			$tableHtml .= '</tr></tbody></table>';
			// END Header

			// Article Image On The Left
			$tableHtml .= '<table width="100%" border="0" cellspacing="0" cellpadding="0"> <tbody>';
				$tableHtml .= '<tr><td>';
					$tableHtml .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f6f9fc" style="color:#535e7d;font-family:"Roboto", Arial,sans-serif;font-size:16px;line-height:28px;text-align:left;border:1px solid #e6ebf1"> <tbody>';
							$userArtwrokImages = '';
							$i = 0;
							foreach ( $order->get_items() as $item_id => $item ) {
								$i++;
								
									$productId = $order->get_item_meta($item_id, '_product_id', true); // product ID
 									$productName = $item['name']; // product name
						    	$productDescription = get_post($productId)->post_content; // Product description
							   	$allmeta = $item->get_meta_data();
							   	$userArtwrokImages = $item->get_meta( 'aakron_design_artwork_url_value', true );
							   	$productOrderQuantity= $item->get_quantity();

							   	$product             = wc_get_product( $productId );
								$productSku          = $product->get_sku($productId);
								$productRegularPrice = $product->get_regular_price();
								$producItemTotal     = $productOrderQuantity * $productRegularPrice;
								$userArtworkImg = '';
								$userArtworkImgOnly = '';
								$userArtworkImgColor ='';
								$userArtworkImgImprint ='';

								if( !empty($userArtwrokImages) ){
									$userArtworkImg     = $userArtwrokImages[0];
									$userArtworkImgOnly = $userArtwrokImages[1];
									$userArtworkImgColor = $userArtwrokImages[2];
									$userArtworkImgImprint = $userArtwrokImages[3];
								}
							
							$tableHtml  .= '<tr><td colspan="2" style="padding:5px 20px;border-bottom:solid 1px #e6ebf1;font-size: 22px;font-weight: 700;color: #000;">Product '.$i.'</td></tr>';

								$tableHtml .= '<tr>
											<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>SKU</strong></td>
											<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">'.$productSku.'</td>
										</tr>';

								$tableHtml .= '<tr>
											<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Product Title</strong></td>
											<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">'.$productName.'</td>
										</tr>';

								$tableHtml .= '<tr>
											<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Quantity</strong></td>
											<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">'.$productOrderQuantity.'</td>
										</tr>';

								$tableHtml .= '<tr>
											<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Image of art for proofing</strong></td>
											<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;"><img width="150px" height="150px" src="'.$userArtworkImg.'"> <br/> <a href="'.$userArtworkImg.'" style="color:#000;" >Link to Proof</a></td>
										</tr>';

								$tableHtml .= '<tr>
											<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Link to art</strong></td>
											<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;"><a href="'.$userArtworkImgOnly.'" style="color:#000;" >Link to art</a></td>
										</tr>';

								$tableHtml .= '<tr>
											<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Color</strong></td>
											<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">'.$userArtworkImgColor.'</td>
										</tr>';

								$tableHtml .= '<tr>
											<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Imprint Location</strong></td>
											<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">'.$userArtworkImgImprint.'</td>
										</tr>';

								$tableHtml .= '<tr>
									<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Price</strong></td>
									<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">$'.$producItemTotal.'</td>
								</tr>';
						}

						$tableHtml .= '<tr>
									<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Company Name</strong></td>
									<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">'.$billingCompanyName.'</td>
								</tr>';

						$tableHtml .= '<tr>
									<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Account / Suffix</strong></td>
									<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">'.$billingAccount.'</td>
								</tr>';
								
						$tableHtml .= '<tr>
									<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Name</strong></td>
									<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">'.$billingFullName.'</td>
								</tr>';

						$tableHtml .= '<tr>
									<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Email</strong></td>
									<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">'.$billingEmail.'</td>
								</tr>';

						$tableHtml .= '<tr>
									<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Phone</strong></td>
									<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">'.$billingPhone.'</td>
								</tr>';

						$tableHtml .= '<tr>
									<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Bill To</strong></td>
									<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">'.$billingAddress.'</td>
								</tr>';

						$tableHtml .= '<tr>
									<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Shipping Method</strong></td>
									<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">'.$shipingMethod.'</td>
								</tr>';

						$tableHtml .= '<tr>
									<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Name</strong></td>
									<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">'.$shipping_first_name.'</td>
								</tr>';

						$tableHtml .= '<tr>
									<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Shipping Address</strong></td>
									<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">'.$shippingAddress.'</td>
								</tr>';

						$tableHtml .= '<tr>
									<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Zip code</strong></td>
									<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">'.$shippingZipCOde.'</td>
								</tr>';


						$tableHtml .= '<tr>
									<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Ship Date</strong></td>
									<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">'.$shiDate.'</td>
								</tr>';

						$tableHtml .= '<tr>
									<td style="padding:5px 20px; border-bottom:solid 1px #e6ebf1;"><strong>Total</strong></td>
									<td style="padding:5px 20px ; border-bottom:solid 1px #e6ebf1;font-size:14px; line-height: 18px;">$'.$orderTotal.'</td>
								</tr>';

					$tableHtml .= '</tbody></table>';
				$tableHtml .= '</td></tr>';
			$tableHtml .= '</tbody></table>';

			$optionsSocialMedia			= get_option( 'aakron_design_options' );
        	$facebookUrl		  		= $optionsSocialMedia['aakron_product_design_tool_facebook_url'];
        	$linkedinUrl		  		= $optionsSocialMedia['aakron_product_design_tool_linkedin_url'];
        	$instagramUrl		  		= $optionsSocialMedia['aakron_product_design_tool_instagram_url'];
        	$twitterUrl		  		    = $optionsSocialMedia['aakron_product_design_tool_twitter_url'];
        	$copyrightText		  		= $optionsSocialMedia['aakron_product_design_tool_copyright_text'];

			$tableHtml .= '<table width="100%" border="0" cellspacing="0" cellpadding="0"> <tbody><tr><td class="" style="padding: 0px;">';
				$tableHtml .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#e3e3e3"  >
						<tbody>';
						$tableHtml .= '<tr>
							<th class="" style="font-size:0pt;line-height:0pt;padding: 15px;margin:0;font-weight:normal;">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tbody>
										<tr>
										<td class="" style="text-align:center; padding:20px 0">
											<a href="'.$facebookUrl.'" target="_blank" style="margin:0 5px"><img src="'.plugin_dir_url( __FILE__ ).'images/fb.png"  height="" border="0" alt=""></a>
											<a href="'.$linkedinUrl.'" target="_blank" style="margin:0 5px"><img src="'.plugin_dir_url( __FILE__ ).'images/in.png"  height="" border="0" alt=""></a>
											<a href="'.$instagramUrl.'" target="_blank" style="margin:0 5px"><img src="'.plugin_dir_url( __FILE__ ).'images/insta.png"  height="" border="0" alt=""></a>
											<a href="'.$twitterUrl.'" target="_blank" style="margin:0 5px"><img src="'.plugin_dir_url( __FILE__ ).'images/tw.png"  height="" border="0" alt=""></a>
										</td>
										<td class="" style="color:#000;font-family:"Roboto", Arial,sans-serif;font-size:13px;line-height:28px;text-align:center;padding-bottom: 0px;">'.$copyrightText.'</td>
									</tr>
								</tbody></table>
							</th>
						</tr>';
				$tableHtml .= '</tbody></table>';
			$tableHtml .= '</td></tr></tbody></table>';

			// END Article Image On The Left
			$tableHtml .= '</td></tr></tbody></table>';
		$tableHtml .= '</td></tr></tbody></table>';

		return $tableHtml;
	}

	/*
     * calculate ship date wxcluding weekdays
     * callback function
    */
	public function getShipDate(){
		// Declare a date 
				$currentDate         = date('d-m-Y');
				$startDate           = null;
				$endDate             = null;
        $currentDateString   = strtotime($currentDate);
        $dayName             = date('l', $currentDateString);
        $dayNameLowercase    = strtolower($dayName);
        if( $dayNameLowercase == 'saturday'){
            $currentDate         = date('d-m-Y', strtotime($currentDate. ' + 2 days'));
        }elseif( $dayNameLowercase == 'sunday' ){
            $currentDate         = date('d-m-Y', strtotime($currentDate. ' + 1 days'));
        }else{
            $currentDate         = date('d-m-Y');
        }
        // Add days to date and display it 
        $dateWithoutExclude  = date('d-m-Y', strtotime($currentDate. ' + 7 days'));
        $resultDays = array('Monday' => 0, 
        'Tuesday' => 0, 
        'Wednesday' => 0, 
        'Thursday' => 0, 
        'Friday' => 0, 
        'Saturday' => 0, 
        'Sunday' => 0); 
      
        // change string to date time object 
        $startDate = new DateTime($startDate); 
        $endDate = new DateTime($endDate); 
      
        // iterate over start to end date 
        while($startDate <= $endDate ){ 
            // find the timestamp value of start date 
            $timestamp = strtotime($startDate->format('d-m-Y')); 
      
            // find out the day for timestamp and increase particular day 
            $weekDay = date('l', $timestamp); 
            $resultDays[$weekDay] = $resultDays[$weekDay] + 1; 
      
            // increase startDate by 1 
            $startDate->modify('+1 day');
        } 
        
        // print the result 
        $numberOfWeekDays          = $resultDays['Saturday'] + $resultDays['Sunday'];
        $dateWithExcludeWeekdays   = date('m-d-Y', strtotime($currentDate. ' + 8 days + '.$numberOfWeekDays.' weekdays'));
        return $dateWithExcludeWeekdays;
	}

	/*
     * WP Email content-type : html
     * callback function
    */
	public function aakron_design_tool_set_html_mail_content_type() {
	    return 'text/html';
	}

	/*
     * WP Email disable mail emojees
     * callback function
    */
	public function aakron_design_tool_disable_mail_emojis() {
	    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	}

	/***
	 * To ensure arrays are properly sanitized to WordPress Codex standards,
	 * they encourage usage of sanitize_text_field(). That only works with a single
	 * variable (string). This function allows for a full blown array to get sanitized
	 * properly, while sanitizing each individual value in a key -> value pair.
	 *
	 * Source: https://wordpress.stackexchange.com/questions/24736/wordpress-sanitize-array
	 * Author: Broshi, answered Feb 5 '17 at 9:14
	 */
	public function aakron_design_tool_wporg_recursive_sanitize_text_field( $array ) {
	    foreach ( $array as $key => &$value ) {
	        if ( is_array( $value ) ) {
	            $value = wporg_recursive_sanitize_text_field( $value );
	        } else {
	            $value = sanitize_text_field( $value );
	        }
	    }
	    return $array;
	}


}