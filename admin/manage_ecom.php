<?php
ini_set('upload_max_filesize', '10M');  
ini_set('post_max_size', '10M');  
ini_set('max_input_time', 300);  
ini_set('max_execution_time', 300);  

include('config.php');
    
include('header.php');

function clean_url($url){
	$url = strtolower($url);
	if(!preg_match('/http:\/\//', $url)){
		$url = preg_replace('/^\//', '', $url);
		$url = preg_replace('/\.html/', '', $url);
		$url = preg_replace('/ /', '-', $url);
		$url = preg_replace('/\//', '-', $url);
		$url = preg_replace('/\\\/', '-', $url);
		$url = preg_replace('/&/', 'and', $url);
		$url = '/'.$url.'.html';
	}
	return $url;
}

foreach($_POST as $key => $value){
	if($value != '' && !is_array($value)){
		if(preg_match('/"/', $value) || preg_match("/'/", $value)){
			$value = stripslashes($value);
			$value = preg_replace("/\\\/", '', $value);
			$value = preg_replace("/'/", '&#39;', $value);
			#$value = preg_replace('/"/', '&#34;', $value);
			
			$_POST[$key] = $value;
		}
	}
	if($key == 'Price'){
		$value = $_POST[$key];
		$value = str_replace('$', '', $value);
		$value = str_replace(' ', '', $value);
		$_POST[$key] = $value;
	}
}

$vars = $Product_Vars;

/******* SUBMIT Category Add *******/
if(isset($_POST['category_add']) && $_POST['category_add'] == 'yes'){
	include('ecom/category-add.php');
}
/******* ADD PRODUCT *******/
if(isset($_POST['submit_product']) && $_POST['submit_product'] == 'yes'){
	include('ecom/product-submit.php');
}
/** Product Gallery Submissions **/
if(isset($_POST['submit_add_gallery']) && $_POST['submit_add_gallery'] == 'yes'){
	include('ecom/products-gallery-add-submit.php');
}


/******* SORT PHOTOGALLERY *******/
if(isset($_POST['order_photogallery']) && $_POST['order_photogallery'] == yes){
	echo '<div class="Message"><img src="images/tick.gif"> '.$_POST['field_name'].' images have been sorted.</div>';
}


#####
#####
#####
##### DISPLAY
#####
#####
#####

if(isset($_REQUEST['section']) && $_REQUEST['section'] == 'categories'){
	include('ecom/categories.php');
}
if(isset($_REQUEST['section']) && $_REQUEST['section'] == 'products'){
	include('ecom/products-overview.php');
}
if(isset($_REQUEST['submit_edit_gallery']) && $_REQUEST['submit_edit_gallery'] == 'yes'){
	include('ecom/products-gallery-edit-submit.php');
}
if(isset($_REQUEST['submit_remove_gallery']) && $_REQUEST['submit_remove_gallery'] == 'yes'){
	include('ecom/products-gallery-delete-submit.php');
}

if(isset($_REQUEST['function']) && $_REQUEST['function'] == 'gallery_edit'){
	include('ecom/products-gallery-edit-item.php');
}
elseif(isset($_REQUEST['section']) && $_REQUEST['section'] == 'product'){
	include('ecom/product.php');
}
elseif(isset($_REQUEST['section']) && $_REQUEST['section'] == 'coupons'){
	include('ecom/coupons.php');
}
elseif(isset($_REQUEST['section']) && $_REQUEST['section'] == 'featuredproducts'){
	include('ecom/featured-products.php');
}
elseif(isset($_REQUEST['section']) && $_REQUEST['section'] == 'groupedproducts'){
	include('ecom/grouped-products.php');
}



include('footer.php');

?>