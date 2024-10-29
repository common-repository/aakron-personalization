<?php

/**
 * The admin-facing functionality of the plugin to sync Products.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-facing stylesheet and JavaScript.
 *
 * @package    Aakron_Personalization
 * @subpackage Aakron_Personalization/sync
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Aakron_Design_Api_Sync_Products {

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

    private $package_api_url;

    private $offer_api_url;

    private $package_cat_id;

    private $offer_cat_id;


    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $options                    = get_option( 'aakron_design_options' );
        $this->plugin_name          = $plugin_name;
        $this->version              = $version;
        //$this->aakron_design_product_list_api_url  = $options['aakron_design_product_list_api'];
    }
    /**
     * Product Sync from API 
     * callback function
     */
    public function syncProducts(){
      $requestUri   = 'https://apipersonalization.aakronline.com/api/products/getAllProductsList';
      $productsResponseArr = "";
      $sku_list = "";
      if( isset($_POST['sku_list']) && !empty($_POST['sku_list']) ){
        $sku_list = sanitize_text_field($_POST['sku_list']);
        $request_parms = '?sku_id='.$sku_list;
        $request_uri = $requestUri.$request_parms;
        $productsResponseArr = $this->jsonResponse($request_uri);
        $this->syncAllProducts($requestUri,$sku_list,$productsResponseArr);
        $data_response = "<div class='sync-success'><p> Products Sync From API Completed Successfully.</p></div>";
        $status = 1;
        $result['type'] = ($status == 0 ? "failed" : "success");
        $result['data'] = $data_response;
        return $result;
      }else{
        $request_uri = $requestUri;
        $productsResponseArr = $this->jsonResponse($request_uri);
        $this->syncAllProducts($requestUri,$sku_list,$productsResponseArr);
        $data_response = "<div class='sync-success'><p> Products Sync From API Completed Successfully.</p></div>";
        $status = 1;
        $result['type'] = ($status == 0 ? "failed" : "success");
        $result['data'] = $data_response;
        return $result;
      }
    }

    /**
     * get response json from api request
     * callback function
     */
    public function jsonResponse($request_uri){
      $argsApi = [
          'headers'     => [
              'Content-Type' => 'application/json',
          ],
          'timeout'     => 300,
          'redirection' => 10,
          'blocking'    => true,
          'httpversion' => '1.0',
          'sslverify'   => false,
      ];
      $response = wp_remote_get($request_uri,$argsApi);
      $responseBody = wp_remote_retrieve_body( $response );
      $productsJsonResponseArr = json_decode($responseBody,true);
      $productsResponseArr    = $productsJsonResponseArr['response']['data']['orgData'];
      return $productsResponseArr;
    }

    /**
     * sync all product or sku wise import
     * callback function
     */
    public function syncAllProducts($request_uri,$sku_list,$productsResponseArr){
      $request_uri;
      $page              = 1;
      $start             = 0;
      $limit             = 10;
      $totalCountAray    = $productsResponseArr[0]['total'];
      $totalProductCount = $totalCountAray[0]['count'];
      $totalProductPages = round($totalProductCount / 10) + 1;
      $syncLogData = null;
      $syncLogData = array();
      

      do{
        /*if($page == 3){
          break;
        }*/

        if( !empty($sku_list)){
          $productRequestParms = '?sku_id='.$sku_list;
        }else{
          $productRequestParms = '?start='.$start.'&limit='.$limit;
        }
      
        $productRequestUri = $request_uri.$productRequestParms;
        $response = wp_remote_get($productRequestUri);
        $productsResponse = wp_remote_retrieve_body( $response );
        $productsJasonResponseData = json_decode($productsResponse,true);
        $productsJasonArr     = $productsJasonResponseData['response']['data']['orgData'];

        if($page == 1){
          $start = 1;
        }
        $start = $start + 10;
        $limit = $limit + 10;

        $product_response = "";
        $counter = 1;
        if(!empty($productsJasonArr)){
          if( !empty($productsJasonArr[0]['list']) ){
            foreach ($productsJasonArr[0]['list'] as $productsArr){
              try {
                  $product_id = "";
                  $productSku = $productsArr['basic_information']['sku_number'];
                  $product_id = wc_get_product_id_by_sku( $productSku );

                  if( empty($product_id) ){
                      $product_response = $this->CreateProduct( $productsArr );
                      $product_id =  $product_response['product_id'];
                      if(!empty($product_id) && $product_id > 0 ) {
                          $logContentText = "Successfully Sync with Product #ID : {".$product_id."} and Product #SKU : {".$productSku."}";
                          if( !empty($logContentText) && $logContentText !== null ){
                              array_push($syncLogData,$logContentText);
                          }   
                      }else{
                          $logContentText = "Failed Sync with Product SKU : {".$productSku."}";
                          if( !empty($logContentText) && $logContentText !== null ){
                              array_push($syncLogData,$logContentText);
                          }
                      }
                  }else{
                      $product_response = $this->updateProduct( $productsArr, $product_id );
                      $logContentText = "Product Aleady Exists. Updated with Product #ID : {".$product_id."} and Product SKU : {".$productSku."}";
                      if( !empty($logContentText) && $logContentText !== null ){
                          array_push($syncLogData,$logContentText);
                      }
                  }
              }
              catch (Exception $e) {
                  $status = 0;
              }
              $counter++;
            }
          }else{
            $logContentText = "Provided product SKU ID not available in API.";
            if( !empty($logContentText) && $logContentText !== null ){
                array_push($syncLogData,$logContentText);
            }   
          }
        }
        $page = $page + 1;
      }while($page <= $totalProductPages);
      $this->createSynLogFile($syncLogData);
    }

    /**
     * create log file after product sync is completed
     * callback function
     */
    public function createSynLogFile($syncLogData){
        $uploads  = wp_upload_dir( null, false );
        $logs_dir = $uploads['basedir'] . '/aakron-design-tool-logs/';

        if ( ! is_dir( $logs_dir ) ) {
            mkdir( $logs_dir, 0766, true );
        }
        $fileName = 'Flow_design_tool_logs_'.date('m-d-Y_hia').'.log';
        $logsFileName = $logs_dir.$fileName;
        $writeLogFile = fopen($logsFileName, "w") or die("Unable to open file!");
        $counter = 1;
        foreach( $syncLogData as $fileLogContent ){
          $fileLogContent = $counter.'. '.$fileLogContent.PHP_EOL;
          fwrite($writeLogFile, $fileLogContent);
          $counter++;
        }

        fclose($myfile);
        global $wpdb;
        $date = date('d-m-Y');
        $time = date('h:i:a');
        $dateTime = $date.' - '.$time;
        $uploadsDir = wp_get_upload_dir();
        $syncFileUrl = $uploadsDir['baseurl'].'/aakron-design-tool-logs/'.$fileName;
        $syncTableName = $wpdb->prefix.'product_sync_log';
        $data = array(
          'created_date' => $dateTime, 
          'file' => $fileName, 
          'download_url' => $syncFileUrl
        );
        $format = array('%s','%s','%s');
        $wpdb->insert($syncTableName,$data,$format);
        $my_id = $wpdb->insert_id;
    }

    /**
     * Generate table of log files 
     * callback function
     */
    public function syncLogsTbale(){
      global $wpdb;
      if( isset($_GET['paged']) ){
      	  $paged  = sanitize_text_field($_GET['paged']);
      }else{
          $paged  = 1;
      }
   
      // init html output
      $html = '';
   
      // initial link for pagination.
      // "page" must be the  menu slug / clean url from the add_menu_page
      $link = 'admin.php?page=aakron_design_tool';
      $syncLogTableName = $wpdb->prefix.'product_sync_log';
      $rows = $wpdb->get_results( "SELECT * FROM $syncLogTableName");
      $rows = array_reverse($rows);
      $rows_per_page = 10;

      // add pagination arguments from WordPress
      $pagination_args = array(
          'base' => add_query_arg('paged','%#%'),
          'format' => '',
          'total' => ceil(sizeof($rows)/$rows_per_page),
          'current' => $paged,
          'show_all' => false,
          'type' => 'plain',
      );

      $start = ($paged - 1) * $rows_per_page;
      $end_initial = $start + $rows_per_page;
      $end = (sizeof($rows) < $end_initial) ? sizeof($rows) : $end_initial;

      // if we have results
      if (count($rows) > 0) {
          // prepare link for pagination
          $link .= '&paged=' . $paged;

          // html table head
          $html .= '<table id="user-sent-mail" class="wp-list-table widefat fixed users">
                  <thead>
                  <tr class="manage-column">
                      <th>
                          No.
                      </th>
                      <th>
                          Date & Time
                      </th>
                      <th>
                          File
                      </th>
                      <th>
                          Download
                      </th>
                  </tr>
                  </thead>
                  <tbody>
                  ';

          // add rows
          for ($index = $start; $index < $end;  ++$index) {
              $count = $index + 1;
              $row = $rows[$index];
              $class_row = ($index % 2 == 1 ) ? ' class="alternate"' : '';
              $html .= '
                  <tr ' . $class_row . '>
                      <td>' . $count . '</td>
                      <td>' . $row->created_date . '</td>
                      <td>' . $row->file . '</td>
                      <td><a href='.$row->download_url.' download> Download</a></td>
                      <td class="delete-log" style="display:none;"><a name=delete_log href="'.$link.'&log_id='.$row->id.'&file='.$row->download_url.'">Delete</a></td>
                  </tr>';
          }

          $html .= '</tbody></table>';

          // add pagination links from WordPress
          $html .= paginate_links($pagination_args);
          // print form + table
          echo $html;
      } else {
          echo '<p style="color:#FF0000;"> No product import records found  ! </p>';
      } // endif count($rows)
      
      if (isset($_GET['log_id'])){
        $log_id   = sanitize_text_field($_GET['log_id']);
        $filePath = sanitize_text_field($_GET['file']);
        
        wp_delete_file( $filePath ); //delete file here.
        $wpdb->delete( $syncLogTableName, array( 'id' => $log_id ) );
      }
      
      
      die;
    }

    /**
     * Add where conditions for dates
     * 
     * @param array $filter date_begin | date_end
     * @return string
     */
    public function example_table_page_filter($filter){
        $conditions = '';
        if (isset($filter) && count($filter)>0) {
            if (isset($filter['date_begin']) && $filter['date_begin'] 
                && checkdate(
                    substr($filter['date_begin'], 5,2),
                    substr($filter['date_begin'], 8,2),
                    substr($filter['date_begin'], 0,4)
                    )) {
                $conditions .= ' AND DATE_FORMAT(m.created,"%Y-%m-%d") 
                    >= DATE_FORMAT("'.$filter['date_begin'].'","%Y-%m-%d") ';
            }
            if (isset($filter['date_end']) && $filter['date_end'] 
                    && checkdate(
                    substr($filter['date_end'], 5,2),
                    substr($filter['date_end'], 8,2),
                    substr($filter['date_end'], 0,4)
                    )) {
                $conditions .= ' AND DATE_FORMAT(m.created,"%Y-%m-%d") 
                    <= DATE_FORMAT("'.$filter['date_end'].'","%Y-%m-%d") ';
            }
        }
        return $conditions;
    }

    /**
     * create product with basic information
    */
    public function CreateProduct( $productsArr, $product_id = null){
        if( $productsArr['is_deleted'] == FALSE ){
          $productBasicInfo = $productsArr['basic_information'];
          $result = array();
          $error = array();
          try {
                  $new_simple_product = new WC_Product_Simple();
                  $new_simple_product->set_name($productBasicInfo['title']);
                  $new_simple_product->set_description($productBasicInfo['description']);
                  $new_simple_product->set_sku($productBasicInfo['sku_number']);
                  $regularPrice = $productsArr['pricing']['decorative'][0]['rate'];
                  $new_simple_product->set_regular_price($regularPrice);
                  if( $productBasicInfo['pending'] == TRUE ){
                    $productBasicInfo['pending'] = 'draft';
                  }elseif( $productBasicInfo['active'] == TRUE){
                    $productBasicInfo['pending'] = 'publish';
                  }else{
                    $productBasicInfo['pending'] = 'publish';
                  }
                  $new_simple_product->set_status($productBasicInfo['pending']);
                  
                  $product_id = $new_simple_product->save();

          } catch (Exception $ex) {
              $error[] = $ex->getMessage();
          }
          if(isset($product_id)){
              $this->updateMetaData( $product_id, $productsArr);
              $this->addProductBlankImages( $product_id, $productsArr['blankImages']['image']);
              $this->addProductAttributes( $product_id, $productsArr['attributes'] );
              $this->mapProductCategories( $product_id, $productsArr['category'] );
              $this->productDynamicPricing( $product_id, $productsArr['pricing'] );
          }

          $result['error']        = $error;
          $result['product_id']   = $product_id;

          return $result;
        }
    }

    /**
     * update product with basic information
    */
    public function updateProduct( $productsArr, $product_id){
        if( $productsArr['is_deleted'] == FALSE ){
          $productBasicInfo = $productsArr['basic_information'];
          $result = array();
          $error = array();
          if( $productBasicInfo['pending'] == TRUE ){
            $productBasicInfo['pending'] = 'draft';
          }elseif( $productBasicInfo['active'] == TRUE){
            $productBasicInfo['pending'] = 'publish';
          }else{
            $productBasicInfo['pending'] = 'publish';
          }
          try {
            $productBasicData = array(
                'post_author'  => 1,
                'ID'           => $product_id,
                'post_type'    => 'product',
                'post_status'  => $productBasicInfo['pending'],
                'post_title'   => $productBasicInfo['title'],
                'post_content' => $productBasicInfo['description'],
                'post_name'    => $productBasicInfo['slug']
            );
           
            // Update the product into the database
            wp_update_post( $productBasicData );
          } catch (Exception $ex) {
              $error[] = $ex->getMessage();
          }

          if(isset($product_id)){
              wp_set_object_terms( $product_id, 'simple', 'product_type' );
              update_post_meta( $product_id, '_stock_status', 'instock');
              update_post_meta( $product_id, '_visibility', 'visible' );
              $regularPrice = $productsArr['pricing']['decorative'][0]['rate'];
              update_post_meta( $product_id, '_regular_price', $regularPrice );
              update_post_meta( $product_id, '_price', $regularPrice );
              $this->updateMetaData( $product_id, $productsArr);
              $this->addProductBlankImages( $product_id, $productsArr['blankImages']['image']);
              $this->addProductAttributes( $product_id, $productsArr['attributes'] );
              $this->mapProductCategories( $product_id, $productsArr['category'] );
              $this->productDynamicPricing( $product_id, $productsArr['pricing'] );
          }

          $result['error']        = $error;
          $result['product_id']   = $product_id;

          return $result;
        }
    }

    /**
     * add / update dynamic pricing product
    */
    function productDynamicPricing($product_id,$product_data){
      $priceArray = null;
      $regularPrice = null;
      $productDecorativeArr = $product_data['decorative'];
      $i = 1;
      foreach ($productDecorativeArr as $pricingArray ) {
        if($pricingArray['to'] == null){
          $pricingArray['to'] = '*';
        }
        $priceArray[$i]['from'] = $pricingArray['from'];
        $priceArray[$i]['to'] = $pricingArray['to'];
        $priceArray[$i]['type'] = "fixed_price";
        $priceArray[$i]['amount'] = $pricingArray['rate'];
        $i++;
      }
      $arrayCollection= array(
              1=>array(           
                      'conditions_type'=>'all',
                      'conditions'=>array(1=>array(
                      'type'=>'apply_to',
                      'args'=>array(
                              'applies_to'=>'everyone'
                              )               
                      )
              ),                                      
              //'code_value' => $codeValue,
              'collector'=>array(
              'type'=>'product'
              ),
              'rules'=>$priceArray                
              )
      );
      update_post_meta( $product_id, '_pricing_rules', $arrayCollection );
    }
    /**
     * add / update meta data for product
    */
    public function updateMetaData( $product_id, $meta_data){
        if(empty($product_id))
            return;

        if(!empty($meta_data)){
            /* ADD CUSTOM META FOR PRODUCT IS VIRTUAL DESIGN TOOL PRODUCT*/
            $is_design_tool_product = 1;
            update_post_meta( $product_id, 'is_design_tool_product', $is_design_tool_product);
            unset($meta_data['deleted_at']);
            unset($meta_data['is_vmc']);
            unset($meta_data['rating']);
            unset($meta_data['total_reviews']);
            if( $meta_data['is_deleted'] == FALSE ){
              $meta_data['is_deleted'] = 0;
            }else{
              $meta_data['is_deleted'] = 1;
            }

            if( $meta_data['basic_information']['pending'] == FALSE ){
              $meta_data['basic_information']['pending'] = 0;
            }else{
              $meta_data['basic_information']['pending'] = 1;
            }

            if( $meta_data['basic_information']['active'] == FALSE ){
              $meta_data['basic_information']['active'] = 0;
            }else{
              $meta_data['basic_information']['active'] = 1;
            }

            if( $meta_data['images']['is_default'] == FALSE ){
              $meta_data['images']['is_default'] = 0;
            }else{
              $meta_data['images']['is_default'] = 1;
            }

            if( $meta_data['decorative_pricing'] == FALSE ){
              $meta_data['decorative_pricing'] = 0;
            }else{
              $meta_data['decorative_pricing'] = 1;
            }

            if( $meta_data['blank_pricing'] == FALSE ){
              $meta_data['blank_pricing'] = 0;
            }else{
              $meta_data['blank_pricing'] = 1;
            }

            if( $meta_data['special_pricing'] == FALSE ){
              $meta_data['special_pricing'] = 0;
            }else{
              $meta_data['special_pricing'] = 1;
            }

            if( $meta_data['special_blank_pricing'] == FALSE ){
              $meta_data['special_blank_pricing'] = 0;
            }else{
              $meta_data['special_blank_pricing'] = 1;
            }

            if( $meta_data['is_size'] == FALSE ){
              $meta_data['is_size'] = 0;
            }else{
              $meta_data['is_size'] = 1;
            }
            
            foreach ($meta_data as $meta_key => $meta_value ){
                if(!is_array($meta_value)){
                  update_post_meta( $product_id, $meta_key, $meta_value );
                }else{
                  $productMetaData = json_encode($meta_value);
                  update_post_meta( $product_id, $meta_key, $productMetaData );
                }
            }
        }
    }

    /**
     * add product feature image
    */
    public function addProductImage($product_id, $productBasicInfoArray){
      $imageFileUrl        = $productBasicInfoArray['default_image_url'];
      $imageFileName       = substr($imageFileUrl, strrpos($imageFileUrl, '/' )+1)."\n";
      $imageFile           = pathinfo($imageFileName, PATHINFO_FILENAME); 
      $featuredCheck       = null;
      if(!empty($imageFile)){
        $featuredArgs = array(
            'posts_per_page' => 1,
            'post_type'      => 'attachment',
            'name'           => $imageFile
        );
        $featuredCheck = get_posts($featuredArgs);
      }
      
      // check if image attachment is already exists
      if ( !empty($featuredCheck) ) {
          $attchmentId = $featuredCheck[0]->ID;
          set_post_thumbnail($product_id, $attchmentId);
      }else{
          if( !has_post_thumbnail( $product_id ) ){
              // Attcah image to product using magic media_sideload_image
              $media = media_sideload_image($imageFileUrl, $product_id);
              $args = array(
                      'post_type' => 'attachment',
                      'posts_per_page' => -1,
                      'post_status' => 'any',
                      'post_parent' => $product_id
              );
              $attachments = get_posts($args);
              if(isset($attachments) && is_array($attachments)){
                      foreach($attachments as $attachment){
                              // Grab full size image from source
                              $image = wp_get_attachment_image_src($attachment->ID, 'full');
                              // determine if in the $media image we created, the string of the URL exists
                              if(strpos($media, $image[0]) !== false){
                                      // if so, we found our image. set it as thumbnail
                                      set_post_thumbnail($product_id, $attachment->ID);
                                      // only want one image
                                      // Set the image Alt-Text
                                      update_post_meta( $attachment->ID, '_wp_attachment_image_alt', $galleryImgAltText );
                                      break;
                              }
                      }
              }
          }
      }
    }

    /**
     * add product gallery images
    */
    public function addProductGalleryImages($product_id, $productGalleryImages){
      // Remove first element of images array we used as featured image 
      unset($productGalleryImages[0]); 
      // Re-index the array elements 
      $productGalleryImagesArr = array_values($productGalleryImages); 
      $arrIds = null;
      $arrIds = array();
      foreach($productGalleryImagesArr as $imageValue){
        $galleryImgNameUrl   = $imageValue['image_path'];
        $galleryImgName      = preg_replace('/[^a-zA-Z0-9-_\.]/','', $galleryImgNameUrl);
        $galleryImage      = pathinfo($galleryImgName, PATHINFO_FILENAME);
        $galleryImgAltText = $imageValue['actual_color'];
        // check if image attachment already exists
        $attachment_check = null;
        if(!empty($galleryImage)){
          $attachment_args = array(
              'posts_per_page' => 1,
              'post_type'      => 'attachment',
              'name'           => $galleryImgName
          );
          $attachment_check = get_posts($attachment_args);
        }

        // if attachment exists, reuse and update data
        if ( !empty($attachment_check) ) {
            $attach_id = $attachment_check[0]->ID;
            $arrIds[] = $attach_id;
            // do stuff..
            // if attachment doesn't exist fetch it from url and save it
        } else {
            $galleryImageUrl     = $imageValue['url'];
            $upload_dir               = wp_upload_dir(); // Set upload folder
            $gallery_img_data         = file_get_contents($galleryImageUrl); // Get image data
            $gallery_unique_file_name = wp_unique_filename( $upload_dir['path'], $galleryImgNameUrl ); // Generate unique name
            $gallery_filename         = basename( $gallery_unique_file_name );// Create image file name
            
            // Check folder permission and define file location
            if( wp_mkdir_p( $upload_dir['path'] ) ) {
                    $gallery_file = $upload_dir['path'] . '/' . $gallery_filename;
            } else {
                    $gallery_file = $upload_dir['basedir'] . '/' . $gallery_filename;
            }
            // Create the image  file on the server
            file_put_contents( $gallery_file, $gallery_img_data );
            // Check image file type
            $wp_filetype = wp_check_filetype( $gallery_filename, null );
            // Set attachment data
            $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title'     => sanitize_file_name( $gallery_filename ),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
            );
        
            // Create the attachment
            $attach_id = wp_insert_attachment( $attachment, $gallery_file, $product_id );
            // Set the image Alt-Text
            update_post_meta( $attach_id, '_wp_attachment_image_alt', $galleryImgAltText );
            // Include image.php
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            // Define attachment metadata
            $attach_data = wp_generate_attachment_metadata( $attach_id, $gallery_file );
            // Assign metadata to attachment
            wp_update_attachment_metadata( $attach_id, $attach_data );
            $arrIds[] = $attach_id;
            // And finally assign featured image to product
        }
      }
      /* Set Gallery Images */
      update_post_meta($product_id, '_product_image_gallery',  implode(",",$arrIds));
    }

    /**
     * add product gallery images
    */
    public function addProductBlankImages($product_id, $productBlankImages){
      /* ADD FETURED IMAGE TO PRODUCT */
      $imageFileUrl        = $productBlankImages[0]['imageUrl'];
      $featuredImgLinkArr  = explode('/',$imageFileUrl);
      $featuredImgName     = end($featuredImgLinkArr);
      $featuredImgAltText    = $productBlankImages[0]['color'];
       
      $featuredCheck       = null;
      if(!empty($featuredImgName)){
        $featuredArgs = array(
            'posts_per_page' => 1,
            'post_type'      => 'attachment',
            'name'           => $featuredImgName
        );
        $featuredCheck = get_posts($featuredArgs);
      }
      
      // check if image attachment is already exists
      if ( !empty($featuredCheck) ) {
          $attchmentId = $featuredCheck[0]->ID;
          set_post_thumbnail($product_id, $attchmentId);
      }else{   
        // Attcah image to product using magic media_sideload_image
        $media = media_sideload_image($imageFileUrl, $product_id);
        $args = array(
                'post_type' => 'attachment',
                'posts_per_page' => -1,
                'post_status' => 'any',
                'post_parent' => $product_id
        );
        $attachments = get_posts($args);
        if(isset($attachments) && is_array($attachments)){
                foreach($attachments as $attachment){
                        // Grab full size image from source
                        $image = wp_get_attachment_image_src($attachment->ID, 'full');
                        // determine if in the $media image we created, the string of the URL exists
                        if(strpos($media, $image[0]) !== false){
                                // if so, we found our image. set it as thumbnail
                                set_post_thumbnail($product_id, $attachment->ID);
                                // only want one image
                                // Set the image Alt-Text
                                update_post_meta( $attachment->ID, '_wp_attachment_image_alt', $featuredImgAltText );
                                break;
                        }
                }
        }   
      }

      /*ADD FEATURED IMAGE TO PRODUCT */

      // Remove first element of images array we used as featured image 
      unset($productBlankImages[0]);
      // Re-index the array elements 
      $productGalleryImagesArr = $productBlankImages;
      $arrIds = null;
      $arrIds = array();
      foreach($productGalleryImagesArr as $imageValue){
        $galleryImgNameUrl   = $imageValue['imageUrl'];
        $linkArray = explode('/',$galleryImgNameUrl);
        $galleryImgName = end($linkArray);
        $galleryImgAltText   = $imageValue['color'];
        // check if image attachment already exists
        $attachment_check = null;
         
        if(!empty($galleryImgName)){
          $attachment_args = array(
              'posts_per_page' => 1,
              'post_type'      => 'attachment',
              'name'           => $galleryImgName
          );
          $attachment_check = get_posts($attachment_args);
        }

        // if attachment exists, reuse and update data
        if ( !empty($attachment_check) ) {
            $attach_id = $attachment_check[0]->ID;
            $arrIds[] = $attach_id;
            // do stuff..
            // if attachment doesn't exist fetch it from url and save it
        } else {
            $galleryImageUrl     = $imageValue['imageUrl'];
            $upload_dir               = wp_upload_dir(); // Set upload folder
            $gallery_img_data         = file_get_contents($galleryImageUrl); // Get image data
            $gallery_unique_file_name = wp_unique_filename( $upload_dir['path'], $galleryImgName ); // Generate unique name
            $gallery_filename         = basename( $gallery_unique_file_name );// Create image file name
            
            // Check folder permission and define file location
            if( wp_mkdir_p( $upload_dir['path'] ) ) {
                    $gallery_file = $upload_dir['path'] . '/' . $gallery_filename;
            } else {
                    $gallery_file = $upload_dir['basedir'] . '/' . $gallery_filename;
            }
            // Create the image  file on the server
            file_put_contents( $gallery_file, $gallery_img_data );
            // Check image file type
            $wp_filetype = wp_check_filetype( $gallery_filename, null );
            // Set attachment data
            $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title'     => sanitize_file_name( $gallery_filename ),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
            );
        
            // Create the attachment
            $attach_id = wp_insert_attachment( $attachment, $gallery_file, $product_id );
            // Set the image Alt-Text
            update_post_meta( $attach_id, '_wp_attachment_image_alt', $galleryImgAltText );
            // Include image.php
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            // Define attachment metadata
            $attach_data = wp_generate_attachment_metadata( $attach_id, $gallery_file );
            // Assign metadata to attachment
            wp_update_attachment_metadata( $attach_id, $attach_data );
            $arrIds[] = $attach_id;
            // And finally assign featured image to product
        }
      }
      /* Set Gallery Images */
      update_post_meta($product_id, '_product_image_gallery',  implode(",",$arrIds));
    }

    
    /**
     * insert and assign product categories
    */
    public function mapProductCategories($product_id, $productCategoriesArray){
      if($productCategoriesArray !== null && isset($productCategoriesArray)){
        $productWiseCategoriesData = array();
        foreach($productCategoriesArray as $productCategoryName){
          if ( term_exists($productCategoryName)) {
              $productCategoryTerm = get_term_by('name', $productCategoryName, 'product_cat');
              $productCategoryId   = $productCategoryTerm->term_id;
              $productCategoryName = $productCategoryTerm->name;
              if(!empty($product_id) ){
                  $productWiseCategoriesData[$product_id][] = $productCategoryId;
                  wp_remove_object_terms( $product_id, 'Uncategorized', 'product_cat' );
              }
          }else{
              $productCategoryTerm = wp_insert_term(
                  $productCategoryName, // the term 
                  'product_cat', // the taxonomy
                  array(
                      'description'=> '',
                  )
              ); 
              $productCategoryId = $productCategoryTerm['term_id'];
              if(!empty($product_id) ){
                  $productWiseCategoriesData[$product_id][] = $productCategoryId;
                  wp_remove_object_terms( $product_id, 'Uncategorized', 'product_cat' );
              }
          }
          foreach($productWiseCategoriesData as $productId=>$categoryArray){
              wp_set_object_terms($productId, $categoryArray, 'product_cat');
          }
        }
      }
    }

    /**
     * add product attributes
    */
    public function addProductAttributes($product_id, $productsAttributesArr){
      $attributeDataArray = array();
      foreach( $productsAttributesArr as $attributesArray){ 
        if( !empty($attributesArray) ){
            $productAttributeName   = $attributesArray['label'];
            $productAttributeValue  = $attributesArray['values'];
            $attr = wc_sanitize_taxonomy_name(stripslashes($productAttributeName));
            $attr = 'pa_'.$attr;
            if( taxonomy_exists($attr) ){
              foreach($productAttributeValue as $option){
                wp_set_object_terms($product_id,$option,$attr,true);
              }
            }else{
              $this->create_product_attribute( $productAttributeName,$productAttributeValue );
              foreach($productAttributeValue as $option){
                wp_set_object_terms($product_id,$option,$attr,true);
              }
            }
            $_product_attributes[$attr] = array(
              'name' => $attr,
              'value' => '',
              'position' => '',
              'is_visible' => 0,
              'is_variation' => 1,
              'is_taxonomy' => 1
            );
            update_post_meta($product_id, '_product_attributes', $_product_attributes);
        }
      }
    }

    /**
     * create product attributes
    */
    public function create_product_attribute( $raw_name, $terms ){
      global $wpdb, $wc_product_attributes;

      // Make sure caches are clean.
      delete_transient( 'wc_attribute_taxonomies' );
      WC_Cache_Helper::incr_cache_prefix( 'woocommerce-attributes' );

      // These are exported as labels, so convert the label to a name if possible first.
      $attribute_labels = wp_list_pluck( wc_get_attribute_taxonomies(), 'attribute_label', 'attribute_name' );
      $attribute_name   = array_search( $raw_name, $attribute_labels, true );

      if ( ! $attribute_name ) {
        $attribute_name = wc_sanitize_taxonomy_name( $raw_name );
      }

      $attribute_id = wc_attribute_taxonomy_id_by_name( $attribute_name );

      if ( ! $attribute_id ) {
        $taxonomy_name = wc_attribute_taxonomy_name( $attribute_name );

        // Degister taxonomy which other tests may have created...
        unregister_taxonomy( $taxonomy_name );

        $attribute_id = wc_create_attribute(
          array(
            'name'         => $raw_name,
            'slug'         => $attribute_name,
            'type'         => 'select',
            'order_by'     => 'menu_order',
            'has_archives' => 0,
          )
        );

        // Register as taxonomy.
        register_taxonomy(
          $taxonomy_name,
          apply_filters( 'woocommerce_taxonomy_objects_' . $taxonomy_name, array( 'product' ) ),
          apply_filters(
            'woocommerce_taxonomy_args_' . $taxonomy_name,
            array(
              'labels'       => array(
                'name' => $raw_name,
              ),
              'hierarchical' => false,
              'show_ui'      => false,
              'query_var'    => true,
              'rewrite'      => false,
            )
          )
        );

        // Set product attributes global.
        $wc_product_attributes = array();

        foreach ( wc_get_attribute_taxonomies() as $taxonomy ) {
          $wc_product_attributes[ wc_attribute_taxonomy_name( $taxonomy->attribute_name ) ] = $taxonomy;
        }
      }

      $attribute = wc_get_attribute( $attribute_id );
      $return    = array(
        'attribute_name'     => $attribute->name,
        'attribute_taxonomy' => $attribute->slug,
        'attribute_id'       => $attribute_id,
        'term_ids'           => array(),
      );

      foreach ( $terms as $term ) {
        $result = term_exists( $term, $attribute->slug );

        if ( ! $result ) {
          $result = wp_insert_term( $term, $attribute->slug );
          $return['term_ids'][] = $result['term_id'];
        } else {
          $return['term_ids'][] = $result['term_id'];
        }
      }

      return $return;
    }

}