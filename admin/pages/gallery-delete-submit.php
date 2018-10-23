<?
$dir_path = $_SERVER['DOCUMENT_ROOT'].'/gallery/'.$_POST['field_name'];

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

require($dir_path.'/images.php');

$deletekey = $_POST['array_key'];
$i=0;
foreach($gallery_array as $key => $value){
	if($key != $deletekey && $value != ''){
		$keys[$i] = $value;
	}
	else{
		$delete_file = explode('||', $value);
	}
	$i++;
}		

$writearray = '<?php  $gallery_array = array(';
if(isset($keys)){
	foreach($keys as $key => $value){
		$value = str_replace("'", "\'", $value);
		$writearray .= "$key => '$value',";
	}
}
$writearray .= '); ?>';

$file = fopen($dir_path.'/images.php', "w+");
fwrite($file, $writearray);
fclose($file);

if(isset($delete_file[$ii]) && $delete_file[$ii] != ''){
	if(!empty($sizes)){
		foreach($sizes as $v){
			if($v == 'medium'){
				if(file_exists($dir_path.'/'.$delete_file[$ii])){
					unlink($dir_path.'/'.$delete_file[$ii]);
				}
			}
			else{
				if(file_exists($dir_path.'/'.$v.'-'.$delete_file[$ii])){
					unlink($dir_path.'/'.$v.'-'.$delete_file[$ii]);
				}
			}
		}
	}
	elseif(file_exists($dir_path.'/'.$delete_file[$ii])){
		unlink($dir_path.'/'.$delete_file[$ii]);
	}
}

echo '<div class="Message"><img src="images/tick.gif"> Your '.$_POST['type'].' has been removed.</div>';

?>