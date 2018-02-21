<?php
/**
 * @package   WooCommerce Product Manager
 * @author    Zhefan Xu & Joe Sheehan
 * @license   GPL-2.0+
 * @link      http://sheehanweb.com
 * @copyright 2016 SheehanWeb.com
 */

class WCM_Stock {

  /**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
  
  /**
	 * Constructor for the stock class.
	 *
	 * @since     1.0.0
	 */
  public $limit = 100; 
   

	/**
	 * Constructor for the stock class.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		
    
	}
  
  /**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
  
  
  /**
   * Return products
   *
   *   
   * @since 1.0.0  
   */        
  public function get_products(){
  



    /*----------------Check for sku first----------------------------------------------------------------*/
    //If sku exist, run thourgh the sku of all the products, if does not match, warning message and show all products


   if(isset($_GET['sku'])&&!empty($_GET['sku'])){

      echo "sku input";
      $sku=$_GET['sku'];
      $args = array();
      $args = array(
            'post_type'       => 'product',
            'post_status'     => 'publish',
            'posts_per_page'  => -1,
            'orderby'         => 'title',
            'order'           => 'ASC',
            'meta_query'      => array(array('key'=>'_sku','value'=> $sku,'compare' => 'LIKE')),
            // 'tax_query'       => array(
            //                             array(
            //                               'taxonomy'  => 'product_type',
            //                               'field'   => 'slug',
            //                               'terms'   => array('simple'),
            //                               'operator'  => 'IN'
            //                             )
            //                     )
              );
        
        $loop = new WP_Query( $args );

        if($loop->have_posts()){

          return $loop->posts;
        } else{
              $args = array();
    
              $args = array(
                'post_type'     => 'product_variation',
                'post_status'     => 'publish',
                    'posts_per_page'  => -1,
                    'orderby'     => 'title',
                    'order'       => 'ASC',
                'meta_query' => array(
                  array(
                    'key'     => '_sku',
                    'value'   => $sku,
                    'compare' => 'LIKE'
                  )
                )
              );
              $loop2= new WP_Query($args);
            //   while($loop2->have_posts()):
            //      $loop2->the_post();
            //      $productv = new WC_Product_Variation( $loop2->post->ID );
            //      $prod_sku=$productv->sku;
                 
            //      if($sku==$prod_sku){
            //         $prod_id=$productv->ID;
            //         echo $prod_id;
            //      }
            //      //echo $productv_children['_sku'][0];
            //      //echo $pro_sku;
            //        // if($sku==$pro_sku){
            //        //   echo "sku found in the variable product";
            //        // }
            //      //echo var_dump($productv);
            // endwhile;
            return $loop2->posts;
             

              // if($loop2->have_posts()){
              //   echo "variable products";
              // }
              //echo var_dump($loop2);
              //  while ($loop->have_posts()):
              //         $post_t=$loop->the_post();
              //     echo var_dump($post_t);
              // //     $productv = new WC_Product_Variation( $loop->post->ID );
              // //     $pro_sku=$productv->sku;
              // //     if($sku==$pro_sku){
              // //       echo "sku found in the variable product";
              // //     }
              //  endwhile; 
              
        }
        
      
        
  } else{
        /*----------------------------------------------------------------------------------------------*/
          $args = array();
          if(isset($_GET['product-type'])){

              if($_GET['product-type']=='variable'){
                $args['post_type'] = 'product';
                
                $args['tax_query'] = array(
                          array(
                            'taxonomy'  => 'product_type',
                            'terms'     => array('variable'),
                            'field'     => 'slug'
                          )
                        );
              }elseif($_GET['product-type']=='simple'){
               $args['post_type'] = 'product';
               $args['tax_query'] = array(
                          array(
                            'taxonomy'  => 'product_type',
                            'terms'     => array('simple'),
                            'field'     => 'slug'
                          )
                        );
              }else{
                $args['post_type'] = 'product';
              }

        }else{

              $args['post_type'] = 'product';

        }


          /**
           * Product category filter
           */         
          if(isset($_GET['product-category'])){
            if($_GET['product-category'] != 'all'){
            
            $category = $_GET['product-category'];
            
            $args['tax_query'] = array(
                                      array(
                                        'taxonomy'  => 'product_cat',
                                        'terms'     => $category,
                                        'field'     => 'term_id'
                                      )
                                );   
            }
          }
         


         if(isset($_GET['stock-status'])){
            if($_GET['stock-status']!='all'){
              $status = $_GET['stock-status'];
              //$meta_array[]=array('key'=>'_stock_status','value'=>$status);
              $args['meta_query']=array(array('key'=>'_stock_status','value'=>$status));
            }
         }
         
         if(isset($_GET['manage-stock'])){
            if($_GET['manage-stock']!='all'){
              $manage = $_GET['manage-stock'];
              //$meta_array[]=array('key'=>'_manage_stock','value'=>$manage);
              $args['meta_query']=array(array('key'=>'_manage_stock','value'=>$manage));
            } 
         }


          $args['posts_per_page'] = $this->limit;


          if(!empty($_GET['offset'])){
            $offset = $_GET['offset'] - 1;
            $offset = $offset * $this->limit;
            $args['offset'] = $offset;

          }
        
        
          $the_query = new WP_Query( $args );
          
          return $the_query->posts;
      }


  } 
  





  public function get_products_for_export(){
  
    $args = array();
    $args['post_type'] = 'product';
    $args['posts_per_page'] = -1;
    
    $the_query = new WP_Query( $args );
    
    return $the_query->posts;
  }   
  
  /**
   * Return pagination
   *
   */        
  public function pagination(){
     
     $all = count($this->get_products());
     $pages = ceil($all / 10);
     if(!empty($_GET['offset'])){
       $current = $_GET['offset'];
     }else{
       $current = 1;
     }
     
     $html = '';
     $html .= '<div class="stock-manager-pagination">';
     $query_string = $_SERVER['QUERY_STRING'];
     if($pages != 1){
     
      for ($i=1; $i <= $pages; $i++){
        if($current == $i){
            $html .= '<span class="btn btn-default">'.$i.'</span>';
        }else{
            $html .= '<a class="btn btn-primary" href="'.admin_url().'admin.php?'.$query_string.'&offset='.$i.'">'.$i.'</a>';
        }
      }
     
     }
     
     $html .= '</div>';
     
     return $html;
  }  
  
  /**
   * Save all meta data
   *
   */        
  public function save_all($data){
    foreach($data['product_id'] as $key => $item){
  
     $manage_stock = sanitize_text_field($data['manage_stock'][$item]);
     $stock_status = sanitize_text_field($data['stock_status'][$item]);
     $backorders   = sanitize_text_field($data['backorders'][$item]);
     $stock        = sanitize_text_field($data['stock'][$item]);
  
     update_post_meta($item, '_manage_stock', $manage_stock);
     update_post_meta($item, '_stock_status', $stock_status);
     update_post_meta($item, '_backorders', $backorders);
     update_post_meta($item, '_stock', $stock);
     
    }   
  }
  
  /**
   *
   * Get prduct categories 
   *
   */   
  public function products_categories($selected = null){
    $out = '';
    
    $terms = get_terms(
                      'product_cat', 
                      array(
                            'hide_empty' => 0, 
                            'orderby' => 'ASC'
                      )
    );
    if(count($terms) > 0)
    {
        foreach ($terms as $term)
        {
            if(!empty($selected) && $selected == $term->term_id){ $sel = 'selected="selected"'; }else{ $sel = ''; }
            $out .= '<option value="'.$term->term_id.'" '.$sel.'>'.$term->name.'</option>';
        }
        return $out;
    }
    return;
  }
  
 
/*
  ========================================================================================
    Get Stock Adjust History in Database
  ========================================================================================
*/

public function get_stock_adjust_history(){
            global $wpdb;
            // $querystr="
            //   SELECT Post_ID, sku, title, Adjust_Stock, After_Adjust_Stock, Update_By, Update_Date FROM fan_stockadjust

            //   BETWEEN ".$_GET['startday']." AND ".$_GET['endday']

            // ;
            $mysql_startday=strtotime($_GET['startday']);
            $mysql_endday=strtotime("+1 day",strtotime($_GET['endday']));

            if($_GET['startday']){
              $where_date=" AND "."(Update_Date <= FROM_UNIXTIME($mysql_endday) AND Update_Date >= FROM_UNIXTIME($mysql_startday) )";
            } else{
              $where_date=" AND "."(Update_Date <= FROM_UNIXTIME($mysql_endday))";
            }


            $sku=trim($_GET['sku']);
            $dateorder=$_GET['dateorder'];


            $querystr="
              SELECT Post_ID, sku, title, Adjust_Stock, After_Adjust_Stock,Inv_Location, Update_By, Update_Date FROM fan_stockadjust

              WHERE sku LIKE '".$sku."%'".$where_date." ORDER By Update_Date ".$dateorder;
            ;

            //echo $querystr;

            //$wpdb->query($wpdb->prepare($querystr,array($pid,$sku,$jobnum,$manage_stock,$stock_status,$backorders,$stock,$type,$parent_ID,$username)));
            $results=$wpdb->get_results($querystr);

            if($results){
                  return $results;
              
            }else{
                echo "<p style='color:red'>*No Results Return!</>";
            }

}

  
}//End class  