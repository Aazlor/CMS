<?

function countdim($array){
	if (is_array(reset($array))){
		$return = countdim(reset($array)) + 1;
	}	
	else{
		$return = 1;
	}
	return $return;
}

$img_list = '';

$dir_path = $_SERVER['DOCUMENT_ROOT'].'/products/'.$_POST['product_id'];
require($dir_path.'/'.$_POST['field_name'].'.php');

$deletekey = $_POST['array_key'];
$i=0;
foreach($gallery_array as $key => $value){
	if($key != $deletekey && $value != ''){
		if(preg_match('/\.jpg/i', $value) || preg_match('/\.jpeg/i', $value) || preg_match('/\.gif/i', $value) || preg_match('/\.png/i', $value)){
			$img_list .= '[][]'.$value;
		}
		$keys[$i] = $value;
	}
	else{
		$delete_file = explode('||', $value);
	}
	$i++;
}

if(isset($delete_file)){
	foreach($delete_file as $value){
		if(preg_match('/\.jpg/i', $value) || preg_match('/\.jpeg/i', $value) || preg_match('/\.gif/i', $value) || preg_match('/\.png/i', $value)){
			$skip_delete = 'no';
			if(preg_match("/$value/", $img_list))
				$skip_delete = 'yes';
		}
	}
}

$writearray = '<?php  $gallery_array = array(';
if(isset($keys)){
	foreach($keys as $key => $value){
		$writearray .= "$key => '$value',";
	}
}
else{
	unset($gallery_array);
}
$writearray .= ');  ?>';

$file = fopen($dir_path.'/'.$_POST['field_name'].'.php', "w+");
fwrite($file, $writearray);
fclose($file);

if(isset($skip_delete) && $skip_delete == 'no'){

	$gallery_vars = 'Gallery__'.$_POST['field_name'];
	$gallery_vars = $$gallery_vars;

	$ii = -1;
	foreach($gallery_vars as $key => $value){
		if(is_array($value) && preg_match('/{image}/', $key)){
			if(countdim($value) > 1){
				foreach($value as $k => $v){
					$sizes[] = $k;
				}
			}
			break;		
		}
		$ii++;
	}


	if(!empty($sizes)){
		foreach($sizes as $v){
			if($v == 'medium' && file_exists($dir_path.'/'.$delete_file[$ii]) && $delete_file[$ii] != '')
					unlink($dir_path.'/'.$delete_file[$ii]);
			elseif(file_exists($dir_path.'/'.$v.'-'.$delete_file[$ii]) && $delete_file[$ii] != '')
				unlink($dir_path.'/'.$v.'-'.$delete_file[$ii]);
		}
	}
	elseif($delete_file[$ii] != ''){
		unlink($dir_path.'/'.$delete_file[$ii]);
	}
}

echo '<div class="Message"><img src="images/tick.gif"> Your '.$_POST['type'].' has been removed.</div>';

?>