<?php
/**
 * Plugin Name:       Product Manager
 * Plugin URI:        http://sheehanweb.com
 * Description:       Easily bulk upload Products to WooCommerce using a CSV file and manage your inventory.
 * Version:           1.0.0
 * Author:            Zhefan Xu & Joe Sheehan
 * Author URI:        http://sheehanweb.com
 * Text Domain:       stock-manager
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'STOCKDIR', plugin_dir_path( __FILE__ ) );
define( 'STOCKURL', plugin_dir_url( __FILE__ ) );
/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/
require_once( plugin_dir_path( __FILE__ ) . 'public/class-stock-manager.php' );

register_activation_hook( __FILE__, array( 'Stock_Manager', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Stock_Manager', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Stock_Manager', 'get_instance' ) );

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-stock-manager-admin.php' );
	add_action( 'plugins_loaded', array( 'Stock_Manager_Admin', 'get_instance' ) );

}




 
 

  add_action( 'wp_ajax_save_one_product', 'stock_manager_save_one_product_stock_data' ); 

  /**
   * Save one product stock data 
   *
   */        
  function stock_manager_save_one_product_stock_data(){
	
     $product_id   = sanitize_text_field($_POST['product']);
	   $manage_stock = sanitize_text_field($_POST['manage_stock']);
     $stock_status = sanitize_text_field($_POST['stock_status']);
     $backorders   = sanitize_text_field($_POST['backorders']);
     
     $sku          = sanitize_text_field($_POST['sku']);
     $title        = sanitize_text_field($_POST['title']);
     
     $stockadjust  = sanitize_text_field($_POST['stockadjust']);

     

     if(empty($stockadjust) || is_null($stockadjust)){
        $stockadjust=0;
        $stock=$_POST['stock'];
     } else {
        $stockadjust=intval($_POST['stockadjust']);
        $stock=intval($stock=$_POST['stock']);
        $stock=$stock+$stockadjust;
     }

     if($stock>0){
        $stock_status='instock';
     }else{
        $stock_status='outofstock';
     }

     $product_meta=get_post_meta($product_id);

     $invlocation='';
     if(array_key_exists("_invlocation_field", $product_meta)){
          $invlocation  = sanitize_text_field($_POST['invlocation']);
          update_post_meta($product_id, '_invlocation_field', $invlocation);
     } 

     update_post_meta($product_id, '_manage_stock', $manage_stock);
     update_post_meta($product_id, '_stock_status', $stock_status);
     update_post_meta($product_id, '_backorders', $backorders);
     update_post_meta($product_id, '_stock', $stock);

     //Store Stock Adjust History into database fan_stockadjust

      global $current_user;
      get_currentuserinfo();

      $username=$current_user->user_login;

      global $wpdb;
      $querystr="
        INSERT INTO fan_stockadjust
        (Post_ID,sku,title,Adjust_Stock,After_Adjust_Stock,Inv_Location,Update_By)
        VALUES (%s,%s,%s,%s,%s,%s,%s)
      ";

          $wpdb->query($wpdb->prepare($querystr,array($product_id,$sku,$title,$stockadjust,$stock,$invlocation,$username)));

     echo $stock;

     wp_die();
  }  



  add_action( 'wp_ajax_import_simple_product', 'fan_import_simple_product' ); 

  /**
   * import simple product
   *
   */        
  function fan_import_simple_product(){
    
    //Get File path and read the csv file, store the data in variable
    $filepath=$_POST['filepath'];
    $file = fopen($filepath,"r");

    $products=array();

    while(!feof($file))
      {
        $products[]=fgetcsv($file);
      }

    fclose($file);

    //Get Producst column number from csv file
    $headers=$products[0]; //Headers from csv file, which provide column number

    $hcol=array(

                        'post_name'=>'',
                        'post_title'=>'',
                        'post_content'=>'',
                        'post_excerpt'=>'',
                        'post_status'=>'',
                        'virtual'=>'',
                        'downloadable'=>'',
                        'visibility'=>'',
                        'regular_price'=>'',
                        'sale_price'=>'',
                        'sku'=>'',
                        'manage_stock'=>'',
                        'stock'=>'',
                        'backorders'=>'',
                        'stock_status'=>'',
                        'weight'=>'',
                        'length'=>'',
                        'width'=>'',
                        'height'=>'',
                        'product_image'=>'',

    );

    $hattrs=array();

     foreach($headers as $k=>$v){
        foreach($hcol as $bk=>$bv){
            if(strtoupper($bk)==strtoupper($v)){
                $hcol[$bk]=$k;
            }

        }
        if(strpos($v,'attribute:')!==FALSE){
            $attr=substr($v,-(strlen($v)-strlen('attribute:')));
            $hattrs[$attr]=array($k);            
        }
        if(strpos($v,'attribute_data:')!==FALSE){
            $attr_data=substr($v,-(strlen($v)-strlen('attribute_data:')));
            $hattrs[$attr_data][]=$k;               
        }
    }
    //echo var_dump($hcol);
    //echo var_dump($hattrs);


    //Below is to insert new product or update product if sku exist
    global $wpdb;
    global $post;
    
    $data_size=count($products);
    //echo "Data size: ".$data_size;


    for( $i=1; $i<$data_size; $i++){
        $post_name=$products[$i][$hcol['post_name']];
        $post = array(
            'post_title'   => $products[$i][$hcol['post_title']],
            'post_content' => $products[$i][$hcol['post_content']],
            'post_status'  => $products[$i][$hcol['post_status']],
            'post_excerpt' => $products[$i][$hcol['post_excerpt']],
            'post_name'    => $post_name, //name/slug
            'post_type'    => "product"
        );

        $post_id=$wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name like '$post_name'");

        if(empty($post_id)){
            $post_id = wp_insert_post($post,$wp_error);
            $count_new=$count_new+1;
        }else{
            $count_update=$count_update+1;
        }
        //echo "Post ID:".$post_id;

        //Set Product Values

         update_post_meta($post_id, '_virtual'      , $products[$i][$hcol['virtual']]);
         update_post_meta($post_id, '_downloadable' , $products[$i][$hcol['downloadable']]);
         update_post_meta($post_id, '_visibility'   , $products[$i][$hcol['visibility']]);

         update_post_meta($post_id, '_sku'          , $products[$i][$hcol['sku']]);
         update_post_meta($post_id,'_regular_price' ,$products[$i][$hcol['regular_price']]);


         $sale_price=$products[$i][$hcol['sale_price']];
         update_post_meta($post_id,'_sale_price'    ,$sale_price);

         if(empty($sale_price)){
            update_post_meta($post_id,'_price' ,$products[$i][$hcol['regular_price']]);
         }else{

            update_post_meta($post_id,'_price' ,        $sale_price);
 
         }
      
         update_post_meta($post_id,'_manage_stock'  ,$products[$i][$hcol['manage_stock']]);
         update_post_meta($post_id,'_stock'         ,$products[$i][$hcol['stock']]);
         update_post_meta($post_id,'_backorders'    ,$products[$i][$hcol['backorders']]);
         update_post_meta($post_id,'_stock_status'  ,$products[$i][$hcol['stock_status']]);

         update_post_meta($post_id,'_weight'        ,$products[$i][$hcol['weight']]);
         update_post_meta($post_id,'_length'        ,$products[$i][$hcol['length']]);
         update_post_meta($post_id,'_width'         ,$products[$i][$hcol['width']]);
         update_post_meta($post_id,'_height'        ,$products[$i][$hcol['height']]);


        //Below is to set attributes
        $product_attributes=array();

        foreach($hattrs as $attr=>$pos){
            $attr_name=$attr;
            $attr_setting=explode("|",$products[$i][$pos[1]]);

            $attr_position=$attr_setting[0];
            $attr_visible=$attr_setting[1];
            $attr_variation=$attr_setting[2];

            //echo var_dump($attr_setting);
            $product_attributes[$attr_name]=array(
                'name'          =>  $attr_name,
                'value'         =>  $products[$i][$pos[0]],
                'position'      =>  $attr_position,
                'is_visible'    =>  $attr_visible,
                'is_variation'  =>  $attr_variation,
                'is_taxonomy'   =>  0
            );

        }
        update_post_meta($post_id, '_product_attributes', $product_attributes);


        //Below is to set image
        $toupdateimage=$_POST['toupdateimage'];
        $updateimage = strtolower($toupdateimage) == 'true' ? true : false;
        $image_msg='';

        if($updateimage){
            if ( !function_exists('media_handle_upload') ) {
              require_once(ABSPATH . "wp-admin" . '/includes/image.php');
              require_once(ABSPATH . "wp-admin" . '/includes/file.php');
              require_once(ABSPATH . "wp-admin" . '/includes/media.php');
            }

            $thumb_url=$products[$i][$hcol['product_image']];
            // Download file to temp location
            $tmp = download_url($thumb_url);
           

            preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $thumb_url, $matches);
            $file_array['name'] = basename($matches[0]);
            $file_array['tmp_name'] = $tmp;

            // If error storing temporarily, unlink
            if (is_wp_error($tmp)) {
                @unlink($file_array['tmp_name']);
                $file_array['tmp_name'] = '';

            }

            //use media_handle_sideload to upload img:
            $thumbid = media_handle_sideload( $file_array, $post_id, $post_name );
            // If error storing permanently, unlink
            if ( is_wp_error($thumbid) ) {
                @unlink($file_array['tmp_name']);

            }
            set_post_thumbnail($post_id, $thumbid);          
          
            $image_msg=" Update image included";
        }else{
                $image_msg=" Update image NOT included";
        }


        do_action( 'woocommerce_process_product_meta_' . 'simple', $post_id );
    }

    echo "Simple Products DONE"."-".$image_msg;
    //echo "Total New: ".$count_new." Total Updated: ".$count_update;
    wp_die();
  }

  add_action( 'wp_ajax_import_variable_product', 'fan_import_variable_product' ); 

  /**
   * import variable product
   *
   */        
  function fan_import_variable_product(){
    
    //Get File path and read the csv file, store the data in variable
    $filepath=$_POST['filepath'];
    $file = fopen($filepath,"r");

    $products=array();

    while(!feof($file))
      {
        $products[]=fgetcsv($file);
      }

    fclose($file);

    //Get Producst column number from csv file
    $headers=$products[0]; //Headers from csv file, which provide column number

    $hcol=array(

                        'post_name'=>'',
                        'parent_postname'=>'',
                        'post_status'=>'',
                        'virtual'=>'',
                        'downloadable'=>'',
                        'regular_price'=>'',
                        'sale_price'=>'',
                        'sku'=>'',
                        'manage_stock'=>'',
                        'stock'=>'',
                        'stock_status'=>'',
                        'weight'=>'',
                        'length'=>'',
                        'width'=>'',
                        'height'=>'',
                        'variation_description'=>'',
                        'product_image'=>'',

    );

    $hattrs2=array();

     foreach($headers as $k=>$v){
        foreach($hcol as $bk=>$bv){
            if(strtoupper($bk)==strtoupper($v)){
                $hcol[$bk]=$k;
            }

        }
        if(strpos($v,'attribute:')!==FALSE){
            $attr=substr($v,-(strlen($v)-strlen('attribute:')));
            $hattrs2[$attr]=$k;            
        }

    }
    //echo var_dump($hcol);
    //echo var_dump($hattrs2);


    //Below is to insert variable product or update variable product if parent_postname exist
    global $wpdb;
    global $post;


    
    $data_size=count($products);
    //echo "Data size: ".$data_size;

    for($i=1;$i<$data_size;$i++){


        $parent_postname=$products[$i][$hcol['parent_postname']];
        $parent_id=$wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name like '$parent_postname'");
        if(empty($parent_id)){
            echo "Parent product ".$parent_postname. "does not exist.";
        } else{
            wp_set_object_terms($parent_id,'variable','product_type');
            $post_name=$products[$i][$hcol['post_name']];
            //echo "Post Name: ".$post_name;
            $variation_id=$wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name like '$post_name'");

            if(empty($variation_id)){
                 $variation = array(
                    'post_title'   => 'Product #' . $parent_id . ' Variation',
                    'post_name'    => $post_name,
                    'post_status'  => $products[$i][$hcol['post_status']],
                    'post_parent'  => $parent_id,
                    'post_type'    => 'product_variation'
                );           
                $variation_id=wp_insert_post($variation);
                $count_new=$count_new+1;
                //echo "Variation ID:".$variation_id;
            }else{
                $count_update=$count_update+1;
            }


            update_post_meta($variation_id, '_virtual'      , $products[$i][$hcol['virtual']]);
            update_post_meta($variation_id, '_downloadable' , $products[$i][$hcol['downloadable']]);
            
            update_post_meta($variation_id, '_regular_price', $products[$i][$hcol['regular_price']] );

             $sale_price=$products[$i][$hcol['sale_price']];
             update_post_meta($variation_id,'_sale_price'    ,$sale_price);

             if(empty($sale_price)){
                update_post_meta($variation_id,'_price' ,$products[$i][$hcol['regular_price']]);
             }else{
                
                update_post_meta($variation_id,'_price' ,        $sale_price);
     
             }

            update_post_meta($variation_id, '_sku'          , $products[$i][$hcol['sku']]);
            update_post_meta($variation_id,'_manage_stock'  ,$products[$i][$hcol['manage_stock']]);
            update_post_meta($variation_id,'_stock'         ,$products[$i][$hcol['stock']]);
            update_post_meta($variation_id,'_stock_status'  ,$products[$i][$hcol['stock_status']]);

            update_post_meta($variation_id,'_weight'        ,$products[$i][$hcol['weight']]);
            update_post_meta($variation_id,'_length'        ,$products[$i][$hcol['length']]);
            update_post_meta($variation_id,'_width'         ,$products[$i][$hcol['width']]);
            update_post_meta($variation_id,'_height'        ,$products[$i][$hcol['height']]);
            
            update_post_meta($variation_id,'_variation_description'        ,$products[$i][$hcol['variation_description']]);

            
            $variation_attributes=array();
            foreach($hattrs2 as $hk=>$hv){
                update_post_meta($variation_id, 'attribute_' .$hk, $products[$i][$hv]);
            }
        }
        //Below is to set image
        $toupdateimage=$_POST['toupdateimage'];
        $updateimage = strtolower($toupdateimage) == 'true' ? true : false;
        $image_msg='';


        if($updateimage){
            if ( !function_exists('media_handle_upload') ) {
              require_once(ABSPATH . "wp-admin" . '/includes/image.php');
              require_once(ABSPATH . "wp-admin" . '/includes/file.php');
              require_once(ABSPATH . "wp-admin" . '/includes/media.php');
            }

            $thumb_url=$products[$i][$hcol['product_image']];
            // Download file to temp location
            $tmp = download_url($thumb_url);
           

            preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $thumb_url, $matches);
            $file_array['name'] = basename($matches[0]);
            $file_array['tmp_name'] = $tmp;

            // If error storing temporarily, unlink
            if (is_wp_error($tmp)) {
                @unlink($file_array['tmp_name']);
                $file_array['tmp_name'] = '';

            }

            //use media_handle_sideload to upload img:
            $thumbid = media_handle_sideload($file_array,$variation_id,$post_name);
            // If error storing permanently, unlink
            if ( is_wp_error($thumbid) ) {
                @unlink($file_array['tmp_name']);

            }
            set_post_thumbnail($variation_id, $thumbid);       

            $image_msg="Update image included";
        }else{
          $image_msg="Update image NOT included";
        }

        // WC_Product_Variable::sync($parent_id);
        // WC_Product_Variable::sync_stock_status($parent_id);

   }



   echo "Variable Products DONE"."-".$image_msg;
   //echo "Total New: ".$count_new." Total Update: ".$count_update;
   wp_die();
  }
  

  add_action( 'wp_ajax_test_code', 'fan_test_code' ); 

  /**
   * import simple product
   *
   */        
  function fan_test_code(){

    $updateimage=$_POST['toupdateimage'];
    $updateimage = strtolower($updateimage) == 'true' ? true : false;

    if($updateimage){
      echo "update image included";
    }else{
      echo "Update image NOT included";
    }

    $a=$_POST['a'];
    if(empty($a)){
      echo "a is empty";
    }else{
      echo "a has value ".$a;
    }
  }


/**
*Restrict file type when use media upload for plugin
*/
// function fan_restrictMimeTypes($mimes) {
//     $mimes = array(
//         'csv' => 'application/csv',
//         'jpg|jpeg|jpe' => 'image/jpeg',
//         'gif' => 'image/gif',
//         'png' => 'image/png',
//         'bmp' => 'image/bmp',
//         'tif|tiff' => 'image/tiff'
//     );

//     return $mimes;
// }

// add_filter('upload_mimes','fan_restrictMimeTypes');