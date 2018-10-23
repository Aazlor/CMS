<?

$dir_path = $_SERVER['DOCUMENT_ROOT'].'/products/'.$_POST['product_id'];

$gallery_vars = 'Gallery__'.$_POST['field_name'];
$gallery_vars = $$gallery_vars;

require($dir_path.'/'.$_POST['field_name'].'.php');

foreach($_POST as $key => $value){
	if(preg_match('/^Current{}/', $key)){
		$key = preg_replace('/^Current{}/', '', $key);
		$imageSizes = $key.'{image}';
		if(!empty($gallery_vars[$imageSizes])){
			foreach($gallery_vars[$imageSizes] as $k => $v){
				$sizes[] = $k;
			}
		}

		if(isset($_POST['Remove{}'.$key]) && $_POST['Remove{}'.$key] == 'yes' || $_FILES[$key]['name'] != ''){

			foreach($gallery_array as $k => $v){
				if($k != $_POST['array_key'] && preg_match("/$value/", $v)){

					$skip_delete = 1;
				}
			}

			if(isset($skip_delete) && $skip_delete != 1){
				$record_images[$key] = '';
				if(!empty($sizes)){
					foreach($sizes as $v){
						if($v == 'medium'){
							if(file_exists($dir_path.'/'.$value))
								unlink($dir_path.'/'.$value);
						}
						elseif(file_exists($dir_path.'/'.$v.'-'.$value)){
							unlink($dir_path.'/'.$v.'-'.$value);
						}
					}
				}
				else{
					unlink($dir_path.'/'.$value);				
				}
			}

		}
		else{
			$record_images[$key] = $value;
		}

	}
}
		
include($_SERVER['DOCUMENT_ROOT'].'/admin/file_upload.php');

$a=0;
foreach($gallery_vars as $key => $value){
	if(!preg_match('/{.*}/', $key)){
		$get_keys[$a] = $key;
		$a++;
	}
}

$i=0;
foreach($gallery_array as $value){
	unset($find);
	if($i == $_POST['array_key']){
		$keys[$i] = '';
		foreach($gallery_vars as $key => $value){
			if(preg_match('/{image}/i', $key)){
				$image_key_name = preg_replace('/{image}/i', '', $key);
				if($record_images[$image_key_name] == '' && !$_POST['Remove{}'.$image_key_name]){
					$record_images[$image_key_name]	= $_POST['Current{}'.$image_key_name];
				}
				$keys[$i] .= '||'.$record_images[$image_key_name];
			}
			elseif($value == '{file}'){
				if($record_images[$key] == '' && !$_POST['Remove{}'.$key]){
					$record_images[$key] = $_POST['Current{}'.$key];
				}
				$keys[$i] .= '||'.$record_images[$key];
			}
			elseif(preg_match('/^Type/i', $key)){
				
			}
			elseif(preg_match('/{select}/i', $key)){
				$key = preg_replace('/{.*}/', '', $key);
				$content = preg_replace("/'/", '&#39;', $_POST[$key]);
				$content = preg_replace("/\"/", '&#34;', $content);
				$keys[$i] .= '||'.$content;					
			}
			else{
				$content = preg_replace("/'/", '&#39;', $_POST[$key]);
				$keys[$i] .= '||'.$content;
			}
		}
		$keys[$i] = substr($keys[$i], 2);
		$find = 'yes';
	}
	if(!isset($find)){
		$keys[$i] = $value;
	}
	if(isset($find)){
		$add_new = 'no';
	}
	$i++;
}

$writearray = '<?php  $gallery_array = array(';
foreach($keys as $key => $value){
	$value = str_replace("'", "\'", $value);
	$writearray .= $key.' => \''.$value.'\',';
}	
$writearray .= ');  ?>';

$file = fopen($dir_path.'/'.$_POST['field_name'].'.php', "w+");
fwrite($file, $writearray);
fclose($file);	

echo '<div class="Message"><img src="images/tick.gif"> '.$gallery_vars['Type'].' updated.</div>';


?>