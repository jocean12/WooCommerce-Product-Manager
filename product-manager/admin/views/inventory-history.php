<?php
/**
 * @package   WooCommerce Product Manager
 * @author    Vladislav Musílek
 * @license   GPL-2.0+
 * @link      http:/toret.cz
 * @copyright 2015 Toret.cz
 */
$stock = $this->stock();


?>


<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
  
  

  
<div class="t-col-12">
  <div class="toret-box box-info">
    <div class="box-header">
      <h3 class="box-title"><?php _e('Product Manager','stock-manager'); ?></h3>
    </div>
  <div class="box-body">
      <div class="stock-filter">
          
          <form method="get" action="">
            sku:  <input type="text" name="sku" placeholder="sku" value="<?php echo isset($_GET['sku'])?$_GET['sku']:'';?>"/>
            
            From: <input type="date" name="startday" value="<?php echo isset($_GET['startday'])?$_GET['startday']:''; ?>" max="<?php echo date('Y-m-d'); ?>">
            To:   <input type="date" name="endday" value="<?php echo isset($_GET['endday'])?$_GET['endday']:date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>">

          <select name="dateorder">
            <option value="DESC" <?php if(isset($_GET['orderby']) && $_GET['orderby'] == 'DESC'){ echo 'selected="selected"'; } ?>><?php _e('DESC','stock-manager'); ?></option>
            <option value="ASC" <?php if(isset($_GET['orderby']) && $_GET['orderby'] == 'ASC'){ echo 'selected="selected"'; } ?>><?php _e('ASC','stock-manager'); ?></option>
          </select>

            <input type="hidden" name="page" value="stock-manager-inventory-history" />
            <input type="submit" name="search" value="<?php _e('Search','stock-manager'); ?>" class="btn btn-info" />
            <a href="<?php echo admin_url().'admin.php?page=stock-manager-inventory-history'; ?>" class="btn btn-danger"><?php _e('Clear filter','stock-manager'); ?></a>
          </form>
      </div>  


      <div class="lineloader"></div>  
        <table class="table-bordered">
          <tr>
            <th><?php _e('Post ID','stock-manager'); ?></th>
            <th><?php _e('SKU','stock-manager'); ?></th>
            <th><?php _e('Title','stock-manager'); ?></th>
            <th><?php _e('Adjust Stock','stock-manager'); ?></th>
            <th><?php _e('Stock After Adjust','stock-manager'); ?></th>
            <th><?php _e('Bin Location','stock-manager'); ?></th>
            <th><?php _e('Update By','stock-manager'); ?></th>
            <th><?php _e('Update Date','stock-manager'); ?></th>
          </tr>
          <?php
          if(isset($_GET['search'])){
 
            $results=$stock->get_stock_adjust_history();
            if($results){
            foreach($results as $result){
              $post_id            = $result->Post_ID;
              $sku                = $result->sku;
              $title              = $result->title;
              $adjust_stock       = $result->Adjust_Stock;
              $after_adjust_stock = $result->After_Adjust_Stock;
              $invlocation        = $result->Inv_Location;
              $update_by          = $result->Update_By;
              $update_date        = $result->Update_Date;
            
          ?>

          <tr>
            <td class='td_center'><?php echo $post_id;?></td>
            <td class='td_center'><?php echo $sku;?></td>
            <td class='td_center'><?php echo $title;?></td>
            <td class='td_center'><?php echo $adjust_stock;?></td>
            <td class='td_center'><?php echo $after_adjust_stock;?></td>
            <td class='td_center'><?php echo $invlocation;?></td>
            <td class='td_center'><?php echo $update_by;?></td>
            <td class='td_center'><?php echo $update_date;?></td>
          </tr>

          <?php }}}?>
        </table>
      </div>



        <!--**************************************************************************************************************************************************************************-->
 
      <div class="clear"></div>
      
  </div>
</div>  
  

</div>
