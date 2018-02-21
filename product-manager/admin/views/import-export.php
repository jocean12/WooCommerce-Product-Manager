<?php
/**
 * @package   WooCommerce Product Manager
 * @author    Vladislav Musílek
 * @license   GPL-2.0+
 * @link      http:/toret.cz
 * @copyright 2015 Toret.cz
 */

$stock = $this->stock();
 
function stockautoUTF($s){
    if (preg_match('#[\x80-\x{1FF}\x{2000}-\x{3FFF}]#u', $s))
        return $s;

    if (preg_match('#[\x7F-\x9F\xBC]#', $s))
        return iconv('WINDOWS-1250', 'UTF-8', $s);

    return iconv('ISO-8859-2', 'UTF-8', $s);
}

wp_enqueue_media();

?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
  
	 <div>
      <h3>Instructions</h3>
      <ol>
      	<li><strong>Important please note: In order to upload variable products you must <em>first</em> upload the products as simple products.</strong></li>
        <li><a href="<?php echo plugins_url( 'Sample-CSV-Files/Sample-Products.csv', dirname(__FILE__) ) ;?>">Download the Sample CSV file</a> for Simple Products, and the <a href="<?php echo plugins_url( 'Sample-CSV-Files/Sample-Product-Variations.csv', dirname(__FILE__) ) ;?>">Sample CSV file</a> for Variable Products.</li>
        <li>‘post_name' must be unique to add a new item. If the post name matches any already uploaded it will update that product with the new information on the CSV file.</li>
        <li>‘product_image’ must be an active link or a local http:// link.  If developing locally use example http://yourdomainexample.dev/yourimagelocation/imagename.jpg</li>
        <li>Fill in all fields</li>
        <li>Save in CSV format</li>
        <li>Click 'Upload CSV File' browse for file on your computer and upload, select file.</li>
        <li>Click 'Upload Products'</li>
        <li>Please note that your server may limit the amount of images you upload at once, please check your server settings for file size limit uploads</li>
      </ol>
    </div>  



    <h3>Simple Product</h3>

    <div>
      <label for="simple_product_path">File Path</label>
      <input type="text" id='simple_product_path' name='simple_product_path' size="80%"> or 
      <input type="button" name="upload_btn" id="upload_file_simple" class="button-secondary" value="Upload CSV">
      <input type="checkbox" id="updateimage_simple" name="updateimage" checked>Uncheck to not include images.<br>
    </div>
    <div>
      <button type="button" id='uploadsimple' class="button-secondary">Upload Products</button>
      <p id='display_simple' style="color:green;"></p>  
      <a href="<?php echo plugins_url( 'Sample-CSV-Files/Sample-Products.csv', dirname(__FILE__) ) ;?>">Download Sample CSV file</a> 
    </div>


    <h3>Variable Product</h3>
    <p>Must upload parent product before uploading variable product.</p>
    <div>
      <label for="variable_product_path">File Path</label>
      <input type="text" id='variable_product_path' name='variable_product_path' size="80%"> or
      <input type="button" name="upload_btn" id="upload_file_variable" class="button-secondary" value="Upload CSV">
      <input type="checkbox" id="updateimage_variable" name="updateimage" checked>Uncheck to not include images.<br>
    </div>

    <div>
      <button type="button" id='uploadvariable' class="button-secondary">Upload Products</button>
      <p id='display_variable' style="color:green;"></p>   
      <a href="<?php echo plugins_url( 'Sample-CSV-Files/Sample-Product-Variations.csv', dirname(__FILE__) ) ;?>">Download Sample CSV file</a>
    </div>


    <div>

      <img src="<?php $dir = plugins_url().'\product-manager\admin\views\image\default.gif'; echo $dir; ?>" id="loading-indicator" alter="Indicator" width='60px' height='60px' style='display:none;position:absolute;top:50%;left:50%;'/>
    </div>
</div>


</div>
