<?

include_once($_SERVER['DOCUMENT_ROOT'].'/admin/config.php');

$dir_path = $_SERVER['DOCUMENT_ROOT'].'/products/'.$_POST['product_id'];

$gallery_vars = 'Gallery__'.$_POST['field_name'];
$gallery_vars = $$gallery_vars;

include($_SERVER['DOCUMENT_ROOT'].'/admin/file_upload.php');

require($_SERVER['DOCUMENT_ROOT'].'/products/'.$_POST['product_id'].'/'.$_POST['field_name'].'.php');

$a=0;
foreach($gallery_vars as $key => $value){
	if(!preg_match('/{.*}/', $key)){
		$get_keys[$a] = $key;
		$a++;
	}
}

$i=0;
if(!empty($gallery_array)){
	foreach($gallery_array as $value){
		$keys[$i] = $value;
		$i++;
	}
}
$z = 0;
foreach($gallery_vars as $key => $value){
	if(preg_match('/{image}/i', $key)){
		$image_key_name = preg_replace('/{image}/i', '', $key);
		if(!isset($keys[$i]))
			$keys[$i] = '';
		$keys[$i] .= '||'.$record_images[$image_key_name];
	}
	elseif($value == '{file}'){
		$keys[$i] .= '||'.$record_images[$key];
	}
	elseif(preg_match('/{select}/i', $key)){
		$key = preg_replace('/{.*}/', '', $key);
		$content = preg_replace("/'/", '&#39;', $_POST[$key]);
		$content = preg_replace("/\"/", '&#34;', $content);
		$keys[$i] .= '||'.$content;	
	}
	else{
		if(isset($_POST[$key])){
			$content = preg_replace('/\'/', '&#39;', $_POST[$key]);
			$content = stripslashes($content);
			$keys[$i] .= '||'.$content;
		}
		elseif(isset($_FILES[$key])){
			$content = preg_replace('/\'/', '&#39;', $_FILES[$key]['name']);
			$content = stripslashes($content);
			$keys[$i] .= '||'.$content;
		}
	}
}
$keys[$i] = substr($keys[$i], 2);

$writearray = '<?php  $gallery_array = array(';
foreach($keys as $key => $value){
	$value = str_replace("'", "\'", $value);
	$writearray .= $key.' => \''.$value.'\',';
}	
$writearray .= ');  ?>';

$file = fopen($_SERVER['DOCUMENT_ROOT'].'/products/'.$_POST['product_id'].'/'.$_POST['field_name'].'.php', "w+");
fwrite($file, $writearray);
fclose($file);	

$show_field = preg_replace('/_/', ' ', $_POST['field_name']);

if(!isset($error)){
	echo '<div class="Message"><img src="images/tick.gif"> Your '.$show_field.' has been added.</div>';
}
else{
	foreach($error_code as $ev){
		$error_msg .= '<br>'.$ev;
	}
	echo '<div class="Message"><img src="images/cross.gif"> There was an error uploading your '.$_POST['field_name'].'.'.$error_msg.'</div>';
}

?>