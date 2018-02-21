(function ( $ ) {
	"use strict";

	$(function () {

		/**
		 * Save single product line in stock table
		 *
		 */              
    jQuery('.save-product').on('click', function(){
       jQuery('.lineloader').css('display','block');
       var product = jQuery(this).data('product');
       var sku=jQuery('.sku_'+product).data('sku');
       var title=jQuery('.title_'+product).data('title');
       
       var manage_stock = jQuery('.manage_stock_' + product).val();
       var stock_status = jQuery('.stock_status_' + product).val();
       var backorders   = jQuery('.backorders_' + product).val();
       var stockadjust  = jQuery('.stockadjust_'+ product).val();
       var stock        = jQuery('.stock_' + product).val();

       var invlocation  = jQuery('.invlocation_' + product).val(); 

       // if (!jQuery('.stockadjust_'+ product).val()){
       //    stockadjust=0;
       //    stock=jQuery('.stock_' + product).val();
       // } else{
       //    stockadjust= parseInt(jQuery('.stockadjust_'+ product).val());
       //     if(!jQuery('.stock_' + product).val()){
       //          stock=stockadjust;
       //     } else{           
       //          stock=parseInt(jQuery('.stock_' + product).val())+ stockadjust;
       //     }

       // }

       
       var data = {
            action      : 'save_one_product',
            product     : product,
            sku         : sku,
            title       : title,
            manage_stock: manage_stock,
            stock_status: stock_status,
            backorders  : backorders,
            stock       : stock,
            stockadjust : stockadjust,
            invlocation : invlocation
       };
       
        jQuery.post(ajaxurl, data, function(response){
           
          jQuery('.lineloader').css('display','none'); 
          jQuery('.stock_' + product).val(parseInt(response));
          jQuery('.stockadjust_'+ product).val(null);

          if(response>0){
            jQuery('.stock_status_' + product).val('instock');
            jQuery('.stock_' + product).css('background-color','#43b610');

          }else{
            jQuery('.stock_status_' + product).val('outofstock');
            jQuery('.stock_' + product).css('background-color','#f80000');
          }
        });

        //location.reload();
       
    });
    
    
    /**
     * Show variations of selected product
     *
     */ 
    jQuery('.show-variable').on('click', function(){
       var variable = jQuery(this).data('variable');
       jQuery('.variation-item-' + variable).toggleClass('show-variations');
              
    });




    jQuery('#test').on('click', function(){


      var a=jQuery('#upload_product').val();
      alert(a);

      // var toupdateimage=jQuery('#updateimage').prop('checked');
      //   var data={
      //     action:'test_code',
      //     toupdateimage:toupdateimage,
      //     a:a
      //   };

      //   jQuery.post(ajaxurl,data,function(response){
      //        jQuery('#display').text(response);
      //   });
    });


    jQuery('#uploadsimple').on('click', function(){

        jQuery('#display_simple').text('');
        jQuery('#display_variable').text('');


        var filepath=jQuery("#simple_product_path").val();

        var path_ext=filepath.substring(filepath.lastIndexOf(".")+1)
        if(path_ext =='csv'){
          // Let's assign the url value to the input field
          var toupdateimage=jQuery('#updateimage_simple').prop('checked');

          //alert(filepath);

          var data={
            action:'import_simple_product',
            filepath:filepath,
            toupdateimage:toupdateimage
          };

          jQuery.post(ajaxurl,data,function(response){
               jQuery('#display_simple').text(response);
          });

        } else{
          alert('File type must be csv.');
          jQuery("#simple_product_path").val('');
        } 





    }); 

    jQuery('#uploadvariable').on('click', function(){
          jQuery('#display_simple').text('');
          jQuery('#display_variable').text('');

        var filepath=jQuery("#variable_product_path").val();

        var path_ext=filepath.substring(filepath.lastIndexOf(".")+1)
        if(path_ext =='csv'){
          // Let's assign the url value to the input field
          var toupdateimage=jQuery('#updateimage_variable').prop('checked');
          //alert(filepath);
         
          var data={
            action:'import_variable_product',
            filepath:filepath,
            toupdateimage:toupdateimage
          };

          jQuery.post(ajaxurl,data,function(response){
               jQuery('#display_variable').text(response);
          });

        } else{
          alert('File type must be csv.');
          jQuery("#variable_product_path").val('');
        } 
                   



    });



    $('#upload_file_simple').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload CSV',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            //console.log(uploaded_image);
            var image_url = uploaded_image.toJSON().url;

            var path_ext=image_url.substring(image_url.lastIndexOf(".")+1)
            if(path_ext=='csv'){
              $('#simple_product_path').val(image_url);
            } else{
              alert('File type must be csv.');
            }
        });
    });

   $('#upload_file_variable').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload CSV',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            //console.log(uploaded_image);
            var image_url = uploaded_image.toJSON().url;

            var path_ext=image_url.substring(image_url.lastIndexOf(".")+1)
            if(path_ext=='csv'){
              // Let's assign the url value to the input field
              $('#variable_product_path').val(image_url);
            } else{
              alert('File type must be csv.');
            }

        });
    });                     

	});


$(document).ajaxSend(function(event, request, settings) {
  $('#loading-indicator').show();
});

$(document).ajaxComplete(function(event, request, settings) {
  $('#loading-indicator').hide();
});

}(jQuery));