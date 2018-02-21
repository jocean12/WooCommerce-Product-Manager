# WooCommerce-Product-Manager
WooCommerce Product Manager for uploading many products at once using CSV file

=== Product Manager ===
Contributors: Zhefan Xu & Joe Sheehan
Tags: WooCommerce, Product Manager
Requires at least: 3.5.1
Tested up to: 4.6.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

This WooCommerce Product Manager Plugin allows you to upload products in bulk to your WooCommerce storefront via CSV file format. Easily add hundreds of products to your store in seconds. Works with Simple Products as well as Variable products with the premium version. You can easily update quantities on products using the Stock Adjustment feature. 


Plugin is compatible with WooCommerce 2.1+ and is tested on 2.6.4 version. 
 

== Product Upload Instructions ==

Important please note: In order to upload variable products you must first upload the products as simple products.

1. Download the Sample CSV file for Simple Products, and the Sample CSV file for Variable Products.
2. ‘post_name' must be unique to add a new item. If the post name matches any already uploaded it will update that product with the new information on the CSV file.
3. ‘product_image’ must be an active link or a local http:// link.  If developing locally use example http://yourdomainexample.dev/yourimagelocation/imagename.jpg
4. Fill in all fields
5. Save in CSV format
6. Click 'Upload CSV File' browse for file on your computer and upload, select file.
7. Click 'Upload Products'
8. Please note that your server may limit the amount of images you upload at once, please check your server settings for file size limit uploads



== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'Product Manager'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `product-manager-lite.zip` or 'product-manager.zip' for premium users from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `product-manager-lite.zip` or `product-manager.zip`
2. Extract the directory to your computer
3. Upload the directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

== Changelog ==

= 1.0.0 =
Startup/stable version
