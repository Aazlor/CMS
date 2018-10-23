<?php
    
ini_set('upload_max_filesize', '10M');  
ini_set('post_max_size', '10M');  
ini_set('max_input_time', 300);  
ini_set('max_execution_time', 300);  
    
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

function countdim($array){
	if (is_array(reset($array))){
		$return = countdim(reset($array)) + 1;
	}	
	else{
		$return = 1;
	}
	return $return;
}

function resize($width, $height, $value){
	if($value['width'] != '' && $value['height'] != ''){
		$modwidth = $value['width'];	
		$modheight = $value['height'];	
	}
	elseif($value['width'] != '' && $value['height'] == ''){
		$modwidth = $value['width'];	
		$diff = $width / $modwidth;
		$modheight = $height / $diff;
	}
	elseif($value['height'] != '' && $value['width'] == ''){
		$modheight = $value['height'];	
		$diff = $height / $modheight;
		$modwidth = $width / $diff;
	}

	if(isset($value['minwidth']) && $value['minwidth'] != ''){
		$modwidth = $value['minwidth'];	
		$diff = $width / $modwidth;
		$modheight = $height / $diff;
	}
	if(isset($value['minheight']) && $value['minheight'] != '' && $modheight < $value['minheight']){
		$modheight = $value['minheight'];	
		$diff = $height / $modheight;
		$modwidth = $width / $diff;
	}
	
	if($modwidth == ''){
		$modwidth = $width;
	}
	if($modheight == ''){
		$modheight = $height;
	}

	if(isset($value['maxheight']) && $value['maxheight'] != '' && $value['maxheight'] < $modheight){
		$modheight = $value['maxheight'];	
		$diff = $height / $modheight;
		$modwidth = $width / $diff;
	}
	
	if(isset($value['maxwidth']) && $value['maxwidth'] != '' && $value['maxwidth'] < $modwidth){
		$modwidth = $value['maxwidth'];	
		$diff = $width / $modwidth;
		$modheight = $height / $diff;
	}
	
	return $modwidth.'||'.$modheight;
}


foreach($_POST as $key => $value){
	if(preg_match('/"/', $value) || preg_match("/'/", $value)){
		$value = stripslashes($value);
		$value = preg_replace("/\\\/", '', $value);
		$value = preg_replace("/'/", '&#39;', $value);
		#$value = preg_replace('/"/', '&#34;', $value);
		
		$_POST[$key] = $value;
	}
}

/******* SUBMIT EDIT *******/
if(isset($_POST['submit_edit']) && $_POST['submit_edit'] == 'yes'){
	include('pages/edit-submit.php');
}
/******* ADD GALLERY *******/
if(isset($_POST['submit_add_gallery']) && $_POST['submit_add_gallery'] == 'yes'){
	include('pages/gallery-add-submit.php');
}
/******* DELETE FROM GALLERY *******/
if(isset($_POST['submit_remove_gallery']) && $_POST['submit_remove_gallery'] == 'yes'){
	include('pages/gallery-delete-submit.php');
}

/******* SORT GALLERY *******/
if(isset($_POST['order_gallery']) && $_POST['order_gallery'] == yes){
	echo '<div class="Message"><img src="images/tick.gif"> '.$_POST[field_name].' images have been sorted.</div>';
}

/******* EDIT GALLERY *******/
if(isset($_POST['submit_edit_gallery']) && $_POST['submit_edit_gallery'] == 'yes'){
	include('pages/gallery-edit-submit.php');
}

#####
#####
#####
##### DISPLAY
#####
#####
#####
/******* MANAGE CONTENTS/EDIT PAGE *****/
if(isset($_GET['y']) && $_GET['y'] != ''){	
	include('pages/edit.php');
}

/******* EDIT GALLERY *****/
if(isset($_GET['x']) && $_GET['x'] == 'gallery_edit'){
	
	include('pages/gallery-edit-item.php');
}

include('footer.php');

?>
