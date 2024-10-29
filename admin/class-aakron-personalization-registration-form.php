<?php

/**
 * The file that defines the core plugin class for Registration form
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 *
 * @package    Aakron_Personalization
 * @subpackage Aakron_Personalization/admin
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Aakron_Personalization_Registration_Form  {
    /**
     * User Registration Form HTML for "aakron Design Tool Registration"
     *
     * @since    1.0.0
     */
    function registrationFormHtml(){
        $options                    = get_option( 'aakron_design_options' );
       /* $request_uri                = 'https://fdt-public.staging.flowz.com'; */
       /**
        * 
        * Our tool User Registration API where user is registeres.
        *
       **/
        $request_uri                = 'https://designtoolapi.aakronline.com';
        
        $aakronDesignToolUser = get_users( [ 'role__in' => [ 'aakron_design_tool_user' ] ] );
        if( !empty($aakronDesignToolUser) ){
            $userDataObj =  $aakronDesignToolUser[0]->data;
            $userId =  $userDataObj->ID;
            $userInformation = get_userdata($userId);
            //echo "<pre>";print_r($userInformation);echo "</pre>";
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
        }
?>
    <div class="aakron-design-tool-main">
        <h3>AAkron Personalization PlugIn Registration</h3>
        <div id="user_registration-form">
            <form action="" method="post" name="user_registeration" class="aakron_design_tool_registration">
                            <div class="forms-box">
                    <label>First Name <span class="error">*</span></label>
                    <input type="text" id="js_user_first_name" name="fname" placeholder="Enter Your First Name" class="text" value="<?php if(!empty($firstName)){ echo esc_attr($firstName); } ?>" required /><br />
                            </div>
                            <div class="forms-box">
                    <label>Last Name <span class="error">*</span></label>
                    <input type="text" name="lname" placeholder="Enter Your Last Name" class="text" value="<?php if(!empty($lastName)){ echo esc_attr($lastName); } ?>" required /><br />
                            </div>
                            <div class="forms-box">
                    <label>Account / Suffix <span class="error">*</span></label>
                    <input type="text" name="account" placeholder="Enter Your Account" class="text" value="<?php if(!empty($account)){ echo esc_attr($account); }  ?>" required /><br />
                            </div>
                            <div class="forms-box">
                    <label>Company Name <span class="error">*</span></label>
                    <input type="text" name="company" placeholder="Enter Your Company Name" class="text" value="<?php if(!empty($companyName)){ echo esc_attr($companyName); }  ?>" required /><br />
                            </div>

                            <div class="forms-box">
                    <label>Country / Region <span class="error">*</span></label>
                    <input type="text" name="billing_country" placeholder="Enter Your Country Name" class="text" value="<?php if(!empty($billingCountry)){ echo esc_attr($billingCountry); }  ?>" required /><br />
                            </div>

                            <div class="forms-box">
                    <label>Street address <span class="error">*</span></label>
                    <input type="text" name="billing_address_1" placeholder="Enter Your Street Address Line One" class="text" value="<?php if(!empty($billingAddressOne)){ echo esc_attr($billingAddressOne); }  ?>" required /><br />
                    <input type="text" name="billing_address_2" placeholder="Enter Your Street Address Line Two" class="text" value="<?php if(!empty($billingAddressTwo)){ echo esc_attr($billingAddressTwo); }  ?>" required /><br />
                            </div>

                            <div class="forms-box">
                    <label>Town / City  <span class="error">*</span></label>
                    <input type="text" name="billing_city" placeholder="Enter Your Town/City" class="text" value="<?php if(!empty($billingCity)){ echo esc_attr($billingCity); }  ?>" required /><br />
                            </div>

                            <div class="forms-box">
                    <label>State  <span class="error">*</span></label>
                    <input type="text" name="billing_state" placeholder="Enter Your State" class="text" value="<?php if(!empty($billingState)){ echo esc_attr($billingState); }  ?>" required /><br />
                            </div>

                            <div class="forms-box">
                    <label>Zip Code  <span class="error">*</span></label>
                    <input type="text" name="billing_postcode" placeholder="Enter Your Zip Code" class="text" value="<?php if(!empty($billingPoNumber)){ echo esc_attr($billingPoNumber); }  ?>" required /><br />
                            </div>

                            <div class="forms-box">
                    <label>Phone <span class="error">*</span></label>
                    <input type="text" name="billing_phone" placeholder="Enter Your Phone" class="text" value="<?php if(!empty($billingPhone)){ echo esc_attr($billingPhone); }  ?>" required /><br />
                            </div>
                            
                    <div class="forms-box">
                        <?php
                            if( !empty($aakronDesignToolUser) ){
                        ?>  
                            <label>Email address <span class="error">*</span></label>
                            <input type="text" id="js_user_email" name="useremail" class="text" placeholder="Enter Your Email" value="<?php if(!empty($userEmail)){ echo esc_attr($userEmail); } ?>" required readonly="readonly" />
                        <?php
                            }else{
                        ?>
                            <label>Email address <span class="error">*</span></label>
                            <input type="text" id="js_user_email" name="useremail" class="text" placeholder="Enter Your Email" value="<?php if(!empty($userEmail)){ echo esc_attr($userEmail); } ?>" required />
                            <img id="js_verify_spinner" src="<?php echo plugin_dir_url( __FILE__ ).'/images/verify_loader.gif'; ?>" style="display:none;width:50px;height:40px;vertical-align: middle;">
                            <span class="email-validate" style="display: none;"></span>
                        <?php
                            }
                        ?>
                    
                    </div>
                            <div class="forms-box">
                    <?php   $websiteUrl = site_url();   ?>
                    <label style="display: none;">Website <span class="error">*</span></label>
                    <input type="hidden" name="website" class="text" placeholder="Enter Your Website url" value="<?php echo esc_url_raw($websiteUrl); ?>" required /> <br />
                            </div>
                            <div class="forms-box">
                    <label>Password <span class="error">*</span></label>
                    <input type="password" name="password" class="text" placeholder="Enter Your password" value="<?php if(!empty($password)){ echo esc_attr($password); } ?>" required /> <br />
                            </div>
                <?php
                    if( empty($aakronDesignToolUser) ){
                ?>
                        <input type="submit" name="user_registeration" value="Register" />
                <?php
                    }else{
                        if( !empty($userId) ){
                ?>
                            <input type="hidden" name="user_id" value="<?php echo esc_attr ($userId); ?>">
                            <input type="submit" name="user_update" value="Update" />
                <?php
                        }
                    }
                ?>
            </form>
            <p style="color:red;font-size: 16px;"><span>Note : </span>Please note that a credit card and UPS or FedEx account number will be required to complete registration.</p>
        </div>
    <?php
    /**
     * register new user
     */
    if (isset($_POST['user_registeration'])){
        global $reg_errors,$firstName, $lastName, $userEmail, $companyName, $website, $password, $companyName, $billingCountry, $billingAddressOne, $billingAddressTwo, $billingCity, $billingState, $billingPoNumber, $billingPhone, $account, $userName ;
        $reg_errors     = new WP_Error;

        $firstName      =   sanitize_text_field( $_POST['fname'] );
        $lastName       =   sanitize_text_field( $_POST['lname'] );
        $userEmail      =   sanitize_email( $_POST['useremail'] );
        $website        =   esc_url_raw( $_POST['website'] );
        $password       =   md5( $_POST['password'] );
        $companyName    =   sanitize_text_field( $_POST['company'] );

        $billingCountry         = sanitize_text_field( $_POST['billing_country'] );
        $billingAddressOne      = sanitize_text_field( $_POST['billing_address_1'] );
        $billingAddressTwo      = sanitize_text_field( $_POST['billing_address_2'] );
        $billingCity            = sanitize_text_field( $_POST['billing_city'] );
        $billingState           = sanitize_text_field( $_POST['billing_state'] );
        $billingPoNumber        = sanitize_text_field( $_POST['billing_postcode'] );
        $billingPhone           = sanitize_text_field( $_POST['billing_phone'] );
        $account                = sanitize_text_field( $_POST['account'] );
        $userName               = preg_replace('/([^@]*).*/', '$1', $userEmail);

        
        if(empty($firstName) || empty($lastName) || empty($companyName) || empty($account) || empty( $billingCountry ) || empty( $billingAddressOne ) || empty( $billingAddressTwo ) || empty( $billingCity ) || empty( $billingState ) || empty( $billingPoNumber ) || empty( $billingPhone ) || empty( $userEmail ) || empty($website) || empty($password))
        {
            $reg_errors->add('field', 'Required form field is missing');
        }       

        if ( !is_email( $userEmail ) )
        {
            $reg_errors->add( 'email_invalid', 'Email id is not valid!' );
        }
        
        if ( email_exists( $userEmail ) )
        {
            $reg_errors->add( 'email', 'Email Already exist!' );
        }
        if ( 5 > strlen( $password ) ) {
            $reg_errors->add( 'password', 'Password length must be greater than 5!' );
        }
        if ( ! empty( $website ) ) {
            if ( ! filter_var( $website, FILTER_VALIDATE_URL ) ) {
                $reg_errors->add( 'website', 'Website is not a valid URL' );
            }
        }
        if (is_wp_error( $reg_errors ))
        { 
            foreach ( $reg_errors->get_error_messages() as $error )
            {
                //$signUpError='<p style="font-size:14px;line-height:1;"><strong>ERROR</strong>: '.$error . '<br /></p>';
                $signUpError= $error ;
            } 
        }
        
        
        if ( 1 > count( $reg_errors->get_error_messages() ) )
        {   
            $userdata = array(
                'user_login'            =>   $userName,
                'user_email'            =>   $userEmail,
                'user_pass'             =>   $password,
                'user_url'              =>   $website,
                'first_name'            =>   $firstName,
                'last_name'             =>   $lastName,
                'billing_company'       =>   $companyName,
                'role'                  =>   'aakron_design_tool_user'
                );
            
            // delete "is_design_tool_active" & "aakron_design_tool_access_toekn" if old user have in db
            if ( get_option( 'is_design_tool_active' ) !== false && get_option( 'aakron_design_tool_access_toekn' ) !== false ) {
                delete_option( 'is_design_tool_active' );
                delete_option( 'aakron_design_tool_access_toekn' );
            }
            
            $user = wp_insert_user( $userdata );
            add_user_meta( $user, 'billing_company', $companyName);
            add_user_meta( $user, 'billing_country', $billingCountry );
            add_user_meta( $user, 'billing_address_1', $billingAddressOne );
            add_user_meta( $user, 'billing_address_2', $billingAddressTwo );
            add_user_meta( $user, 'billing_city', $billingCity );
            add_user_meta( $user, 'billing_state', $billingState );
            add_user_meta( $user, 'billing_postcode', $billingPoNumber );
            add_user_meta( $user, 'billing_phone', $billingPhone );
            add_user_meta( $user, 'billing_account', $account);
            
            $body = null;
            if( !empty($user) ){
                $body = [
                    'user_id'       => '"'.$user.'"',
                    'firstName'     => $firstName,
                    'lastName'      => $lastName,
                    'company'       => $companyName,
                    'account'       => $account,
                    'email'         => $userEmail,
                    'domain'        => $website,
                    'accessToken'   => 'aakron_design_token123',
                    'connector'     => 'wordpress',
                ];
            }
            // Register's user in design tool
            $body = wp_json_encode( $body );
            $argsApi = [
                'method'      => 'POST',
                'body'        => $body,
                'headers'     => [
                    'Content-Type' => 'application/json',
                ],
                'timeout'     => 60,
                'redirection' => 10,
                'blocking'    => true,
                'httpversion' => '1.0',
                'sslverify'   => false,
                'data_format' => 'body',
            ];
            $requestApiUrl = $request_uri.'/api/registration/register';
            $responseData = wp_remote_post( $requestApiUrl, $argsApi );
            
            $responseUserReg = $responseData['body'];
            $userRegArr = json_decode($responseUserReg, TRUE);
            if( $userRegArr['status'] == 200 ){
                if( !empty($user) ){
                    $user_name    = $firstName.' '.$lastName;
                    $toEmail      =  $userEmail;
                    $subject      = 'New User Registration';
                    $message_body = '';
                    $message_body .=  $this->messageBodyHtml($user_name); // get message body html

                    // order email function with subject , message boy and headers
                    $userMail = wp_mail( $toEmail, $subject, $message_body);

                    echo '<div id="regSuccess"> <p style="font-size:14px;line-height:1;">User Registered Successfully. You will receive an Email regarding your approval status.</p></div>';
                }
            }else{
                // delete user from WP if not created in design tool admin
                wp_delete_user( $user, $reassign = null );
                echo '<div id="regError"><p style="font-size:14px;line-height:1;">User not registered please check you have entered right information.</p></div>';
            }
        }
        header("Refresh:3");
    }

    /**
     * update existing user
     */
    if (isset($_POST['user_update']))
    {   
        global $reg_errors,$firstName, $lastName, $userEmail, $companyName, $website, $password, $companyName, $billingCountry, $billingAddressOne, $billingAddressTwo, $billingCity, $billingState, $billingPoNumber, $billingPhone, $account, $userName ;
        $reg_errors     = new WP_Error;
        
        $firstName      =   sanitize_text_field( $_POST['fname'] );
        $lastName       =   sanitize_text_field( $_POST['lname'] );
        $userEmail      =   sanitize_email( $_POST['useremail'] );
        $website        =   esc_url_raw( $_POST['website'] );
        $password       =   md5( $_POST['password'] );
        $companyName    =   sanitize_text_field( $_POST['company'] );
        $user_id        =   sanitize_text_field( $_POST['user_id'] );

        $billingCountry         = sanitize_text_field( $_POST['billing_country'] );
        $billingAddressOne      = sanitize_text_field( $_POST['billing_address_1'] );
        $billingAddressTwo      = sanitize_text_field( $_POST['billing_address_2'] );
        $billingCity            = sanitize_text_field( $_POST['billing_city'] );
        $billingState           = sanitize_text_field( $_POST['billing_state'] );
        $billingPoNumber        = sanitize_text_field( $_POST['billing_postcode'] );
        $billingPhone           = sanitize_text_field( $_POST['billing_phone'] );
        $account                =   sanitize_text_field( $_POST['account'] );
        $userName               =   preg_replace('/([^@]*).*/', '$1', $userEmail);

        
        if(empty($firstName) || empty($lastName) || empty($companyName) || empty($account) || empty( $billingCountry ) || empty( $billingAddressOne ) || empty( $billingAddressTwo ) || empty( $billingCity ) || empty( $billingState ) || empty( $billingPoNumber ) || empty( $billingPhone ) || empty( $userEmail ) || empty($website) || empty($password))
        {
            $reg_errors->add('field', 'Required form field is missing');
        }    

        /*if ( !is_email( $userEmail ) )
        {
            $reg_errors->add( 'email_invalid', 'Email id is not valid!. Please Provide valid Email id.' );
        }*/
        
        
        if ( 5 > strlen( $password ) ) {
            $reg_errors->add( 'password', 'Password length must be greater than 5!' );
        }
        if ( ! empty( $website ) ) {
            if ( ! filter_var( $website, FILTER_VALIDATE_URL ) ) {
                $reg_errors->add( 'website', 'Website is not a valid URL.' );
            }
        }
        if (is_wp_error( $reg_errors ))
        { 
            foreach ( $reg_errors->get_error_messages() as $error )
            {
                //$signUpError='<p style="font-size:14px;line-height:1;"><strong>ERROR</strong>: '.$error . '<br /></p>';
                $signUpError= $error ;
            } 
        }
        
        
        if ( 1 > count( $reg_errors->get_error_messages() ) )
        {
            $user_data = wp_update_user( 
                array( 
                    'ID'                    =>   $user_id,
                    'user_login'            =>   $userName,
                    'user_email'            =>   $userEmail,
                    'user_pass'             =>   $password,
                    'user_url'              =>   $website,
                    'first_name'            =>   $firstName,
                    'last_name'             =>   $lastName,
                    'role'                  =>   'aakron_design_tool_user'
                ) );

            update_user_meta( $user_id, 'billing_company', $companyName );
            update_user_meta( $user_id, 'billing_country', $billingCountry );
            update_user_meta( $user_id, 'billing_address_1', $billingAddressOne );
            update_user_meta( $user_id, 'billing_address_2', $billingAddressTwo );
            update_user_meta( $user_id, 'billing_city', $billingCity );
            update_user_meta( $user_id, 'billing_state', $billingState );
            update_user_meta( $user_id, 'billing_postcode', $billingPoNumber );
            update_user_meta( $user_id, 'billing_phone', $billingPhone );
            update_user_meta( $user_id, 'billing_account', $account );
            
            // update user in design tool
            $body = [
                'user_id'       => $user_id,
                'firstName'     => $firstName,
                'lastName'      => $lastName,
                'company'       => $companyName,
                'account'       => $account,
                'email'         => $userEmail,
                'domain'        => $website,
                'accessToken'   => 'token123',
                'connector'     => 'wordpress',
            ];

            $body = wp_json_encode( $body );

            $argsApi = [
                'method'      => 'POST',
                'body'        => $body,
                'headers'     => [
                    'Content-Type' => 'application/json',
                ],
                'timeout'     => 60,
                'redirection' => 5,
                'blocking'    => true,
                'httpversion' => '1.0',
                'sslverify'   => false,
                'data_format' => 'body',
            ];
            $requestApiUrl = $request_uri.'/api/registration/update';
            $responseData = wp_remote_post( $requestApiUrl, $argsApi );
            
            $responseUserReg = $responseData['body'];
            $userUpdateArr = json_decode($responseUserReg, TRUE);

            if( $userUpdateArr['status'] == 200 ){
                if( !empty($user_data) ){
                    echo '<div id="regSuccess"><p style="font-size:14px;line-height:1;"> User Information Updated Successfully.</p></div>';
                }
            }else{
                echo '<div id="regError"><p style="font-size:14px;line-height:1;">User not updated please check you have entered right information.</p></div>';
            }
        }
        header("Refresh:3");
    }

    if(isset($signUpError)){
    ?>
        <div id="regError"><p style="font-size:14px;line-height:1;"><strong>ERROR</strong>: <?php echo esc_html($signUpError); ?><br /></p></div>
    <?php
        }
    ?>
    </div>
    <?php if( !empty($aakronDesignToolUser) ){ ?>
    <div class="aakron-design-tool-main">
        <div id="js_design_tool_status" class="design_tool_status">
            
                <?php
                    if( get_option('aakron_design_tool_access_toekn') !== false ){
                        $userDesignToolAccessToken  = get_option( 'aakron_design_tool_access_toekn' );
                    }
                ?>
                <h3><strong>AAkron Personalization PlugIn Status</strong></h3>
                <label>User Access Token <span class="error">*</span></label>
                <input style="width:50%;" type="text" id="js_accesstoken" name="user_access_token" value="<?php if( !empty($userDesignToolAccessToken) ){ echo esc_attr($userDesignToolAccessToken); } ?>">
                <br/>
                <input type="submit" id="user_verify" name="user_verify" value="Active" />
                <input type="submit" id="user_token_remove" name="user_token_remove" value="Remove" />
                <?php
                    echo '<img id="js_verify_spinner" src="'.plugin_dir_url( __FILE__ ).'/images/verify_loader.gif" style="display:none;width:50px;height:40px;vertical-align: middle;">';
                    echo '<div id="regSuccess" style="display:none;"><p style="font-size: 14px; line-height: 1;"> Access Token verified Successfully.</p></div>';
                    echo '<div id="regError" style="display:none;"><p style="font-size: 14px; line-height: 1;">Provided Access Token is not valid. Please check and Enter again.</div>';
                    echo '<div id="regImpError" style="display:none;"><p style="font-size: 14px; line-height: 1;">Access Token could not be Empty. Please Enter a valid Access Token.</p></div>';
                    echo '<div id="removeTokenSuccess" style="display:none;"> </div>';

                    $userDesignToolAccessToken  = get_option( 'aakron_design_tool_access_toekn' );
                    $userDesignToolStatus       = get_option( 'is_design_tool_active' );
                    if( !empty($userDesignToolStatus) ){
                        echo '<h4>Token Validation Status</h4>';
                        echo "<button class='btn-active'>Active</button>";
                    }else{
                        echo '<h4><strong>Token Validation Status</strong></h4>';
                        echo "<button class='btn-pending'>Inactive</button>";
                    }
                ?> 
        </div>
    </div>
    <?php } ?>


    <?php 
    }

    /*
     * order email message body html create
     * callback function
    */
    public function messageBodyHtml($user_name){
        $tableHtml  = '';
        $tableHtml .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#fff"><tbody><tr><td align="center" valign="top">';
            $tableHtml .= '<table width="650" border="0" cellspacing="0" cellpadding="0" class=""> <tbody><tr> <td class="td container" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;width:650px;font-size: 14px;line-height: initial;margin:0;font-weight:normal;padding:0px 0px 0px 0px;">';
            
            // Header 
            $tableHtml .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:15px ;"><tbody><tr>';   
            $tableHtml .= '<td class="" style=" padding:10px; margin:0; font-weight:normal; vertical-align:top; font-size:30pt;   text-align:left;" >
                        <img style="width:200px;" src="'.plugin_dir_url( __FILE__ ).'images/logo.png">
                    </td>';
            $tableHtml .= '</tr></tbody></table>';
            // END Header

            // Article Image On The Left
            $tableHtml .= '<table width="100%" border="0" cellspacing="0" cellpadding="0"> <tbody>';
                $tableHtml .= '<tr><td>';
                    $tableHtml .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f6f9fc" style="color:#535e7d;font-family:"Roboto", Arial,sans-serif;font-size:16px;line-height:28px;text-align:left;border:1px solid #e6ebf1;padding: 25px 30px;"> <tbody>';
                        $tableHtml .= '<tr><td style=" padding: 25px 30px;">';
                            $tableHtml .= '<p><strong>Hello, <br>'.$user_name.'</strong></p><br>';
                            $tableHtml .= '<p>Weve received your registration information. Please give us up to 24 hours to verify your account for approval.</p>';
                            $tableHtml .= '<p>Once approved, you will receive an e-mail confirmation to this e-mail address. To ensure that you receive our e-mail notifications in your inbox, please add us to your contact list.</p>';
                            $tableHtml .= '<p>Regards,</p>';
                            $tableHtml .= '<p>Aakronline Team</p>';
                        $tableHtml .= '</td></tr>';
                    $tableHtml .= '</tbody></table>';
                $tableHtml .= '</td></tr>';
            $tableHtml .= '</tbody></table>';

            $optionsSocialMedia         = get_option( 'aakron_design_options' );
            $copyrightText              = $optionsSocialMedia['aakron_product_design_tool_copyright_text'];

            $tableHtml .= '<table width="100%" border="0" cellspacing="0" cellpadding="0"> <tbody><tr><td class="" style="padding: 0px;">';
                $tableHtml .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#e3e3e3"  >
                        <tbody>';
                        $tableHtml .= '<tr>
                            <th class="" style="font-size:18px;line-height:0pt;padding: 15px;margin:0;font-weight:normal;">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tbody>
                                        <tr>
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

}